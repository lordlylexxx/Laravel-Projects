<?php

namespace App\Support;

use App\Models\AppRelease;
use Illuminate\Support\Collection;

final class SemanticVersion
{
    public static function normalize(string $tag): string
    {
        return ltrim(trim($tag), "vV \t\n\r\0\x0B");
    }

    /**
     * @param  Collection<int, AppRelease>|array<int, AppRelease>  $releases
     */
    public static function newestRelease(Collection|array $releases): ?AppRelease
    {
        $list = $releases instanceof Collection ? $releases->all() : $releases;

        if ($list === []) {
            return null;
        }

        usort($list, function (AppRelease $a, AppRelease $b): int {
            return -version_compare(
                self::normalize((string) $a->tag),
                self::normalize((string) $b->tag)
            );
        });

        return $list[0] ?? null;
    }
}
