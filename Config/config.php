<?php

declare(strict_types=1);

return [
    'name'        => 'Consent',
    'description' => 'Purpose-based consent ledger and preference page for Mautic 7.2+. Complements core DNC, categories, and privacy/tracking controls — does not replace them.',
    'version'     => '1.1.0',
    'author'      => 'Wieslaw Golec',

    'routes' => [
        'main' => [
            'mautic_consent_purpose_index' => [
                'path'       => '/consent/purposes',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\PurposeController::indexAction',
            ],
            'mautic_consent_purpose_new' => [
                'path'       => '/consent/purposes/new',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\PurposeController::newAction',
            ],
            'mautic_consent_purpose_edit' => [
                'path'       => '/consent/purposes/edit/{id}',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\PurposeController::editAction',
            ],
            'mautic_consent_purpose_delete' => [
                'path'       => '/consent/purposes/delete/{id}',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\PurposeController::deleteAction',
            ],
            'mautic_consent_index' => [
                'path'       => '/consent/records',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\ConsentController::indexAction',
            ],
            'mautic_consent_contact' => [
                'path'       => '/consent/records/contact/{contactId}',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\ConsentController::contactAction',
            ],
            'mautic_consent_manage' => [
                'path'       => '/consent/records/manage',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\ConsentController::manageAction',
            ],
            'mautic_consent_grant' => [
                'path'       => '/consent/records/grant',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\ConsentController::grantAction',
                'methods'    => ['POST'],
            ],
            'mautic_consent_withdraw' => [
                'path'       => '/consent/records/withdraw',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\ConsentController::withdrawAction',
                'methods'    => ['POST'],
            ],
        ],
        'public' => [
            'mautic_consent_preference' => [
                'path'       => '/consent/preferences/{token}',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\PreferenceController::showAction',
            ],
            'mautic_consent_preference_save' => [
                'path'       => '/consent/preferences/{token}/save',
                'controller' => 'MauticPlugin\MauticConsentBundle\Controller\PreferenceController::saveAction',
                'methods'    => ['POST'],
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'priority' => 50,
            'items'    => [
                'mautic.consent' => [
                    'id'        => 'mautic_consent_root',
                    'iconClass' => 'ri-shield-check-line',
                    'access'    => ['consent:purposes:view', 'consent:records:view'],
                    'parent'    => 'mautic.core.channels',
                    'children'  => [
                        'mautic.consent.purposes' => [
                            'route'  => 'mautic_consent_purpose_index',
                            'access' => 'consent:purposes:view',
                        ],
                        'mautic.consent.records' => [
                            'route'  => 'mautic_consent_index',
                            'access' => 'consent:records:view',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'models' => [
            'mautic.consent.model.purpose' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Model\PurposeModel::class,
                'arguments' => ['mautic.consent.repository.purpose'],
            ],
            'mautic.consent.model.consent' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Model\ConsentModel::class,
                'arguments' => [
                    'mautic.consent.repository.record',
                    'mautic.consent.helper.ledger',
                ],
            ],
        ],
        'repositories' => [
            'mautic.consent.repository.purpose' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Entity\PurposeRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [\MauticPlugin\MauticConsentBundle\Entity\Purpose::class],
            ],
            'mautic.consent.repository.record' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Entity\ConsentRecordRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [\MauticPlugin\MauticConsentBundle\Entity\ConsentRecord::class],
            ],
        ],
        'events' => [
            'mautic.consent.form_subscriber' => [
                'class'     => \MauticPlugin\MauticConsentBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'mautic.consent.helper.ledger',
                    'mautic.consent.model.purpose',
                ],
                'tag' => 'kernel.event_subscriber',
            ],
            'mautic.consent.campaign_subscriber' => [
                'class'     => \MauticPlugin\MauticConsentBundle\EventListener\CampaignSubscriber::class,
                'arguments' => ['mautic.consent.helper.ledger'],
                'tag'       => 'kernel.event_subscriber',
            ],
        ],
        'other' => [
            'mautic.consent.helper.ledger' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Helper\ConsentLedger::class,
                'arguments' => [
                    'mautic.consent.repository.record',
                    'mautic.consent.repository.purpose',
                ],
            ],
            'mautic.consent.helper.token' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Helper\ConsentToken::class,
                'arguments' => ['%mautic.secret_key%'],
            ],
            'mautic.consent.permissions' => [
                'class' => \MauticPlugin\MauticConsentBundle\Security\Permissions\ConsentPermissions::class,
            ],
        ],
        'controllers' => [
            'mautic.consent.controller.purpose' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Controller\PurposeController::class,
                'arguments' => ['mautic.consent.model.purpose'],
            ],
            'mautic.consent.controller.consent' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Controller\ConsentController::class,
                'arguments' => [
                    'mautic.consent.model.consent',
                    'mautic.consent.model.purpose',
                ],
            ],
            'mautic.consent.controller.preference' => [
                'class'     => \MauticPlugin\MauticConsentBundle\Controller\PreferenceController::class,
                'arguments' => [
                    'mautic.consent.model.purpose',
                    'mautic.consent.helper.ledger',
                    'mautic.consent.helper.token',
                ],
            ],
        ],
    ],
];
