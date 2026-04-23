<p align="center"><img src="https://github.com/shafkatkhan/senco-support-software-artefact/blob/f9c98e2126d2681b23e0a11a329a6058eb028509/public/img/logo.svg" width="300" alt="EduSen Logo"></p>

# EduSen: An AI-powered SENCO Support System

A web-based pupil data management system built for Special Educational Needs Coordinators (SENCOs). The application streamlines the recording, retrieval, and reporting of pupil SEN data, with an AI-powered data extraction pipeline that lets staff upload documents, images, or voice recordings and have form fields pre-populated automatically.

---

## Features

- **Pupil Profiles** — store and manage pupil personal details, school history, family members, diagnoses, medications, accommodations, and progression records
- **AI Data Extraction** — upload a PDF, image, or audio recording; the system transcribes and/or extracts structured data and pre-populates the relevant form for staff to review before saving
- **Multi-provider LLM Support** — configurable to use OpenAI or Google Gemini via a single abstraction layer
- **Reports and Exports** — generate per-pupil PDF reports and cohort CSV/Excel exports with AJAX-powered filters
- **Role-based Access Control** — user groups with granular per-permission gates stored in the database
- **Multi-Factor Authentication** — TOTP-based MFA with QR code enrolment; policy enforced globally by an administrator
- **Installation Wizard** — guided first-run setup: database connection, admin account creation, language selection, and LLM provider configuration
- **Internationalisation** — language key system with right-to-left layout support; LLM-assisted translation available during installation
- **Automated Backups** — one-click database backup with download and restore, powered by `mysqldump-php`
- **Secure File Storage** — attachments served through authenticated routes only; files deleted automatically when their database record is removed
- **Import** — bulk pupil import from Excel/CSV

---

## Technology Stack and Packages

| Layer | Technology |
|---|---|
| Backend framework | Laravel 12 (PHP 8.2+) |
| Database | MySQL |
| Frontend | Bootstrap 5.3, jQuery, DataTables, Select2 |
| PDF generation | barryvdh/laravel-dompdf |
| Excel/CSV export & import | maatwebsite/excel |
| MFA | pragmarx/google2fa-laravel + bacon/bacon-qr-code |
| Database backup | ifsnop/mysqldump-php |

---

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL (or MariaDB equivalent)
- An API key for at least one supported LLM provider (OpenAI, Mistral, or Google Gemini), to use AI features

> The application can be self-hosted on low-cost hardware such as a Raspberry Pi 4, as outlined in the [Installation Guide](docs/installation-guide.pdf).

---

## Installation

### 1. Clone and install dependencies

```bash
git clone https://github.com/shafkatkhan/senco-support-software-artefact.git senco
cd senco
composer install --optimize-autoloader
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Start the application

```bash
php artisan serve
```

Navigate to `http://localhost:8000`. You will be redirected to the **Installation Wizard** on first run, which will guide you through:

1. Initialising the database connection and creating database tables
2. Selecting the application language
3. Selecting and configuring your LLM provider (API key entry)

No manual `.env` editing is required, as all the configurations are completed through the wizard UI.

---

## Testing

```bash
php artisan test
```

The test suite uses Laravel's feature testing framework and achieves 100% code coverage across all controller endpoints. Tests cover permission gates, validation constraints, and database operations including backup and restore flows.

---

## License

This project is licensed under the [GNU Affero General Public License v3.0](https://www.gnu.org/licenses/agpl-3.0.en.html) (AGPL-3.0).

This software is designed for non-commercial use by schools and educational institutions.