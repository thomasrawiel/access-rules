<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Tca;

use TYPO3\CMS\Core\Database\ConnectionPool;

class IconOverlay
{
    public function __construct(private readonly ConnectionPool $connectionPool)
    {
    }

    public function getIconOverlay(string $table, array $row, array $status, string $iconName): string
    {
        if ($row === []) {
            return $iconName;
        }

        if (($GLOBALS['TCA'][$table]['tx_accessrules']['registered'] ?? false) === false) {
            return $iconName;
        }

        $finalStatus = array_filter($status, static fn($value) => $value === true);

        if ($finalStatus === [] || ($finalStatus['nav_hide'] ?? false) === true) {
            if ($this->hasAccessRules($table, $row)) {
                $iconName = 'tx_accessrules_rules';

                if ($finalStatus['nav_hide'] ?? false) {
                    $iconName = 'tx_accessrules_rules-nav-hide';
                }
            }
        }

        return $iconName;
    }

    private function hasAccessRules(string $table, array $row): bool
    {
        $conn = $this->connectionPool->getConnectionForTable($table);

        return (int)$conn->select([Rules::FIELDNAME], $table, ['uid' => $row['uid']])->fetchOne() > 0;
    }
}
