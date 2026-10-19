<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Event;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;

/**
 * Dispatched when consent is granted, withdrawn, or recorded.
 */
final class ConsentEvent
{
    public function __construct(
        private readonly ConsentRecord $record,
        private readonly ?Purpose $purpose = null,
    ) {
    }

    public function getRecord(): ConsentRecord
    {
        return $this->record;
    }

    public function getPurpose(): ?Purpose
    {
        return $this->purpose;
    }

    public function getContactId(): int
    {
        return $this->record->getContactId();
    }

    public function isGranted(): bool
    {
        return $this->record->isGranted();
    }
}
