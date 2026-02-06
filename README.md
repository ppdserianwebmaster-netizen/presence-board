# Presence Board (PPD Serian)

**Presence Board** is a centralized, real-time personnel status and presence tracking system developed specifically for the **Serian District Education Office (PPD Serian)**. 

The system provides a high-performance, reactive dashboard to monitor staff availability, check-in/out status, and departmental presence. It replaces manual tracking with a modern digital solution built on the latest Laravel ecosystem.

---

## 🚀 Tech Stack

* **Framework:** Laravel 12.x
* **Frontend:** Livewire 3.x (Standard Class-based Components)
* **UI Components:** Flux UI
* **PHP Version:** 8.4.17
* **Database:** MySQL 8.0.45 (Hosted via [Aiven](https://aiven.io))
* **Hosting:** [Zeabur](https://zeabur.com)
* **Testing:** Pest PHP

---

## ✨ Key Features

* **Real-time Dashboard:** Instant status updates using Livewire 3 reactivity.
* **Presence Tracking:** Quick check-in/out for all office personnel.
* **Departmental Views:** Organized monitoring for specific units within PPD Serian.
* **Image Processing:** Managed profile and document uploads via Intervention Image.
* **Excel Integration:** Seamless report generation via `maatwebsite/excel`.
* **Secure Access:** Enterprise-ready authentication powered by Laravel Fortify.
* **Production Monitoring:** Real-time log tracking with Laravel Pail.

---

## 📦 Core Dependencies

| Package | Purpose |
| :--- | :--- |
| `livewire/flux` | Modern UI component library. |
| `laravel/fortify` | Backend-agnostic authentication logic. |
| `laravel/boost` | Application performance optimization. |
| `intervention/image-laravel` | Image handling and manipulation. |
| `maatwebsite/excel` | Import/Export functionality for reports. |
| `laravel/mcp` | Model Context Protocol integration. |
| `laravel/pail` | Real-time log monitoring in production. |
| `laravel/roster` | Personnel and schedule management logic. |
| `pestphp/pest` | Elegant testing framework for PHP. |

---

## 🛠 Installation & Development

To get started with local development:

**1. Clone & Install:**
```bash
git clone [https://github.com/your-repo/presence-board.git](https://github.com/your-repo/presence-board.git)
cd presence-board
composer install
npm install && npm run build
```

## 🏛️ Project Governance

Developed for the **Serian District Education Office (PPD Serian)**. This software is intended for official use and internal personnel tracking only.
