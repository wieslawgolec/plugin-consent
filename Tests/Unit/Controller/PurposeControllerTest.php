<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Controller;

use MauticPlugin\MauticConsentBundle\Controller\PurposeController;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;
use PHPUnit\Framework\TestCase;

final class PurposeControllerTest extends TestCase
{
    public function testNewAndIndex(): void
    {
        $model = new PurposeModel(new PurposeRepository());
        $ctrl  = new PurposeController($model);

        $created = $ctrl->newAction([
            'name'        => 'Newsletter',
            'alias'       => 'newsletter',
            'legalBasis'  => 'consent',
            'isPublished' => true,
        ]);

        self::assertArrayHasKey('entity', $created);
        self::assertNotNull($created['entity']->getId());

        $list = $ctrl->indexAction();
        self::assertSame('Purpose/list.html.twig', $list['template']);
        self::assertCount(1, $list['vars']['items']);
    }

    public function testEditAndDelete(): void
    {
        $model = new PurposeModel(new PurposeRepository());
        $ctrl  = new PurposeController($model);
        $new   = $ctrl->newAction(['name' => 'T', 'alias' => 't', 'isPublished' => true]);
        $id    = (int) $new['entity']->getId();

        $edited = $ctrl->editAction($id, ['name' => 'T2', 'alias' => 't2']);
        self::assertSame('T2', $edited['entity']->getName());

        $after = $ctrl->deleteAction($id);
        self::assertCount(0, $after['vars']['items']);
    }
}
