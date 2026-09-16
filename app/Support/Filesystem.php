<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem as BaseFilesystem;

class Filesystem extends BaseFilesystem
{
    /**
     * Replace the given file with the given content atomically.
     * Includes retry loop and direct-write fallback resilience for Windows environments
     * to prevent "Acceso denegado (code: 5)" caused by temporary file locks.
     *
     * @param  string  $path
     * @param  string  $content
     * @param  int|null  $mode
     * @return void
     */
    public function replace($path, $content, $mode = null)
    {
        // If the path already exists and is a symlink, get the real path...
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;

        $tempPath = tempnam(dirname($path), basename($path));

        // Fix permissions of tempPath because `tempnam()` creates it with permissions set to 0600...
        if (! is_null($mode)) {
            @chmod($tempPath, $mode);
        } else {
            @chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        if (DIRECTORY_SEPARATOR === '\\') {
            $attempts = 0;
            while ($attempts < 5) {
                if (@rename($tempPath, $path)) {
                    return;
                }

                if (@copy($tempPath, $path)) {
                    @unlink($tempPath);
                    return;
                }

                @unlink($path);

                if (@rename($tempPath, $path)) {
                    return;
                }

                if (@file_put_contents($path, $content) !== false) {
                    @unlink($tempPath);
                    return;
                }

                $attempts++;
                usleep(50000); // 50ms wait for Windows locks/antivirus to release
            }

            if (@file_put_contents($path, $content) !== false) {
                @unlink($tempPath);
                return;
            }

            rename($tempPath, $path);
        } else {
            rename($tempPath, $path);
        }
    }
}
