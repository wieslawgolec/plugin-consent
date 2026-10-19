<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\EventListener;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;

/**
 * Captures purpose consent from form submissions.
 *
 * Convention: form fields named consent_{purposeAlias} (checkbox) or
 * a multi-value field consent_purposes[] with aliases.
 *
 * In full Mautic this listens to FormEvents::FORM_ON_SUBMIT.
 * Unit tests call handleSubmission() directly.
 */
final class FormSubscriber
{
    public function __construct(
        private readonly ConsentLedger $ledger,
        private readonly PurposeModel $purposeModel,
    ) {
    }

    /**
     * @param array<string, mixed> $formData  Submitted form values
     * @param array<string, mixed> $meta      ip, user_agent, form_id, ...
     * @return list<\MauticPlugin\MauticConsentBundle\Entity\ConsentRecord>
     */
    public function handleSubmission(int $contactId, array $formData, array $meta = []): array
    {
        if ($contactId < 1) {
            return [];
        }

        $choices = $this->extractChoices($formData);
        if ([] === $choices) {
            return [];
        }

        $meta = array_merge([
            'source_detail' => isset($meta['form_id']) ? 'form:' . $meta['form_id'] : 'form',
        ], $meta);

        return $this->ledger->applyPreferenceChoices(
            $contactId,
            $choices,
            ConsentRecord::SOURCE_FORM,
            $meta
        );
    }

    /**
     * @param array<string, mixed> $formData
     * @return array<string, bool>
     */
    public function extractChoices(array $formData): array
    {
        $choices = [];

        // consent_purposes as list of aliases (checked)
        if (isset($formData['consent_purposes']) && is_array($formData['consent_purposes'])) {
            foreach ($formData['consent_purposes'] as $alias) {
                $choices[(string) $alias] = true;
            }
        }

        // consent_{alias} checkboxes
        foreach ($formData as $key => $value) {
            if (!is_string($key) || !str_starts_with($key, 'consent_')) {
                continue;
            }
            if ('consent_purposes' === $key) {
                continue;
            }
            $alias = substr($key, strlen('consent_'));
            if ('' === $alias) {
                continue;
            }
            // Only map known published purposes when possible
            $purpose = $this->purposeModel->getByAlias($alias);
            if (null === $purpose) {
                // still record alias if present (ledger skips unknown)
                $choices[$alias] = !empty($value);
                continue;
            }
            $choices[$alias] = !empty($value);
        }

        return $choices;
    }

    /**
     * Suggested form field names for published purposes.
     *
     * @return list<array{name: string, label: string, alias: string, required: bool}>
     */
    public function suggestedFormFields(): array
    {
        $fields = [];
        foreach ($this->purposeModel->getPublished() as $purpose) {
            $fields[] = [
                'name'     => 'consent_' . $purpose->getAlias(),
                'label'    => $purpose->getName(),
                'alias'    => $purpose->getAlias(),
                'required' => $purpose->isRequired(),
            ];
        }

        return $fields;
    }
}
