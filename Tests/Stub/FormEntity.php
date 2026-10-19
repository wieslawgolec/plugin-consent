<?php

declare(strict_types=1);

namespace Mautic\CoreBundle\Entity;

/**
 * Minimal FormEntity stub for isolated unit tests.
 */
class FormEntity
{
    protected ?int $id = null;
    protected bool $isPublished = true;
    protected ?\DateTimeInterface $dateAdded = null;
    protected ?\DateTimeInterface $dateModified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function isPublished(bool $checkPublishStatus = true, bool $checkCategoryPublishStatus = true): bool
    {
        return $this->isPublished;
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
}
