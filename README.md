# SEF e-Invoice (PHP)

Sanitized, minimal examples for issuing XML invoices and posting to Balkan e-invoice gateways (RS/HR).

## Features
- XML builder for invoice payloads
- Serbia issuer example (extendable to HR, BA, ME, MK)
- Env-driven endpoints/keys
- Plain cURL HTTP client

## Quick Start
cp .env.example .env
composer install
php -S localhost:8081 -t examples

Open http://localhost:8081/issue_serbia.php
