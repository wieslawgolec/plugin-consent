<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Model;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;

/**
 * Thin model around ConsentLedger for controllers / campaign conditions.
 */
class ConsentModel
{
    public function __construct(
        private readonly ConsentRecordRepository $repository,
        private readonly ConsentLedger $ledger,
    ) {
    }

    public function getRepository(): ConsentRecordRepository
    {
        return $this->repository;
    }

    public function getLedger(): ConsentLedger
    {
        return $this->ledger;
    }

    public function getEntity($id = null): ?ConsentRecord
    {
        if (null === $id) {
            return new ConsentRecord();
        }
        $entity = $this->repository->getEntity($id);

        return $entity instanceof ConsentRecord ? $entity : null;
    }

    public function grantForContact(int $contactId, Purpose $purpose, string $source = ConsentRecord::SOURCE_ADMIN, array $meta = []): ConsentRecord
    {
        return $this->ledger->grant($contactId, $purpose, $source, $meta);
    }

    public function withdrawForContact(int $contactId, Purpose $purpose, string $source = ConsentRecord::SOURCE_ADMIN, array $meta = []): ConsentRecord
    {
        return $this->ledger->withdraw($contactId, $purpose, $source, $meta);
    }

    public function contactHasPurpose(int $contactId, string $purposeAlias): bool
    {
        return $this->ledger->hasConsent($contactId, $purposeAlias);
    }

    /**
     * @return list<ConsentRecord>
     */
    public function getForContact(int $contactId): array
    {
        return $this->ledger->listForContact($contactId);
    }
}
