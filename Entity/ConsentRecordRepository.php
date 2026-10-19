<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<ConsentRecord>
 */
class ConsentRecordRepository extends CommonRepository
{
    public function findOneByContactAndPurpose(int $contactId, int $purposeId): ?ConsentRecord
    {
        if (method_exists($this, 'findOneBy')) {
            $found = $this->findOneBy([
                'contactId'  => $contactId,
                'purposeId'  => $purposeId,
            ]);

            return $found instanceof ConsentRecord ? $found : null;
        }

        foreach ($this->getEntities() as $entity) {
            if ($entity instanceof ConsentRecord
                && $entity->getContactId() === $contactId
                && $entity->getPurposeId() === $purposeId
            ) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @return list<ConsentRecord>
     */
    public function findByContact(int $contactId): array
    {
        if (method_exists($this, 'findBy')) {
            return array_values($this->findBy(['contactId' => $contactId]));
        }

        $out = [];
        foreach ($this->getEntities() as $entity) {
            if ($entity instanceof ConsentRecord && $entity->getContactId() === $contactId) {
                $out[] = $entity;
            }
        }

        return $out;
    }

    /**
     * @return list<ConsentRecord>
     */
    public function findGrantedByContact(int $contactId): array
    {
        return array_values(array_filter(
            $this->findByContact($contactId),
            static fn (ConsentRecord $r) => $r->isGranted()
        ));
    }

    public function hasGrantedConsent(int $contactId, int $purposeId): bool
    {
        $record = $this->findOneByContactAndPurpose($contactId, $purposeId);

        return null !== $record && $record->isGranted();
    }
}
