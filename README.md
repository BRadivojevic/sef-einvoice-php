# SEF e-Invoice (PHP)

Lightweight, framework-free implementation of **XML invoice generation** and **submission to Balkan e-invoice gateways (SEF)**.  
Includes sanitized examples for Serbia, extendable to Croatia, Bosnia, Montenegro, and North Macedonia.

---

## 🚀 Overview
This module demonstrates a fully functional integration with the **Serbian SEF e-invoice platform** — built in plain PHP with no dependencies.  
It’s designed as a minimal, environment-driven backend service for automated e-invoice creation and validation.

**Core Capabilities**
- 🧾 XML builder for structured invoice payloads  
- 🇷🇸 Serbia issuer example (`issue_serbia.php`)  
- 🌍 Extendable templates for HR / BA / ME / MK  
- 🔐 Env-based configuration for endpoints & credentials  
- 🌐 Plain cURL HTTP client for gateway requests  

---

## 🧠 Tech Stack
| Layer | Technology |
|:--|:--|
| Language | PHP 8+ |
| XML Handling | SimpleXML / DOMDocument |
| HTTP Client | cURL |
| Config | `.env` (dotenv) |
| Standards | UBL 2.1 / SEF 3.14.0 |

---

## ⚙️ Installation & Setup

```bash
git clone https://github.com/BRadivojevic/sef-einvoice-php.git
cd sef-einvoice-php
composer install
cp .env.example .env
php -S localhost:8081 -t examples
