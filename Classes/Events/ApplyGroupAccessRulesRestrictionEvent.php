<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Events;

use Psr\Http\Message\ServerRequestInterface;

class ApplyGroupAccessRulesRestrictionEvent
{
    public function __construct(private bool $applyGroupAccessRules, private readonly ServerRequestInterface $request)
    {

    }

    public function getApplyGroupAccessRules(): bool
    {
        return $this->applyGroupAccessRules;
    }

    public function setApplyGroupAccessRules(bool $applyGroupAccessRules): void
    {
        $this->applyGroupAccessRules = $applyGroupAccessRules;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
