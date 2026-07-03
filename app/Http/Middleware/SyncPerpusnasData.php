<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SyncPerpusnasData
{
    protected array $endpoints = [
        'iku3513'         => 'https://transformasi.perpusnas.go.id/api/iku3513',
        'peningkatan3513' => 'https://transformasi.perpusnas.go.id/api/peningkatan3513',
        'pelibatan3513'   => 'https://transformasi.perpusnas.go.id/api/pelibatan3513',
        'publikasi3513'   => 'https://transformasi.perpusnas.go.id/api/publikasi3513',
        'advokasi3513'    => 'https://transformasi.perpusnas.go.id/api/advokasi3513',
        'replikasi3513'   => 'https://transformasi.perpusnas.go.id/api/replikasi3513',
        'pengunjung3513'  => 'https://transformasi.perpusnas.go.id/api/pengunjung3513',
    ];

    protected string $cacheKey = 'perpusnas_last_sync';
    protected int $throttleMinutes = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get')) {
            $this->syncIfNeeded();
        }

        return $next($request);
    }

    protected function syncIfNeeded(): void
    {
        if ($this->throttleMinutes > 0 && Cache::has($this->cacheKey)) {
            return;
        }

        foreach ($this->endpoints as $name => $url) {
            $this->fetchAndStore($name, $url);
        }

        if ($this->throttleMinutes > 0) {
            Cache::put($this->cacheKey, now(), now()->addMinutes($this->throttleMinutes));
        }
    }


    protected function fetchAndStore(string $name, string $url): void
    {
        try {
            $response = Http::withHeaders([
                'Accept'     => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
            ])
                ->timeout(15)
                ->retry(1, 200)
                ->get($url);

            if ($response->successful()) {
                $dir = public_path('json');

                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
                $decoded = json_decode($response->body());

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning("Perpusnas sync skip [{$name}]: invalid JSON response");
                    return;
                }

                if ($name === 'iku3513' && isset($decoded->data) && is_array($decoded->data)) {
                    $decoded->data = $this->dedupePerpustakaan($decoded->data);
                }

                $pretty = json_encode(
                    $decoded,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );

                File::put("{$dir}/{$name}.json", $pretty);
            } else {
                Log::warning("Perpusnas sync skip [{$name}]: HTTP {$response->status()}");
            }
        } catch (\Throwable $e) {
            Log::warning("Perpusnas sync failed [{$name}]: " . $e->getMessage());
        }
    }

    protected function dedupePerpustakaan(array $data): array
    {
        $grouped = [];

        foreach ($data as $item) {
            $key = strtolower(trim($item->nama_perpustakaan ?? '')) . '|' .
                strtolower(trim($item->desa_kelurahan ?? ''));

            if (!isset($grouped[$key])) {
                $grouped[$key] = $item;
                continue;
            }

            $existingSkor = $grouped[$key]->skor ?? '-';
            $newSkor      = $item->skor ?? '-';

            $existingValid = $existingSkor !== '-' && $existingSkor !== null && $existingSkor !== '';
            $newValid      = $newSkor !== '-' && $newSkor !== null && $newSkor !== '';

            if (!$existingValid && $newValid) {
                $grouped[$key] = $item;
            }
        }

        return array_values($grouped);
    }
}
