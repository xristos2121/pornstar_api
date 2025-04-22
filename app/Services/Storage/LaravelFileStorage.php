<?php

namespace App\Services\Storage;

use App\Contracts\Services\Storage\FileStorageInterface;
use Illuminate\Support\Facades\Storage;

class LaravelFileStorage implements FileStorageInterface
{
    /**
     * @var string
     */
    private $disk;

    public function __construct(string $disk = 'local')
    {
        $this->disk = $disk;
    }

    public function put(string $path, string $contents): bool
    {
        return Storage::disk($this->disk)->put($path, $contents);
    }

    public function path(string $path): string
    {
        return Storage::disk($this->disk)->path($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }
}
