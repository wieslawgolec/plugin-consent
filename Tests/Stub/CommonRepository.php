<?php

declare(strict_types=1);

namespace Mautic\CoreBundle\Entity;

/**
 * Minimal CommonRepository stub for isolated unit tests.
 */
class CommonRepository
{
    /** @var array<int, object> */
    protected array $entities = [];

    public function saveEntity(object $entity, bool $flush = true): void
    {
        $id = method_exists($entity, 'getId') ? $entity->getId() : null;
        if (null === $id && method_exists($entity, 'setId')) {
            $id = count($this->entities) + 1;
            $entity->setId($id);
        }
        if (null !== $id) {
            $this->entities[$id] = $entity;
        }
    }

    public function deleteEntity(object $entity): void
    {
        $id = method_exists($entity, 'getId') ? $entity->getId() : null;
        if (null !== $id) {
            unset($this->entities[$id]);
        }
    }

    public function getEntity($id): ?object
    {
        return $this->entities[(int) $id] ?? null;
    }

    /**
     * @return list<object>
     */
    public function getEntities(array $args = []): array
    {
        return array_values($this->entities);
    }
}
