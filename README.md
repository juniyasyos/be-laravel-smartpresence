<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Tentang Proyek Ini

---

## 🚀 Panduan Instalasi (Cara Menjalankan)

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda:

### 1. Prasyarat (Prerequisites)

Pastikan perangkat Anda sudah terpasang:

- **PHP** (Versi 8.3++ direkomendasikan)
- **Composer**
- **MySQL** atau database lainnya

### 2. Clone Repositori

```bash
git clone [https://github.com/username-anda/nama-repo.git](https://github.com/username-anda/nama-repo.git)
cd nama-repo
```

### 3.Instalasi Depedensi

Jalankan perintah berikut untuk menginstal library PHP dan paket JavaScript:

```bash

composer install

```

### 4. Konfigurasi Environment

Salin file .env.example menjadi .env:

```bash
cp .env.example .env

```

Setelah itu, buka file .env dan sesuaikan pengaturan database Anda:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=

```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Migrasi Database

Jalankan migrasi untuk membuat tabel-tabel yang diperlukan. Jika ada data awal, tambahkan flag --seed:

```bash
php artisan migrate --seed
```

### 7. Storage Link

Link storage dari public

```bash
php artisan storage:link
```

### 8. Menjalankan Aplikasi

```bash
php artisan serve
```
