<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Form\Type;

/**
 * Field definitions for Purpose admin form (Symfony FormType shape without full SF dependency in unit tests).
 */
final class PurposeType
{
    public const NAME = 'consent_purpose';

    /**
     * @return list<array{name: string, type: string, required?: bool, options?: array}>
     */
    public static function getFields(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'alias', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'type' => 'textarea'],
            ['name' => 'legalBasis', 'type' => 'choice', 'required' => true, 'options' => [
                'choices' => [
                    'Consent'             => 'consent',
                    'Contract'            => 'contract',
                    'Legal obligation'    => 'legal_obligation',
                    'Vital interests'     => 'vital_interests',
                    'Public task'         => 'public_task',
                    'Legitimate interest' => 'legitimate_interest',
                ],
            ]],
            ['name' => 'policyText', 'type' => 'textarea'],
            ['name' => 'version', 'type' => 'integer'],
            ['name' => 'required', 'type' => 'checkbox'],
            ['name' => 'order', 'type' => 'integer'],
            ['name' => 'channelHint', 'type' => 'choice', 'options' => [
                'choices' => [
                    'Any'       => '',
                    'Email'     => 'email',
                    'SMS'       => 'sms',
                    'WhatsApp'  => 'whatsapp',
                    'Tracking'  => 'tracking',
                    'Other'     => 'other',
                ],
            ]],
            ['name' => 'isPublished', 'type' => 'checkbox'],
        ];
    }
}
