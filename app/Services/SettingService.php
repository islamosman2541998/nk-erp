<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function all(): array
    {
        return Cache::rememberForever('system_settings', function () {
            return Setting::query()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function fileUrl(string $key, ?string $default = null): ?string
    {
        $path = $this->get($key);

        if (! $path) {
            return $default;
        }

        return asset('storage/' . $path);
    }

    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->clearCache();
    }

    public function deleteFileIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function clearCache(): void
    {
        Cache::forget('system_settings');
    }
}