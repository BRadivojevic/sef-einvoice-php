
---

### 2) `sef-einvoice-php/README.md`
```md
# SEF e-Invoice (PHP)

Sanitized, minimal examples for issuing XML invoices and posting to Balkan e-invoice gateways (RS/HR). Focused on structure, not vendor-specific details.

## Features
- Small XML builder for invoice payloads
- Example issuer for Serbia (extendable to Croatia/others)
- Env-driven endpoints/keys
- Straightforward cURL HTTP client

## Tech
PHP 8+, DOMDocument, cURL, PSR-4 autoload

## Quick Start
```bash
cp .env.example .env
composer install
php -S localhost:8081 -t examples
