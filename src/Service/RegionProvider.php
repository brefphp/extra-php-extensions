<?php declare(strict_types=1);

namespace Bref\Extra\Service;

/**
 * Get all available bref regions.
 */
class RegionProvider
{
    public function getAll(): array
    {
        if (getenv('only_region')) {
            return [getenv('only_region')];
        }

        return json_decode(file_get_contents('https://raw.githubusercontent.com/brefphp/bref/master/utils/layers.json/regions.json'), true);
    }
}
