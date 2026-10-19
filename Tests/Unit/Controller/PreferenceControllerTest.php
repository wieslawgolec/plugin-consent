<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Controller;

use MauticPlugin\MauticConsentBundle\Controller\PreferenceController;
use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Helper\ConsentToken;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;
use PHPUnit\Framework\TestCase;

final class PreferenceControllerTest extends TestCase
{
    private PreferenceController $ctrl;
    private ConsentLedger $ledger;
    private ConsentToken $tokens;

    protected function setUp(): void
    {
        $purposes = new PurposeRepository();
        $records  = new ConsentRecordRepository();
        $this->ledger = new ConsentLedger($records, $purposes);
        $this->tokens = new ConsentToken('unit-test-secret');
        $model = new PurposeModel($purposes);
        $this->ctrl = new PreferenceController($model, $this->ledger, $this->tokens);

        $p = new Purpose();
        $p->setName('News')->setAlias('news')->setIsPublished(true);
        $model->saveEntity($p);
    }

    public function testShowValidToken(): void
    {
        $token = $this->tokens->create(100);
        $res   = $this->ctrl->showAction($token);

        self::assertTrue($res['ok']);
        self::assertSame(200, $res['status']);
        self::assertSame(100, $res['vars']['contact_id']);
        self::assertNotEmpty($res['vars']['fields']);
    }

    public function testShowInvalidToken(): void
    {
        $res = $this->ctrl->showAction('not-a-token');
        self::assertFalse($res['ok']);
        self::assertSame(403, $res['status']);
    }

    public function testSavePreferences(): void
    {
        $token = $this->tokens->create(200);
        $res   = $this->ctrl->saveAction($token, ['purpose_news' => '1']);

        self::assertTrue($res['ok']);
        self::assertTrue($this->ledger->hasConsent(200, 'news'));
        self::assertSame(1, $res['vars']['count']);
    }
}
