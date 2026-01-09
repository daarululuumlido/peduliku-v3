---
id: design-decisions
title: 📐 Keputusan Desain
sidebar_position: 3
---

# 📐 Keputusan Desain (Design Decisions) - PeduliKu v3

> Hasil diskusi dan keputusan arsitektur yang telah disepakati.

---

## 🏛️ Tenancy Model

| Aspek | Keputusan |
|-------|-----------|
| **Model** | Single Tenancy |
| **Arsitektur** | 1 Pesantren = 1 Aplikasi |
| **Alasan** | Lebih simple, mudah maintenance, bisa di-refactor ke multi-tenant jika diperlukan |

---

## 📍 Data Wilayah Indonesia

| Aspek | Detail |
|-------|--------|
| **Package** | [laravolt/indonesia](https://github.com/laravolt/indonesia) |
| **Struktur** | Negara → Provinsi (38) → Kota/Kab (514) → Kecamatan (7.2K) → Desa (84K) |

### Strategi Optimasi untuk 84K+ Data Desa

| Strategi | Implementasi |
|----------|--------------|
| **Lazy Loading Dropdown** | Load bertahap: Provinsi → Kota → Kecamatan → Desa |
| **AJAX Autocomplete** | Query dengan `ILIKE` atau full-text search saat user mengetik |
| **Database Index** | Index pada kolom `nama`, `kode` di PostgreSQL |
| **Redis Cache** | Cache provinsi & kota (static), desa per-kecamatan |
| **Virtual Scroll** | Di Vue, render hanya item yang terlihat di viewport |

```
Flow Pemilihan Alamat:
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Provinsi   │ →  │     Kota     │ →  │  Kecamatan   │ →  │     Desa     │
│  (Dropdown)  │    │  (Dropdown)  │    │  (Dropdown)  │    │  (Searchable)│
│   38 data    │    │   ~20 data   │    │   ~30 data   │    │   ~20 data   │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
                    ↑ Filtered by      ↑ Filtered by       ↑ Filtered by
                      provinsi          kota                 kecamatan
```

---

## 🔐 Authentication (Fleksibel via Config)

| Metode | Status | Keterangan |
|--------|--------|------------|
| **Google OAuth** | ✅ Primary (Default ON) | Login utama dengan Google |
| **Email/Password** | ⚙️ Configurable | Enable/disable via `.env` |
| **WhatsApp OTP** | ⚙️ Configurable | Login via OTP (gateway: Fonnte/Wablas) |

### Contoh Konfigurasi

```php
// config/auth.php
'login_methods' => [
    'google'   => true,  // Always enabled as primary
    'password' => env('AUTH_PASSWORD_ENABLED', false),
    'whatsapp' => env('AUTH_WHATSAPP_ENABLED', false),
],

'whatsapp_gateway' => env('WHATSAPP_GATEWAY', 'fonnte'), // fonnte, wablas, etc.
```

---

## 🏢 Hierarki Organisasi (Dinamis via Database)

Hierarki **tidak di-hardcode** di code, disimpan di database dengan **self-referencing table**:

```
┌─────────────────────────────────────────────────────────────┐
│                    unit_organisasi                          │
├─────────────────────────────────────────────────────────────┤
│  id  │  parent_id  │  nama_unit        │  level  │ urutan  │
├──────┼─────────────┼───────────────────┼─────────┼─────────┤
│  1   │    NULL     │  Yayasan          │    0    │    1    │
│  2   │     1       │  Pendidikan       │    1    │    1    │
│  3   │     1       │  Keuangan         │    1    │    2    │
│  4   │     2       │  SMA IT           │    2    │    1    │
│  5   │     2       │  SMP IT           │    2    │    2    │
│  6   │     4       │  Tata Usaha       │    3    │    1    │
│  7   │     4       │  Kurikulum        │    3    │    2    │
└─────────────────────────────────────────────────────────────┘

Visualisasi Hasil:
├── Yayasan
│   ├── Pendidikan
│   │   ├── SMA IT
│   │   │   ├── Tata Usaha
│   │   │   └── Kurikulum
│   │   └── SMP IT
│   └── Keuangan
```

### Keuntungan

- ✅ Admin bisa tambah/edit/hapus unit via UI tanpa coding
- ✅ Unlimited level hierarki
- ✅ Drag & drop untuk reorder (di Vue)
- ✅ Clone struktur untuk tahun ajaran baru

---

## ✏️ NIP (Nomor Induk Pegawai)

| Aspek | Keputusan |
|-------|-----------|
| **Mode** | Input Manual |
| **Format** | Bebas (sesuai kebijakan pesantren) |
| **Validasi** | Unique constraint di database |
