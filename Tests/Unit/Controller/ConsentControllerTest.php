<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Controller;

use MauticPlugin\MauticConsentBundle\Controller\ConsentController;
use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Model\ConsentModel;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;
use PHPUnit\Framework\TestCase;

final class ConsentControllerTest extends TestCase
{
    private ConsentController $ctrl;
    private PurposeModel $purposeModel;

    protected function setUp(): void
    {
        $purposes = new PurposeRepository();
        $records  = new ConsentRecordRepository();
        $ledger   = new ConsentLedger($records, $purposes);
        $this->purposeModel = new PurposeModel($purposes);
        $consentModel = new ConsentModel($records, $ledger);
        $this->ctrl = new ConsentController($consentModel, $this->purposeModel);

        $p = new Purpose();
        $p->setName('News')->setAlias('news')->setIsPublished(true);
        $this->purposeModel->saveEntity($p);
    }

    public function testIndexEmpty(): void
    {
        $res = $this->ctrl->indexAction();
        self::assertSame('Consent/list.html.twig', $res['template']);
        self::assertSame([], $res['vars']['items']);
    }

    public function testGrantAndContactList(): void
    {
        $res = $this->ctrl->grantAction([
            'contact_id'     => 55,
            'purpose_alias'  => 'news',
            'notes'          => 'manual',
        ]);

        self::assertArrayHasKey('record', $res);
        self::assertTrue($res['record']->isGranted());
        self::assertCount(1, $res['vars']['items']);
    }

    public function testWithdraw(): void
    {
        $this->ctrl->grantAction(['contact_id' => 1, 'purpose_alias' => 'news']);
        $res = $this->ctrl->withdrawAction(['contact_id' => 1, 'purpose_alias' => 'news']);
        self::assertTrue($res['record']->isWithdrawn());
    }

    public function testGrantInvalid(): void
    {
        $res = $this->ctrl->grantAction(['contact_id' => 0, 'purpose_alias' => '']);
        self::assertArrayHasKey('error', $res);
    }

    public function testManageAction(): void
    {
        $res = $this->ctrl->manageAction(9);
        self::assertSame('Consent/manage.html.twig', $res['template']);
        self::assertSame(9, $res['vars']['contact_id']);
        self::assertNotEmpty($res['vars']['purposes']);
    }
}
