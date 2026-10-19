<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Model;

use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;
use PHPUnit\Framework\TestCase;

final class PurposeModelTest extends TestCase
{
    public function testSaveAssignsAliasAndPersists(): void
    {
        $repo  = new PurposeRepository();
        $model = new PurposeModel($repo);

        $p = new Purpose();
        $p->setName('Product Updates')->setIsPublished(true);
        $model->saveEntity($p);

        self::assertNotNull($p->getId());
        self::assertNotSame('', $p->getAlias());
        self::assertSame($p, $model->getEntity($p->getId()));
        self::assertCount(1, $model->getPublished());
    }

    public function testDelete(): void
    {
        $repo  = new PurposeRepository();
        $model = new PurposeModel($repo);
        $p     = new Purpose();
        $p->setName('X')->setAlias('x');
        $model->saveEntity($p);
        $id = $p->getId();
        $model->deleteEntity($p);
        self::assertNull($model->getEntity($id));
    }
}
