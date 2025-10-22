# SEF e-Invoice (PHP)
Minimal, sanitized examples for issuing XML invoices (RS/HR) and posting to gateways.

## Quick start
```bash
cp .env.example .env
composer install
php -S localhost:8081 -t examples
Open http://localhost:8081/issue_serbia.php.

## C) Commit & push
```bash
git add .
git commit -m "Initial: minimal SEF e-invoice example (RS)"
git push -u origin main
