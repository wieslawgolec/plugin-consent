<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle;

/**
 * Event names for the consent ledger.
 *
 * Complements core DNC / categories / preference-center pages.
 * Does not own tracking-cookie or admin privacy-centre behaviour.
 */
final class ConsentEvents
{
    /**
     * Fired after a consent record is granted or updated to granted.
     *
     * Listeners receive ConsentEvent.
     */
    public const CONSENT_GRANTED = 'mautic.consent_granted';

    /**
     * Fired after a consent record is withdrawn or updated to withdrawn.
     *
     * Listeners receive ConsentEvent.
     */
    public const CONSENT_WITHDRAWN = 'mautic.consent_withdrawn';

    /**
     * Fired when consent is recorded from any source (form, preference page, API, import).
     *
     * Listeners receive ConsentEvent.
     */
    public const CONSENT_RECORDED = 'mautic.consent_recorded';
}
