<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\EventListener;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\EventListener\CampaignSubscriber;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use PHPUnit\Framework\TestCase;

final class CampaignSubscriberTest extends TestCase
{
    public function testEvaluate(): void
    {
        $purposes = new PurposeRepository();
        $records  = new ConsentRecordRepository();
        $ledger   = new ConsentLedger($records, $purposes);
        $sub      = new CampaignSubscriber($ledger);

        $p = new Purpose();
        $p->setId(1)->setName('n')->setAlias('news')->setIsPublished(true);
        $purposes->saveEntity($p);
        $ledger->grant(4, $p);

        self::assertTrue($sub->evaluate(4, ['purpose_alias' => 'news', 'operator' => '=']));
        self::assertFalse($sub->evaluate(4, ['purpose_alias' => 'news', 'operator' => '!=']));
        self::assertFalse($sub->evaluate(4, ['purpose_alias' => 'other']));
        self::assertFalse($sub->evaluate(0, ['purpose_alias' => 'news']));
    }

    public function testDefinition(): void
    {
        $def = CampaignSubscriber::getConditionDefinition();
        self::assertSame(CampaignSubscriber::CONDITION_HAS_CONSENT, $def['key']);
        self::assertNotEmpty($def['formFields']);
    }
}
