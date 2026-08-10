<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Hooks;

use TRAW\AccessRules\Tca\RegisterRules;

class IconOverlay
{
    public function __construct(private readonly \TRAW\AccessRules\Tca\IconOverlay $iconOverlay)
    {

    }

    public function postOverlayPriorityLookup(string $table, array $row, array $status, string $iconName)
    {
        return $this->iconOverlay->getIconOverlay($table, $row, $status, $iconName);
    }
}
