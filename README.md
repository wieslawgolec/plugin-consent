# Mautic Consent Bundle

Purpose-based **consent ledger** and public **preference page** for **Mautic 7.2+**.

### Complementary by design

This plugin is **intentionally complementary** to core Mautic and to the community **[Marketing without surveillance](https://mautic.org/blog/marketing-without-surveillance-funded-by-nlnet)** initiative (NLnet-funded; **early design phase** — [forum announcement](https://forum.mautic.org/t/marketing-without-surveillance-funded-by-nlnet/38502)).

| This plugin | Core / “Marketing without surveillance” |
|-------------|------------------------------------------|
| Contact **communication consent** by purpose (newsletter, SMS, etc.) | **Tracking footprint** (cookies, pixels, optional no-cookie mode) |
| Consent ledger + preference toggles for *messages* | Admin **privacy / tracking centre** for *product settings* |
| Works **with** DNC, categories, and preference-center pages | Owns / evolves DNC, categories, unsubscribe, tracking flags |

**This plugin does not implement:**

* Cookie CMP / script blocking on the website  
* Admin tracking privacy centre  
* Replacement of core **Do Not Contact** (DNC), frequency rules, or unsubscribe  

Those capabilities stay with **core Mautic** and/or the **[Marketing without surveillance](https://mautic.org/blog/marketing-without-surveillance-funded-by-nlnet)** project as it moves from early design into implementation. Use this plugin for purpose-based marketing consent only; do not treat it as a full privacy or CMP product.

|  |  |
|--|--|
| **Version** | 1.1.0 |
| **PHP** | 8.2+ (CI: 8.2, 8.5, 8.6) |
| **Mautic** | 7.2+ |
| **License** | GPL-3.0-or-later |

---

## Scope (what this plugin owns)

| Feature | Included |
|---------|----------|
| Purpose definitions (name, alias, legal basis, version, policy text) | ✓ |
| Consent records (granted / withdrawn / pending) with source, IP, UA, timestamps | ✓ |
| Version-aware re-consent detection | ✓ |
| Signed public preference page (`/consent/preferences/{token}`) | ✓ |
| Segment filter helper: has consent for purpose | ✓ |
| Admin purpose CRUD + permissions keys | ✓ |
| Schema migration | ✓ |

| Explicitly **not** in this plugin | Reason |
|----------------------------------|--------|
| Cookie CMP / script blocking | Website / CMP concern; out of scope |
| Admin tracking privacy centre | Core / [Marketing without surveillance](https://mautic.org/blog/marketing-without-surveillance-funded-by-nlnet) (early design phase) |
| Replacing core DNC or frequency rules | Core features — this plugin only *adds* purpose consent |
| Replacing core unsubscribe / preference-center pages | Core features — this plugin’s preference page is purpose-only |
| Full DSAR / erasure product | Separate legal/process tooling |

---

## Install

```bash
cd /path/to/mautic
git clone <this-repo> plugins/MauticConsentBundle
php bin/console cache:clear
php bin/console mautic:plugins:reload
php bin/console doctrine:migrations:migrate --no-interaction
```

Directory name must be **`MauticConsentBundle`**.

---

## Architecture

```
Entity/Purpose.php + PurposeRepository
Entity/ConsentRecord.php + ConsentRecordRepository
Helper/ConsentLedger.php      # grant / withdraw / query
Helper/ConsentToken.php       # HMAC preference links
Helper/SegmentConsentFilter.php
Model/PurposeModel.php, ConsentModel.php
Controller/PurposeController.php, ConsentController.php, PreferenceController.php
EventListener/FormSubscriber.php, CampaignSubscriber.php
Migrations/Version20261019140000.php
```

---

## Admin UI

| Route | Purpose |
|-------|---------|
| `/s/consent/purposes` | Purpose list / CRUD |
| `/s/consent/records` | Consent record list |
| `/s/consent/records/contact/{id}` | Per-contact history |
| `/s/consent/records/manage` | Manual grant / withdraw |
| `/consent/preferences/{token}` | Public preference page (no `/s`) |

Menu: **Channels → Consent** (purposes + records).

Form capture: checkbox fields `consent_{alias}` or `consent_purposes[]`.

Campaign condition key: `consent.has_purpose` (purpose_alias + operator).

## Usage sketch

1. Create purposes (e.g. `newsletter`, `product_updates`, `sms_alerts`).
2. Capture consent from forms (call `ConsentLedger::grant`) or preference page.
3. Segment / campaign: contact **has consent** for purpose alias `newsletter`.
4. Bump purpose **version** when policy text changes → `needsReconsent()` identifies stale grants.

Preference link:

```php
$token = $consentToken->create($contactId);
// https://your-mautic.example/consent/preferences/{token}
```

---

## Development & tests

```bash
composer update --ignore-platform-reqs
vendor/bin/phpunit --configuration phpunit.xml.dist
```

CI: PHP **8.2 / 8.5 / 8.6**, PHPUnit **11.5**.

---

## Support the project

* **GitHub Sponsors:** [github.com/sponsors/wieslawgolec](https://github.com/sponsors/wieslawgolec)
* **Buy Me a Coffee:** [buymeacoffee.com/wieslawgolec](https://buymeacoffee.com/wieslawgolec)

## License

GPL-3.0-or-later (same family as Mautic).
