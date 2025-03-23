<?php

declare(strict_types=1);

namespace App\Exceptions\RecipeImport;

use Exception;

class NoStructuredDataException extends Exception
{
    public function __construct(string $url)
    {
        parent::__construct("No recipe data could be found on the page: {$url}");
    }
}
