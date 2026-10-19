<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Controller;

use MauticPlugin\MauticConsentBundle\Form\Type\PreferenceType;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Helper\ConsentToken;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;

/**
 * Public preference page (signed token). Complements core preference-center pages.
 */
final class PreferenceController
{
    public function __construct(
        private readonly PurposeModel $purposeModel,
        private readonly ConsentLedger $ledger,
        private readonly ConsentToken $tokenHelper,
    ) {
    }

    /**
     * @return array{ok: bool, template: string, vars: array, status: int}
     */
    public function showAction(string $token): array
    {
        $parsed = $this->tokenHelper->validate($token);
        if (!$parsed['valid']) {
            return [
                'ok'       => false,
                'template' => 'Preference/invalid.html.twig',
                'vars'     => ['error' => $parsed['error'] ?? 'invalid'],
                'status'   => 403,
            ];
        }

        $contactId = (int) $parsed['contact_id'];
        $purposes  = $this->purposeModel->getPublished();
        $existing  = $this->ledger->listForContact($contactId);
        $current   = [];
        foreach ($existing as $record) {
            if ($record->isGranted()) {
                $current[$record->getPurposeId()] = true;
            }
        }

        return [
            'ok'       => true,
            'template' => 'Preference/form.html.twig',
            'vars'     => [
                'contact_id' => $contactId,
                'token'      => $token,
                'fields'     => PreferenceType::fieldsFromPurposes($purposes),
                'purposes'   => $purposes,
                'current'    => $current,
            ],
            'status' => 200,
        ];
    }

    /**
     * @param array<string, mixed> $submitted
     * @return array{ok: bool, template: string, vars: array, status: int, records?: list}
     */
    public function saveAction(string $token, array $submitted, array $meta = []): array
    {
        $parsed = $this->tokenHelper->validate($token);
        if (!$parsed['valid']) {
            return [
                'ok'       => false,
                'template' => 'Preference/invalid.html.twig',
                'vars'     => ['error' => $parsed['error'] ?? 'invalid'],
                'status'   => 403,
            ];
        }

        $contactId = (int) $parsed['contact_id'];
        $purposes  = $this->purposeModel->getPublished();
        $choices   = PreferenceType::choicesFromSubmission($submitted, $purposes);
        $records   = $this->ledger->applyPreferenceChoices($contactId, $choices, meta: $meta);

        return [
            'ok'       => true,
            'template' => 'Preference/saved.html.twig',
            'vars'     => [
                'contact_id' => $contactId,
                'count'      => count($records),
            ],
            'status'  => 200,
            'records' => $records,
        ];
    }
}
