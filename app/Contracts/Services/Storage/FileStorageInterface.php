<?php

namespace App\Contracts\Services\Storage;

interface FileStorageInterface
{
    /**
     * Store a file at the given path.
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function put(string $path, string $contents): bool;
    
    /**
     * Get the full path to a file.
     *
     * @param string $path
     * @return string
     */
    public function path(string $path): string;
    
    /**
     * Check if a file exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;
    
    /**
     * Delete a file.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;
}
