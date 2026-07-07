# Bengkel Koding API

API ini adalah backend Laravel untuk manajemen klinik kecil dengan resource dokter, pasien, poli, dan obat.

## Fitur yang disediakan

- Autentikasi API menggunakan Laravel Sanctum
- Endpoint register, login, logout, dan profile (`/api/me`)
- Resource CRUD untuk `dokter`, `pasien`, `poli`, dan `obat`
- Pagination, filtering, dan sorting untuk list resource
- Rate limiting API dasar untuk login/register dan resource
- Unit/feature tests untuk autentikasi dan API dokter

## Setup

1. Copy file lingkungan:

```bash
copy .env.example .env
```

2. Buat key aplikasi:

```bash
php artisan key:generate
```

3. Jalankan migrasi:

```bash
php artisan migrate
```
```

## Authentication API

### Register

`POST /api/register`

Body:

```json
{
  "nama": "Nama Pasien",
  "email": "user@example.com",
  "password": "password",
  "alamat": "Alamat",
  "no_ktp": "1234567890123456",
  "no_hp": "081234567890"
}
```

Response: token dan data user.

### Login

`POST /api/login`

Body:

```json
{
  "email": "user@example.com",
  "password": "password"
}
```

Response: token dan data user.

### Logout

`POST /api/logout`

Header:

```
Authorization: Bearer <token>
```

### Profile

`GET /api/me`

Header:

```
Authorization: Bearer <token>
```

## Resource API

Semua endpoint resource memerlukan header `Authorization: Bearer <token>`.

### Dokter

- `GET /api/dokter`
- `POST /api/dokter`
- `GET /api/dokter/{id}`
- `PUT /api/dokter/{id}`
- `DELETE /api/dokter/{id}`

Query params:
- `search` untuk pencarian nama/email/no_hp
- `id_poli` untuk filter poli
- `per_page` untuk pagination
- `order_by` dan `order_dir` untuk sorting

### Pasien

- `GET /api/pasien`
- `POST /api/pasien`
- `GET /api/pasien/{id}`
- `PUT /api/pasien/{id}`
- `DELETE /api/pasien/{id}`

Query params: `search`, `per_page`, `order_by`, `order_dir`.

### Poli

- `GET /api/poli`
- `POST /api/poli`
- `GET /api/poli/{id}`
- `PUT /api/poli/{id}`
- `DELETE /api/poli/{id}`

Query params: `search`, `per_page`, `order_by`, `order_dir`.

### Obat

- `GET /api/obat`
- `POST /api/obat`
- `GET /api/obat/{id}`
- `PUT /api/obat/{id}`
- `DELETE /api/obat/{id}`

Query params: `search`, `per_page`, `order_by`, `order_dir`.

## Pengujian

Jalankan test dengan:

```bash
vendor/bin/phpunit
```

atau

```bash
php artisan test
```

## Postman minimal

1. `POST /api/register` untuk membuat pasien baru.
2. `POST /api/login` untuk mendapatkan token.
3. Simpan token di Authorization header.
4. Panggil `GET /api/dokter?per_page=10&search=nama` atau endpoint resource lain.
5. Coba `POST /api/dokter` dengan payload valid.

## Contoh Authorization dan body

Setelah login, tambahkan header berikut pada request yang terproteksi:

```
Authorization: Bearer <token>
```

Contoh body `POST /api/dokter` (JSON):

```json
{
  "nama": "Dr. Contoh",
  "email": "dr.contoh@example.com",
  "password": "dokter123",
  "no_hp": "081234567890",
  "alamat": "Jl. Contoh No.1",
  "id_poli": 1
}
```

Jika `id_poli` belum ada, buat `POST /api/poli` terlebih dahulu.
