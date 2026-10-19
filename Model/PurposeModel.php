<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Model;

use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;

/**
 * FormModel-compatible purpose CRUD.
 */
class PurposeModel
{
    public function __construct(
        private readonly PurposeRepository $repository,
    ) {
    }

    public function getRepository(): PurposeRepository
    {
        return $this->repository;
    }

    public function getEntity($id = null): ?Purpose
    {
        if (null === $id) {
            return new Purpose();
        }

        $entity = $this->repository->getEntity($id);

        return $entity instanceof Purpose ? $entity : null;
    }

    public function saveEntity(Purpose $entity): void
    {
        if ('' === $entity->getAlias() && '' !== $entity->getName()) {
            $entity->setAlias($entity->getName());
        }
        if (null === $entity->getDateAdded()) {
            $entity->setDateAdded(new \DateTimeImmutable());
        }
        $entity->setDateModified(new \DateTimeImmutable());
        $this->repository->saveEntity($entity);
    }

    public function deleteEntity(Purpose $entity): void
    {
        $this->repository->deleteEntity($entity);
    }

    /**
     * @return list<Purpose>
     */
    public function getPublished(): array
    {
        return $this->repository->findPublishedOrdered();
    }

    public function getByAlias(string $alias): ?Purpose
    {
        return $this->repository->findOneByAlias($alias);
    }
}
