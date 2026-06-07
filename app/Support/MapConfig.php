<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class MapConfig
{
    private static ?object $dbSettings = null;

    public static function clearCache(): void
    {
        self::$dbSettings = null;
    }

    public static function dbSettings(): ?object
    {
        if (self::$dbSettings !== null) {
            return self::$dbSettings;
        }

        if (! Schema::hasTable('setting')) {
            return null;
        }

        self::$dbSettings = \DB::table('setting')->where('status', 'active')->first()
            ?: \DB::table('setting')->first();

        return self::$dbSettings;
    }

    public static function topologyVersion(): string
    {
        $default = (string) config('maps.topology_version', '2.3.3');
        $db = self::dbSettings();

        if ($db && Schema::hasColumn('setting', 'map_topology_version')) {
            $stored = trim((string) ($db->map_topology_version ?? ''));
            if ($stored !== '') {
                return $stored;
            }
        }

        return $default;
    }

    public static function latestKnownVersion(): ?string
    {
        $db = self::dbSettings();
        if ($db && Schema::hasColumn('setting', 'map_topology_latest_version')) {
            $stored = trim((string) ($db->map_topology_latest_version ?? ''));
            if ($stored !== '') {
                return $stored;
            }
        }

        return null;
    }

    public static function lastCheckedAt(): ?string
    {
        $db = self::dbSettings();
        if ($db && Schema::hasColumn('setting', 'map_topology_version_checked_at')) {
            return $db->map_topology_version_checked_at ?? null;
        }

        return null;
    }

    public static function defaultVersionId(): string
    {
        return 'africa-sadr-topo-'.self::topologyVersion();
    }

    public static function applyRuntimeConfig(): void
    {
        $version = self::topologyVersion();
        $baseUrl = str_replace('{version}', $version, (string) config('maps.topology_base_url', 'https://code.highcharts.com/mapdata/{version}/'));

        config([
            'maps.topology_version' => $version,
            'maps.default_version_id' => self::defaultVersionId(),
            'maps.africa_topology_version' => $version,
            'maps.africa_topology_url' => rtrim($baseUrl, '/').'/custom/africa-sadr.topo.json',
            'maps.admin_units.version' => $version,
            'maps.admin_units.topology_url_template' => rtrim($baseUrl, '/').'/countries/{iso2}/{iso2}-all.topo.json',
        ]);
    }
}
