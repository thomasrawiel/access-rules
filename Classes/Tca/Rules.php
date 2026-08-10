<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Tca;

final readonly class Rules
{
    public const string FIELDNAME = 'tx_accessrules_rules';

    public const string TABLENAME = 'tx_accessrules_rule';
    public const string MM_TABLENAME = 'tx_accessrules_rule_group_mm';
    public const int MODE_INCLUDE = 0;
    public const int MODE_EXCLUDE = 1;
}
