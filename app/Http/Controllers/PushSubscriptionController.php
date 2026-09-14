<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PushSubscriptionController extends Controller
{
    /**
     * Simpan atau perbarui push subscription user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $contentEncoding = $request->input('encoding', 'aesgcm');

        $request->user()->updatePushSubscription(
            $endpoint,
            $key,
            $token,
            $contentEncoding
        );

        return response()->json([
            'success' => true,
            'message' => 'Langganan notifikasi push berhasil disimpan.'
        ]);
    }

    /**
     * Hapus push subscription user.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $request->user()->deletePushSubscription($request->endpoint);

        return response()->json([
            'success' => true,
            'message' => 'Langganan notifikasi push berhasil dihapus.'
        ]);
    }
}
