<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GeneratePwaIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icons:generate {--color=#1E3A5F} {--text=AB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PWA icons in multiple sizes with customizable color and text';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
        $color = $this->option('color');
        $text = $this->option('text');
        $path = public_path('icons');

        // Validasi warna
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $this->error("❌ Format warna tidak valid. Gunakan format: #RRGGBB (contoh: #1E3A5F)");
            return 1;
        }

        // Pastikan folder ada
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
            $this->info("📁 Folder icons dibuat: $path");
        }

        $this->info("🎨 Generating PWA icons...");
        $this->info("   Color: $color | Text: $text");
        $this->newLine();

        $progressBar = $this->output->createProgressBar(count($sizes));
        $progressBar->start();

        foreach ($sizes as $size) {
            try {
                // Buat canvas dengan ukuran dan warna
                $manager = new ImageManager(new Driver());
                $img = $manager->create($size, $size)->fill($color);

                // Tambah teks di tengah
                $img->text($text, $size / 2, $size / 2, function ($font) use ($size) {
                    $font->size($size / 4);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('middle');
                    $font->file(public_path('fonts/Arial.ttf')); // Optional: custom font
                });

                // Simpan icon
                $filename = "icon-{$size}x{$size}.png";
                $img->save("$path/$filename");

                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("\n❌ Error generating icon-{$size}x{$size}.png: " . $e->getMessage());
                return 1;
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->newLine();

        // Tampilkan info generated icons
        $this->info("✅ All PWA icons generated successfully!");
        $this->info("📂 Location: $path");
        $this->newLine();

        $this->table(
            ['Size', 'Filename', 'Purpose'],
            [
                ['72x72', 'icon-72x72.png', 'Android device icon'],
                ['96x96', 'icon-96x96.png', 'Android device icon'],
                ['128x128', 'icon-128x128.png', 'Tablet icon'],
                ['144x144', 'icon-144x144.png', 'Tablet icon'],
                ['152x152', 'icon-152x152.png', 'iPad icon'],
                ['192x192', 'icon-192x192.png', 'Android home screen'],
                ['384x384', 'icon-384x384.png', 'App drawer icon'],
                ['512x512', 'icon-512x512.png', 'Splash screen & app store'],
            ]
        );

        $this->newLine();
        $this->info("💡 Tip: Update manifest.json dengan icons ini");
        $this->line("   Lihat file manifest-config.json untuk contoh konfigurasi");

        return 0;
    }
}