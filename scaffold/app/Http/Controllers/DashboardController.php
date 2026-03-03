<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    private string $statsFile = 'dashboard_stats.json';

    public function index(): \Illuminate\View\View
    {
        return view('dashboard');
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->readStats());
    }

    public function trackVisit(Request $request): JsonResponse
    {
        $stats = $this->readStats();

        $stats['visits']++;
        $stats['last_action'] = $request->input('action', 'page-visit');
        $stats['updated_at'] = now()->toDateTimeString();

        Storage::disk('local')->put($this->statsFile, json_encode($stats));

        return response()->json($stats);
    }

    private function readStats(): array
    {
        if (! Storage::disk('local')->exists($this->statsFile)) {
            $initial = [
                'visits' => 0,
                'last_action' => 'none',
                'updated_at' => now()->toDateTimeString(),
            ];

            Storage::disk('local')->put($this->statsFile, json_encode($initial));

            return $initial;
        }

        return json_decode(Storage::disk('local')->get($this->statsFile), true);
    }
}
