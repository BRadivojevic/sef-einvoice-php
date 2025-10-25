
# SEF e‑Invoice (Serbia) — PHP Integration

Production‑grade **SEF** e‑invoice integration (Serbia) with:
- **UBL 2.1 XML builder** aligned to SEF 3.14.0 profile checks
- **Queue + background worker** to avoid timeouts
- **Retries + dead‑letter** handling
- **Attachments** support
- **.env config + JSON logging**

**Author:** Boško Radivojević — [BRadivojevic](https://github.com/BRadivojevic)

## Setup
1. `composer install`
2. Copy `.env.example` → `.env`
3. Serve `public/` and POST to `/public/submit-invoice.php`
4. Run `workers/send_invoice_worker.php` to process the queue
