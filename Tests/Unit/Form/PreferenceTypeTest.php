<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Form;

use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Form\Type\PreferenceType;
use PHPUnit\Framework\TestCase;

final class PreferenceTypeTest extends TestCase
{
    public function testFieldsFromPurposesSkipsUnpublished(): void
    {
        $a = new Purpose();
        $a->setName('A')->setAlias('a')->setIsPublished(true);
        $b = new Purpose();
        $b->setName('B')->setAlias('b')->setIsPublished(false);

        $fields = PreferenceType::fieldsFromPurposes([$a, $b]);
        self::assertCount(1, $fields);
        self::assertSame('purpose_a', $fields[0]['name']);
    }

    public function testChoicesFromSubmission(): void
    {
        $a = new Purpose();
        $a->setAlias('news')->setIsPublished(true);
        $b = new Purpose();
        $b->setAlias('sms')->setIsPublished(true);

        $choices = PreferenceType::choicesFromSubmission([
            'purpose_news' => '1',
        ], [$a, $b]);

        self::assertTrue($choices['news']);
        self::assertFalse($choices['sms']);
    }
}
