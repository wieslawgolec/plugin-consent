<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Helper;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;

/**
 * High-level facade for recording and querying purpose-based consent.
 *
 * Does not touch core Do Not Contact or tracking cookies.
 */
final class ConsentLedger
{
    public function __construct(
        private readonly ConsentRecordRepository $records,
        private readonly PurposeRepository $purposes,
    ) {
    }

    public function grant(
        int $contactId,
        Purpose $purpose,
        string $source = ConsentRecord::SOURCE_FORM,
        array $meta = [],
    ): ConsentRecord {
        $record = $this->records->findOneByContactAndPurpose($contactId, (int) $purpose->getId())
            ?? (new ConsentRecord())
                ->setContactId($contactId)
                ->setPurposeId((int) $purpose->getId())
                ->setDateAdded(new \DateTimeImmutable());

        $record
            ->setPurposeVersion($purpose->getVersion())
            ->setSource($source)
            ->setSourceDetail($meta['source_detail'] ?? null)
            ->setIpAddress($meta['ip'] ?? null)
            ->setUserAgent($meta['user_agent'] ?? null)
            ->setNotes($meta['notes'] ?? null)
            ->grant();

        $this->records->saveEntity($record);

        return $record;
    }

    public function withdraw(
        int $contactId,
        Purpose $purpose,
        string $source = ConsentRecord::SOURCE_PREFERENCE,
        array $meta = [],
    ): ConsentRecord {
        $record = $this->records->findOneByContactAndPurpose($contactId, (int) $purpose->getId())
            ?? (new ConsentRecord())
                ->setContactId($contactId)
                ->setPurposeId((int) $purpose->getId())
                ->setPurposeVersion($purpose->getVersion())
                ->setDateAdded(new \DateTimeImmutable());

        $record
            ->setSource($source)
            ->setSourceDetail($meta['source_detail'] ?? null)
            ->setIpAddress($meta['ip'] ?? null)
            ->setUserAgent($meta['user_agent'] ?? null)
            ->setNotes($meta['notes'] ?? null)
            ->withdraw();

        $this->records->saveEntity($record);

        return $record;
    }

    public function hasConsent(int $contactId, string $purposeAlias): bool
    {
        $purpose = $this->purposes->findOneByAlias($purposeAlias);
        if (null === $purpose || null === $purpose->getId()) {
            return false;
        }

        return $this->records->hasGrantedConsent($contactId, $purpose->getId());
    }

    public function hasConsentForPurposeId(int $contactId, int $purposeId): bool
    {
        return $this->records->hasGrantedConsent($contactId, $purposeId);
    }

    /**
     * @return list<ConsentRecord>
     */
    public function listForContact(int $contactId): array
    {
        return $this->records->findByContact($contactId);
    }

    /**
     * Apply a map of purposeAlias => bool from a preference form.
     *
     * @param array<string, bool> $choices
     * @return list<ConsentRecord>
     */
    public function applyPreferenceChoices(
        int $contactId,
        array $choices,
        string $source = ConsentRecord::SOURCE_PREFERENCE,
        array $meta = [],
    ): array {
        $results = [];
        foreach ($choices as $alias => $granted) {
            $purpose = $this->purposes->findOneByAlias((string) $alias);
            if (null === $purpose) {
                continue;
            }
            $results[] = $granted
                ? $this->grant($contactId, $purpose, $source, $meta)
                : $this->withdraw($contactId, $purpose, $source, $meta);
        }

        return $results;
    }

    /**
     * Contacts whose granted consent is behind the current purpose version.
     *
     * @return list<ConsentRecord>
     */
    public function findStaleForPurpose(Purpose $purpose): array
    {
        $all = $this->records->getEntities();
        $out = [];
        foreach ($all as $entity) {
            if (!$entity instanceof ConsentRecord) {
                continue;
            }
            if ($entity->getPurposeId() !== (int) $purpose->getId()) {
                continue;
            }
            if ($entity->needsReconsent($purpose->getVersion())) {
                $out[] = $entity;
            }
        }

        return $out;
    }
}
