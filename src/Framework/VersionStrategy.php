<?php

namespace App\Framework;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class VersionStrategy implements VersionStrategyInterface
{
    /** @var string|null */
    private $version;
    //  Nastaví cestu ke konfiguračnímu souboru s verzí assetů.
    public function __construct(string $assetBaseDir, string $versionLinkWildCard)
    {
        $this->version = $this->detectVersion($assetBaseDir, $versionLinkWildCard);
    }
    // Vrátí aktuální verzi assetů pro cache busting.
    public function getVersion(string $path): string
    {
        return $this->version;
    }
    //  Připojí verzi k URL assetu.
    public function applyVersion(string $path): string
    {
        if ($this->version !== null) {
            return sprintf("%s/%s", $this->version, $path);
        } else {
            return $path;
        }
    }
    //  Zkusí zjistit verzi z konfiguračního souboru nebo z času změny.
    private function detectVersion(string $assetBaseDir, string $versionWildCard): ?string
    {
        $cwd = getcwd();
        chdir($assetBaseDir);
        $versions = [];
        foreach (glob($versionWildCard) as $version) {
            if (is_dir($version)) {
                $versions[] = $version;
            }
        }
        chdir($cwd);

        if (empty($versions)) {
            return null;
        }

        sort($versions);

        return array_pop($versions);
    }
}
