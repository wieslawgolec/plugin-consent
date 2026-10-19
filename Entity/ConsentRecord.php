<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Immutable-style consent decision for a contact × purpose.
 *
 * Status changes create a clear audit trail via grantedAt / withdrawnAt / source / meta.
 * Does not replace core Do Not Contact.
 */
class ConsentRecord
{
    public const TABLE = 'consent_records';

    public const STATUS_GRANTED   = 'granted';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_PENDING   = 'pending';

    public const SOURCE_FORM       = 'form';
    public const SOURCE_PREFERENCE = 'preference_page';
    public const SOURCE_API        = 'api';
    public const SOURCE_IMPORT     = 'import';
    public const SOURCE_ADMIN      = 'admin';
    public const SOURCE_CAMPAIGN   = 'campaign';

    private ?int $id = null;
    private int $contactId = 0;
    private int $purposeId = 0;
    private string $status = self::STATUS_PENDING;
    private int $purposeVersion = 1;
    private string $source = self::SOURCE_FORM;
    private ?string $sourceDetail = null;
    private ?string $ipAddress = null;
    private ?string $userAgent = null;
    private ?\DateTimeInterface $grantedAt = null;
    private ?\DateTimeInterface $withdrawnAt = null;
    private ?\DateTimeInterface $dateAdded = null;
    private ?\DateTimeInterface $dateModified = null;
    private ?string $notes = null;

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        if (!class_exists(ClassMetadataBuilder::class)) {
            return;
        }

        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(self::TABLE)
            ->setCustomRepositoryClass(ConsentRecordRepository::class)
            ->addId()
            ->addNamedField('contactId', Types::INTEGER, 'contact_id')
            ->addNamedField('purposeId', Types::INTEGER, 'purpose_id')
            ->addField('status', Types::STRING)
            ->addNamedField('purposeVersion', Types::INTEGER, 'purpose_version')
            ->addField('source', Types::STRING)
            ->addNullableField('sourceDetail', Types::STRING, 'source_detail')
            ->addNullableField('ipAddress', Types::STRING, 'ip_address')
            ->addNullableField('userAgent', Types::STRING, 'user_agent')
            ->addNullableField('grantedAt', Types::DATETIME_MUTABLE, 'granted_at')
            ->addNullableField('withdrawnAt', Types::DATETIME_MUTABLE, 'withdrawn_at')
            ->addNullableField('dateAdded', Types::DATETIME_MUTABLE, 'date_added')
            ->addNullableField('dateModified', Types::DATETIME_MUTABLE, 'date_modified')
            ->addNullableField('notes', Types::TEXT)
            ->addIndex(['contact_id', 'purpose_id'], 'consent_contact_purpose')
            ->addIndex(['status'], 'consent_status')
            ->addIndex(['contact_id', 'status'], 'consent_contact_status');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function setContactId(int $contactId): self
    {
        $this->contactId = $contactId;

        return $this;
    }

    public function getPurposeId(): int
    {
        return $this->purposeId;
    }

    public function setPurposeId(int $purposeId): self
    {
        $this->purposeId = $purposeId;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_GRANTED, self::STATUS_WITHDRAWN, self::STATUS_PENDING], true)) {
            throw new \InvalidArgumentException('Invalid consent status: ' . $status);
        }
        $this->status = $status;

        return $this;
    }

    public function isGranted(): bool
    {
        return self::STATUS_GRANTED === $this->status;
    }

    public function isWithdrawn(): bool
    {
        return self::STATUS_WITHDRAWN === $this->status;
    }

    public function grant(?\DateTimeInterface $at = null): self
    {
        $this->status    = self::STATUS_GRANTED;
        $this->grantedAt = $at ?? new \DateTimeImmutable();
        $this->withdrawnAt = null;
        $this->dateModified = new \DateTimeImmutable();

        return $this;
    }

    public function withdraw(?\DateTimeInterface $at = null): self
    {
        $this->status      = self::STATUS_WITHDRAWN;
        $this->withdrawnAt = $at ?? new \DateTimeImmutable();
        $this->dateModified = new \DateTimeImmutable();

        return $this;
    }

    public function getPurposeVersion(): int
    {
        return $this->purposeVersion;
    }

    public function setPurposeVersion(int $purposeVersion): self
    {
        $this->purposeVersion = max(1, $purposeVersion);

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSourceDetail(): ?string
    {
        return $this->sourceDetail;
    }

    public function setSourceDetail(?string $sourceDetail): self
    {
        $this->sourceDetail = $sourceDetail;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getGrantedAt(): ?\DateTimeInterface
    {
        return $this->grantedAt;
    }

    public function setGrantedAt(?\DateTimeInterface $grantedAt): self
    {
        $this->grantedAt = $grantedAt;

        return $this;
    }

    public function getWithdrawnAt(): ?\DateTimeInterface
    {
        return $this->withdrawnAt;
    }

    public function setWithdrawnAt(?\DateTimeInterface $withdrawnAt): self
    {
        $this->withdrawnAt = $withdrawnAt;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(?\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * True when the stored purpose version is older than the current purpose definition.
     */
    public function needsReconsent(int $currentPurposeVersion): bool
    {
        return $this->isGranted() && $this->purposeVersion < $currentPurposeVersion;
    }
}
