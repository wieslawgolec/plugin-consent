<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\EventListener;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\EventListener\FormSubscriber;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;
use PHPUnit\Framework\TestCase;

final class FormSubscriberTest extends TestCase
{
    private FormSubscriber $subscriber;
    private ConsentLedger $ledger;

    protected function setUp(): void
    {
        $purposes = new PurposeRepository();
        $records  = new ConsentRecordRepository();
        $this->ledger = new ConsentLedger($records, $purposes);
        $model = new PurposeModel($purposes);
        $this->subscriber = new FormSubscriber($this->ledger, $model);

        $p = new Purpose();
        $p->setName('News')->setAlias('news')->setIsPublished(true);
        $model->saveEntity($p);
        $p2 = new Purpose();
        $p2->setName('SMS')->setAlias('sms')->setIsPublished(true);
        $model->saveEntity($p2);
    }

    public function testExtractAndHandle(): void
    {
        $records = $this->subscriber->handleSubmission(77, [
            'consent_news' => '1',
            'email'        => 'a@b.c',
        ], ['form_id' => 3, 'ip' => '10.0.0.1']);

        self::assertCount(1, $records);
        self::assertTrue($this->ledger->hasConsent(77, 'news'));
        self::assertFalse($this->ledger->hasConsent(77, 'sms'));
    }

    public function testConsentPurposesArray(): void
    {
        $this->subscriber->handleSubmission(1, [
            'consent_purposes' => ['news', 'sms'],
        ]);
        self::assertTrue($this->ledger->hasConsent(1, 'news'));
        self::assertTrue($this->ledger->hasConsent(1, 'sms'));
    }

    public function testSuggestedFields(): void
    {
        $fields = $this->subscriber->suggestedFormFields();
        self::assertCount(2, $fields);
        self::assertSame('consent_news', $fields[0]['name']);
    }

    public function testSkipsInvalidContact(): void
    {
        self::assertSame([], $this->subscriber->handleSubmission(0, ['consent_news' => '1']));
    }
}
