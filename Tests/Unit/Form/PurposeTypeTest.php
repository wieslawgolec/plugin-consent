<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Form;

use MauticPlugin\MauticConsentBundle\Form\Type\PurposeType;
use PHPUnit\Framework\TestCase;

final class PurposeTypeTest extends TestCase
{
    public function testFieldsIncludeCoreKeys(): void
    {
        $names = array_column(PurposeType::getFields(), 'name');
        self::assertContains('name', $names);
        self::assertContains('alias', $names);
        self::assertContains('legalBasis', $names);
        self::assertContains('version', $names);
    }
}
