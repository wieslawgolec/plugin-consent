<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Helper;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Helper\SegmentConsentFilter;
use PHPUnit\Framework\TestCase;

final class SegmentConsentFilterTest extends TestCase
{
    public function testDefinitions(): void
    {
        $defs = SegmentConsentFilter::getDefinitions();
        self::assertNotEmpty($defs);
        self::assertSame(SegmentConsentFilter::FILTER_HAS_CONSENT, $defs[0]['alias']);
    }

    public function testMatches(): void
    {
        $purposes = new PurposeRepository();
        $records  = new ConsentRecordRepository();
        $ledger   = new ConsentLedger($records, $purposes);

        $p = new Purpose();
        $p->setId(1)->setName('n')->setAlias('news')->setIsPublished(true);
        $purposes->saveEntity($p);
        $ledger->grant(8, $p);

        self::assertTrue(SegmentConsentFilter::matches($ledger, 8, 'news', '='));
        self::assertFalse(SegmentConsentFilter::matches($ledger, 8, 'news', '!='));
        self::assertFalse(SegmentConsentFilter::matches($ledger, 8, 'other', '='));
        self::assertTrue(SegmentConsentFilter::matches($ledger, 8, 'other', '!='));
    }
}
