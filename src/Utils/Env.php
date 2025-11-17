<?php

namespace App\Utils;

use Exception;

class Env
{
    public function loadEnv($filePath)
    {
        if (!file_exists($filePath)) throw new Exception("Arquivo .env não encontrado.");
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;

                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = str_replace(['"', "'"], '', $value);
                putenv("{$key}={$value}");
            }
        }
    }
}
