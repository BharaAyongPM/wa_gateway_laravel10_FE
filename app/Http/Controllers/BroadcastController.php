<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
public function index()
{
    $broadcasts = Broadcast::latest()->get();
    $devices = Device::all(); // ⬅️ ambil semua device
    return view('broadcasts.index', compact('broadcasts', 'devices'));
}

public function store(Request $request)
{
    $request->validate([
        'message' => 'required|string',
        'send_time' => 'required',
        'groups' => 'required|array',
        'device' => 'required|string',
    ]);

    Broadcast::create($request->all());

    return back()->with('success', 'Broadcast berhasil ditambahkan');
}

public function toggle(Request $request, $id)
{
    $broadcast = Broadcast::findOrFail($id);
    $broadcast->update(['active' => !$broadcast->active]);

    return back()->with('success', 'Status broadcast diperbarui');
}

// Untuk Node.js polling
public function checkForBroadcasts()
{
    $now = now()->format('H:i');

    return Broadcast::where('active', true)
        ->where('send_time', $now)
        ->get(['message', 'groups', 'device']);
}
public function api()
{
    $now = Carbon::now()->format('H:i');
    $broadcasts = Broadcast::where('active', true)
        ->where('send_time', $now)
        ->get();

    return response()->json($broadcasts);
}
public function destroy($id)
{
    $broadcast = Broadcast::findOrFail($id);
    $broadcast->delete();

    return back()->with('success', 'Broadcast berhasil dihapus.');
}

}
