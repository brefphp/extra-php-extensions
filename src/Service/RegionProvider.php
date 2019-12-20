<?php declare(strict_types=1);

namespace Bref\Extra\Service;

/**
 * Get all available bref regions.
 */
class RegionProvider
{
    public function getAll(): array
    {
        return json_decode(file_get_contents('https://raw.githubusercontent.com/brefphp/bref/master/runtime/layers/regions.json'), true);
    }
}
