<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Events;

use TRAW\AccessRules\Tca\IconOverlay;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Imaging\Event\ModifyRecordOverlayIconIdentifierEvent;

#[AsEventListener(identifier: 'traw-access-rules/db-definition', event: ModifyRecordOverlayIconIdentifierEvent::class)]
final readonly class ModifyRecordOverlayIconIdentifierEventListener
{
    public function __construct(private IconOverlay $iconOverlay)
    {
    }

    public function __invoke(ModifyRecordOverlayIconIdentifierEvent $event): void
    {
        $event->setOverlayIconIdentifier(
            $this->iconOverlay->getIconOverlay(
                $event->getTable(),
                $event->getRow(),
                $event->getStatus(),
                $event->getOverlayIconIdentifier()
            )
        );
    }
}
