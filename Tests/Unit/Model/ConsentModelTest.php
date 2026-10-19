<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Model;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Entity\PurposeRepository;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Model\ConsentModel;
use PHPUnit\Framework\TestCase;

final class ConsentModelTest extends TestCase
{
    public function testGrantWithdrawViaModel(): void
    {
        $purposes = new PurposeRepository();
        $records  = new ConsentRecordRepository();
        $ledger   = new ConsentLedger($records, $purposes);
        $model    = new ConsentModel($records, $ledger);

        $p = new Purpose();
        $p->setId(1)->setName('n')->setAlias('n')->setIsPublished(true);
        $purposes->saveEntity($p);

        $model->grantForContact(11, $p);
        self::assertTrue($model->contactHasPurpose(11, 'n'));
        $model->withdrawForContact(11, $p);
        self::assertFalse($model->contactHasPurpose(11, 'n'));
        self::assertCount(1, $model->getForContact(11));
    }
}
