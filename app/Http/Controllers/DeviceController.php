<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('device.index', compact('devices'));
    }

    public function create()
    {
        // Generate session ID
        $sessionId = 'device-' . Str::random(8);

        // Minta ke server Node.js buat sesi baru
        $response = Http::post('http://localhost:5000/device/create', [
            'session_id' => $sessionId
        ]);

        if ($response->successful()) {
            Device::create([
                'name' => 'Device ' . strtoupper(Str::random(3)),
                'session_id' => $sessionId,
                'status' => 'pending',
                'api_key' => 'ziezie_wa_' . Str::random(40),

            ]);
        }

        return redirect()->route('device.index');
    }

    public function showQr($id)
    {
        $device = Device::findOrFail($id);
        $response = Http::get("http://localhost:5000/device/{$device->session_id}/qrcode");

        if ($response->successful()) {
            $qr = $response->json()['qr'];
            $device->update(['qr_code' => $qr]);
        }

        return view('device.qr', compact('device'));
    }

    public function checkStatus($id)
    {
        $device = Device::findOrFail($id);

        $response = Http::get("http://localhost:5000/device/{$device->session_id}/status");
        Log::info('Memanggil endpoint status untuk session: ' . $device->session_id);
        if ($response->successful() && isset($response['status'])) {
            $device->update([
                'status' => $response['status'],
                'last_connected_at' => $response['status'] === 'connected' ? now() : null,
            ]);
        }

        return back();
    }


    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        Http::delete("http://localhost:5000/device/{$device->session_id}");
        $device->delete();

        return back();
    }
    public function liveQr($id)
    {
        $device = Device::findOrFail($id);
        $response = Http::get("http://localhost:5000/device/{$device->session_id}/qrcode-live");

        if ($response->successful()) {
            return response()->json(['qr' => $response->json()['qr']]);
        }

        return response()->json(['qr' => null]);
    }
    public function log()
    {
        $devices = Device::all();
        return view('log.index', compact('devices'));
    }
    public function generateApiKey($id)
    {
        $device = Device::findOrFail($id);

        if (!$device->api_key) {
            $device->api_key = 'ziezie_wa_' . Str::random(32);
            $device->save();
        }

        return response()->json(['api_key' => $device->api_key]);
    }
}
