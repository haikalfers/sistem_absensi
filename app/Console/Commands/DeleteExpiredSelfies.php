<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteExpiredSelfies extends Command
{
    protected $signature   = 'selfies:delete-expired';
    protected $description = 'Hapus foto selfie absensi yang sudah melewati masa simpan 7 hari';

    public function handle(): int
    {
        $this->info('🗑️  Memulai penghapusan foto selfie yang sudah kadaluarsa...');

        $expired = Attendance::whereNotNull('selfie_photo')
            ->whereNotNull('selfie_expires_at')
            ->where('selfie_expires_at', '<=', Carbon::now())
            ->get();

        $deleted = 0;
        $failed  = 0;

        foreach ($expired as $attendance) {
            try {
                // Hapus file dari storage
                if (Storage::disk('public')->exists($attendance->selfie_photo)) {
                    Storage::disk('public')->delete($attendance->selfie_photo);
                }

                // Hapus referensi path dari DB (data absensi tetap ada)
                $attendance->update([
                    'selfie_photo'      => null,
                    'selfie_expires_at' => null,
                ]);

                $deleted++;
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Failed to delete expired selfie', [
                    'attendance_id' => $attendance->id,
                    'selfie_photo'  => $attendance->selfie_photo,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        // Hapus selfie kadaluarsa pada pengajuan presensi ulang (AttendanceRevision)
        $expiredRevisions = \App\Models\AttendanceRevision::whereNotNull('selfie_photo')
            ->whereNotNull('selfie_expires_at')
            ->where('selfie_expires_at', '<=', Carbon::now())
            ->get();

        foreach ($expiredRevisions as $revision) {
            try {
                if (Storage::disk('public')->exists($revision->selfie_photo)) {
                    Storage::disk('public')->delete($revision->selfie_photo);
                }

                $revision->update([
                    'selfie_photo'      => null,
                    'selfie_expires_at' => null,
                ]);

                $deleted++;
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Failed to delete expired revision selfie', [
                    'revision_id'  => $revision->id,
                    'selfie_photo' => $revision->selfie_photo,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $this->info("✅  Selesai: {$deleted} foto dihapus, {$failed} gagal.");

        Log::info('DeleteExpiredSelfies Command Ran', [
            'deleted' => $deleted,
            'failed'  => $failed,
            'ran_at'  => Carbon::now()->toIso8601String(),
        ]);

        // Hapus juga direktori selfies kosong yang sudah lama
        $this->cleanupEmptyDirectories();

        return self::SUCCESS;
    }

    /**
     * Bersihkan direktori selfies/Y/m/d yang sudah kosong
     */
    private function cleanupEmptyDirectories(): void
    {
        $basePath = storage_path('app/public/selfies');
        if (!is_dir($basePath)) return;

        // Hapus direktori hari-hari lama yang kosong
        $cutoff = Carbon::now()->subDays(8);
        $years  = glob($basePath . '/*', GLOB_ONLYDIR);

        foreach ($years as $yearDir) {
            foreach (glob($yearDir . '/*', GLOB_ONLYDIR) as $monthDir) {
                foreach (glob($monthDir . '/*', GLOB_ONLYDIR) as $dayDir) {
                    $dayName = basename($dayDir);
                    $monthName = basename($monthDir);
                    $yearName  = basename($yearDir);
                    $dirDate = Carbon::createFromFormat('Y/m/d', "{$yearName}/{$monthName}/{$dayName}");

                    if ($dirDate < $cutoff && count(glob($dayDir . '/*')) === 0) {
                        rmdir($dayDir);
                    }
                }
            }
        }
    }
}
