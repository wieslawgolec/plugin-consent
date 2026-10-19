<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<Purpose>
 */
class PurposeRepository extends CommonRepository
{
    /**
     * @return list<Purpose>
     */
    public function findPublishedOrdered(): array
    {
        if (method_exists($this, 'createQueryBuilder')) {
            return $this->createQueryBuilder('p')
                ->where('p.isPublished = true')
                ->orderBy('p.order', 'ASC')
                ->addOrderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        }

        $all = $this->getEntities();
        $out = [];
        foreach ($all as $entity) {
            if ($entity instanceof Purpose && $entity->isPublished()) {
                $out[] = $entity;
            }
        }
        usort($out, static fn (Purpose $a, Purpose $b) => $a->getOrder() <=> $b->getOrder());

        return $out;
    }

    public function findOneByAlias(string $alias): ?Purpose
    {
        if (method_exists($this, 'findOneBy')) {
            $found = $this->findOneBy(['alias' => $alias]);

            return $found instanceof Purpose ? $found : null;
        }

        foreach ($this->getEntities() as $entity) {
            if ($entity instanceof Purpose && $entity->getAlias() === $alias) {
                return $entity;
            }
        }

        return null;
    }
}
