# Presence Board (PPD Serian)

**Presence Board** is a centralized, real-time personnel status and presence tracking system developed specifically for the **Serian District Education Office (PPD Serian)**. 

The system provides a high-performance, reactive dashboard to monitor staff availability, check-in/out status, and departmental presence. It replaces manual tracking with a modern digital solution built on Laravel 12 and Livewire 3.

---

## ✨ Key Features

* **Real-time Dashboard:** Instant status updates using Livewire 3 reactivity.
* **Presence Tracking:** Quick check-in/out for all office personnel.
* **Departmental Views:** Organized monitoring for specific units within PPD Serian.
* **Excel Integration:** Seamless report generation via `maatwebsite/excel`.
* **Secure Access:** Enterprise-ready authentication powered by Laravel Fortify.
* **Modern UI:** Polished interface utilizing the Flux UI component library.

---

## 🛠 Tech Stack

* **Framework:** Laravel 12.x
* **Frontend:** Livewire 3.x (Standard Class-based Components)
* **PHP Version:** 8.3.30 (Platform-locked in `composer.json`)
* **Hosting:** [Zeabur](https://zeabur.com)
* **Database:** Managed MySQL/PostgreSQL via [Aiven](https://aiven.io)
* **UI Components:** Flux UI
* **Testing:** Pest PHP

---

## 📦 Core Dependencies

| Package | Purpose |
| :--- | :--- |
| `laravel/fortify` | Backend-agnostic authentication logic. |
| `livewire/flux` | Modern UI component library for Livewire. |
| `maatwebsite/excel` | Import/Export functionality for attendance reports. |
| `laravel/pail` | Real-time log monitoring in production. |
| `pestphp/pest-plugin-laravel` | Feature and Unit testing suite. |

---

## ⚙️ Installation & Development

To get started with local development:

1.  **Clone & Install:**
    ```bash
    git clone [https://github.com/your-repo/presence-board.git](https://github.com/your-repo/presence-board.git)
    cd presence-board
    composer install
    npm install
    ```

2.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Ensure your `.env` points to your **Aiven** database credentials.*

3.  **Database Migration:**
    ```bash
    php artisan migrate --seed
    ```

4.  **Launch:**
    ```bash
    npm run dev
    php artisan serve
    ```

---

## 🚀 Deployment

This project is configured for deployment on **Zeabur**.

* **Environment:** Ensure the Zeabur service is set to use PHP 8.3.
* **Database:** Aiven connection strings should be stored in Zeabur's environment variables.
* **Optimization:**
    ```bash
    php artisan optimize
    php artisan view:cache
    ```

---

## 🏛️ Project Governance

Developed for the **Serian District Education Office (PPD Serian)**. This software is intended for official use and internal personnel tracking only.
