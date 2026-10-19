<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Helper;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use PHPUnit\Framework\TestCase;

final class ConsentLedgerTest extends TestCase
{
    private PurposeRepository $purposes;
    private ConsentRecordRepository $records;
    private ConsentLedger $ledger;

    protected function setUp(): void
    {
        $this->purposes = new PurposeRepository();
        $this->records  = new ConsentRecordRepository();
        $this->ledger   = new ConsentLedger($this->records, $this->purposes);
    }

    private function purpose(string $alias, int $id = 1, int $version = 1): Purpose
    {
        $p = new Purpose();
        $p->setId($id)->setName($alias)->setAlias($alias)->setVersion($version)->setIsPublished(true);
        $this->purposes->saveEntity($p);

        return $p;
    }

    public function testGrantAndHasConsent(): void
    {
        $p = $this->purpose('newsletter');
        $this->ledger->grant(10, $p, ConsentRecord::SOURCE_FORM, ['ip' => '1.2.3.4']);

        self::assertTrue($this->ledger->hasConsent(10, 'newsletter'));
        self::assertFalse($this->ledger->hasConsent(10, 'unknown'));
        self::assertFalse($this->ledger->hasConsent(99, 'newsletter'));

        $list = $this->ledger->listForContact(10);
        self::assertCount(1, $list);
        self::assertTrue($list[0]->isGranted());
        self::assertSame('1.2.3.4', $list[0]->getIpAddress());
    }

    public function testWithdraw(): void
    {
        $p = $this->purpose('sms');
        $this->ledger->grant(5, $p);
        $this->ledger->withdraw(5, $p, ConsentRecord::SOURCE_PREFERENCE);

        self::assertFalse($this->ledger->hasConsent(5, 'sms'));
        self::assertTrue($this->ledger->listForContact(5)[0]->isWithdrawn());
    }

    public function testApplyPreferenceChoices(): void
    {
        $this->purpose('a', 1);
        $this->purpose('b', 2);

        $results = $this->ledger->applyPreferenceChoices(3, ['a' => true, 'b' => false]);
        self::assertCount(2, $results);
        self::assertTrue($this->ledger->hasConsent(3, 'a'));
        self::assertFalse($this->ledger->hasConsent(3, 'b'));
    }

    public function testFindStaleForPurpose(): void
    {
        $p = $this->purpose('v', 1, 1);
        $this->ledger->grant(1, $p);
        $p->setVersion(3);

        $stale = $this->ledger->findStaleForPurpose($p);
        self::assertCount(1, $stale);
        self::assertTrue($stale[0]->needsReconsent(3));
    }
}
