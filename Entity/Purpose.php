<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;

/**
 * A processing purpose (e.g. marketing email, product updates, SMS alerts).
 *
 * Complements core email categories / DNC — does not replace them.
 */
class Purpose extends FormEntity
{
    public const TABLE = 'consent_purposes';

    protected ?int $id = null;
    private string $name = '';
    private string $alias = '';
    private ?string $description = null;
    private string $legalBasis = 'consent';
    private ?string $policyText = null;
    private int $version = 1;
    private bool $required = false;
    private int $order = 0;
    private ?string $channelHint = null;

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        if (!class_exists(ClassMetadataBuilder::class)) {
            return;
        }

        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(self::TABLE)
            ->setCustomRepositoryClass(PurposeRepository::class)
            ->addId()
            ->addField('name', Types::STRING)
            ->addField('alias', Types::STRING)
            ->addNullableField('description', Types::TEXT)
            ->addField('legalBasis', Types::STRING, 'legal_basis')
            ->addNullableField('policyText', Types::TEXT, 'policy_text')
            ->addField('version', Types::INTEGER)
            ->addField('required', Types::BOOLEAN)
            ->addField('order', Types::INTEGER, 'purpose_order')
            ->addNullableField('channelHint', Types::STRING, 'channel_hint')
            ->addPublishDates()
            ->addIndex(['alias'], 'consent_purpose_alias')
            ->addIndex(['is_published'], 'consent_purpose_published');
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = strtolower(preg_replace('/[^a-z0-9_\-]/i', '-', $alias) ?? $alias);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLegalBasis(): string
    {
        return $this->legalBasis;
    }

    public function setLegalBasis(string $legalBasis): self
    {
        $this->legalBasis = $legalBasis;

        return $this;
    }

    public function getPolicyText(): ?string
    {
        return $this->policyText;
    }

    public function setPolicyText(?string $policyText): self
    {
        $this->policyText = $policyText;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = max(1, $version);

        return $this;
    }

    public function bumpVersion(): self
    {
        ++$this->version;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getChannelHint(): ?string
    {
        return $this->channelHint;
    }

    /**
     * Optional hint: email, sms, whatsapp, tracking, etc. Not enforced by core tracking.
     */
    public function setChannelHint(?string $channelHint): self
    {
        $this->channelHint = $channelHint;

        return $this;
    }

    public function canBePresented(): bool
    {
        return $this->isPublished();
    }
}
