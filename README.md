# ShamaSMS

> Reliable bulk SMS for businesses, schools, churches, SACCOs, and developers in Uganda.

**Live site:** [https://shamasms.com](https://shamasms.com)

---

## What is ShamaSMS?

ShamaSMS is a full-featured bulk SMS web platform built with Laravel 13, Livewire 3, and Tailwind CSS 4. It allows individuals, businesses, and developers to send SMS messages to any phone in Uganda — with no smartphone or internet connection required on the recipient side.

---

## Features

- **Bulk SMS** — send one message to hundreds or thousands of recipients at once
- **Personalized SMS** — use placeholders (`@@name@@`, `@@var1@@`–`@@var5@@`) to inject unique values per recipient
- **Scheduled & recurring sends** — schedule at a specific date/time and repeat on chosen weekdays
- **Phonebook & contact groups** — save and reuse contact lists without re-uploading
- **Delivery reports** — per-recipient delivery status for every message sent
- **Me 2 U credit transfers** — share SMS credits with another user instantly
- **REST API (V1 & V2)** — integrate SMS into any external system; V2 uses secure API keys
- **Sandbox mode** — test API integrations without spending credits or sending real SMS
- **Iotec payment integration** — buy SMS credits directly from the dashboard
- **Admin panel** — manage SMS gateway integrations and platform settings

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4, Laravel 13 |
| Frontend | Livewire 3, Tailwind CSS 4, Vite 8 |
| Database | MySQL 8 |
| Cache / Queue | Redis, Database queue driver |
| SMS Gateway | UGSMS (configurable via admin) |
| Payments | Iotec Payment Gateway |
| Web Server | Nginx (via mailcow proxy) + PHP-FPM |
| SSL | Let's Encrypt (auto-renewing) |

---

## API

ShamaSMS exposes a REST JSON API for developers. Full documentation is available at:

**[https://shamasms.com/developers](https://shamasms.com/developers)**

### Quick example (V2 — API key)

```bash
curl -X POST https://shamasms.com/api/v2/sms/send \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_API_KEY" \
 -d '{"sender_id":"MYAPP","message":"Hello @@name@@!","personalized":true,"recipients":[{"phone":"0700000000","name":"Alice"}],"sandbox":false}'
```

---

## Local Development Setup

```bash
# Clone and install
git clone https://github.com/microsoftjulius/shamasms.git
cd shamasms
composer install
npm install && npm run dev

# Configure environment
cp .env.example .env
php artisan key:generate

# Set up database (update .env with your DB credentials first)
php artisan migrate

# Start dev server
php artisan serve
```

### Environment variables

Copy `.env.example` to `.env` and fill in:

| Variable | Description |
|---|---|
| `DB_*` | MySQL connection details |
| `UGSMS_API_KEY` | SMS gateway API key |
| `UGSMS_BASE_URL` | SMS gateway base URL |
| `UGSMS_SANDBOX` | Set `true` for sandbox mode |
| `IOTEC_API_KEY` | Iotec payment gateway key |
| `MAIL_*` | Mail settings for email verification |

---

## Who is it for?

- **Businesses** — promotions, order confirmations, customer updates
- **Schools & universities** — fee reminders, results, parent communication
- **Churches & NGOs** — event announcements, member communication
- **SACCOs & MFIs** — loan repayment reminders, balance alerts
- **Clinics & hospitals** — appointment reminders, health alerts
- **Developers** — integrate SMS into any app via the REST API

---

## License

Proprietary — all rights reserved. This codebase is not open source.

---

*Built with Laravel 13 · Deployed on Ubuntu 24.04 · Live at [shamasms.com](https://shamasms.com)*
