<?php

declare(strict_types=1);

namespace App\Exceptions\RecipeImport;

use Exception;

class ConnectionFailedException extends Exception
{
    public function __construct(string $url, string $error = '')
    {
        $message = "Failed to connect to {$url}";
        if ($error !== '' && $error !== '0') {
            $message .= ": {$error}";
        }
        parent::__construct($message);
    }
}
