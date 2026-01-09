---
id: fase-2-hris
title: Fase 2 - HRIS & Organisasi
sidebar_position: 2
---

# 🏢 FASE 2: HRIS & Organisasi Dinamis

---

> **Tujuan Utama**: Mengubah data "Orang" (dari Fase 1) menjadi "Pegawai" yang memiliki jabatan, NIP, riwayat karir, dan catatan khusus dalam struktur organisasi pesantren yang bisa berubah setiap periode.

---

## 📊 MODUL 2.1: Struktur Organisasi Dinamis (The Foundation)

Fitur ini memungkinkan pesantren mengubah struktur organisasi (misal: memecah divisi, mengganti nama unit) di tahun ajaran baru **tanpa merusak data histori** tahun sebelumnya.

### A. Tabel Database Pendukung

| Tabel | Kolom | Fungsi |
|-------|-------|--------|
| `struktur_organisasi` | `id`, `nama_periode`, `tgl_mulai`, `tgl_selesai`, `is_active` | Wadah utama. Hanya satu periode yang boleh `is_active = 1` |
| `unit_organisasi` | `id`, `struktur_id` (FK), `parent_id` (Self-join), `nama_unit`, `kode_unit`, `level_hierarki` | Menyimpan hierarki (Yayasan → Pendidikan → SMA → Tata Usaha) |
| `master_jabatan` | `id`, `unit_organisasi_id` (FK), `nama_jabatan`, `is_pimpinan`, `kuota_sdm` | Posisi spesifik yang tersedia dalam unit tersebut |

### B. Kebutuhan Fungsional (Fitur)

| Kode | Fitur | Deskripsi |
|------|-------|-----------|
| **ORG-01** | Create & Clone Period | Admin bisa membuat periode baru dari nol. Fitur **Clone**: menyalin seluruh struktur dari periode 2024 ke 2025, lalu melakukan penyesuaian |
| **ORG-02** | Organization Tree View | Visualisasi struktur berbentuk bagan pohon (Parent-Child) agar mudah melihat alur komando |

---

## 👔 MODUL 2.2: Manajemen Profil Pegawai (The Actor)

Mengaktifkan peran pegawai pada data orang yang sudah ada.

### A. Tabel Database Pendukung

| Tabel | Kolom | Fungsi |
|-------|-------|--------|
| `peran_pegawai` | `id`, `orang_id` (FK ke Fase 1), `nip`, `tgl_bergabung`, `status_kepegawaian` (Tetap/Kontrak/Honorer), `is_active` | Menyimpan data kepegawaian |
| `histori_jabatan_pegawai` | `id`, `peran_pegawai_id`, `master_jabatan_id` (FK), `no_sk`, `tgl_mulai`, `tgl_selesai`, `keterangan_mutasi` | Mencatat perjalanan karir (Mutasi, Promosi, Demosi) |

### B. Kebutuhan Fungsional (Fitur)

| Kode | Fitur | Deskripsi |
|------|-------|-----------|
| **EMP-01** | Employee Activation (Assign Role) | Mencari data orang berdasarkan NIK/Nama. Jika belum ada, create orang baru. Jika sudah ada (misal: dia Wali Santri), langsung aktifkan peran pegawainya |
| **EMP-02** | NIP Input | Input NIP secara manual (sesuai kebijakan pesantren) |
| **EMP-03** | Mutasi & Promosi | Saat pegawai pindah jabatan, sistem otomatis mengisi `tgl_selesai` pada jabatan lama dan membuat baris baru di `histori_jabatan_pegawai` dengan `tgl_mulai` hari ini |

---

## 📝 MODUL 2.3: Riwayat Hidup & Catatan Khusus (The History)

Mencatat detail kehidupan pegawai yang mempengaruhi HR, termasuk fitur **Custom Attributes**.

### A. Tabel Database Pendukung

| Tabel | Kolom | Fungsi |
|-------|-------|--------|
| `riwayat_pendidikan` | `jenjang` (SD/SMP/SMA/S1/S2), `institusi`, `jurusan`, `tahun_lulus`, `nilai_akhir` | Riwayat pendidikan formal |
| `riwayat_keluarga_pegawai` | `status` (Menikah/Cerai/Lajang), `nama_pasangan`, `jumlah_anak`, `tgl_perubahan_status` | Mencatat sejarah status pernikahan |
| `catatan_kepegawaian` | `id`, `peran_pegawai_id`, `kategori` (ENUM), `judul`, `deskripsi`, `tgl_kejadian`, `poin` | Generic log / Custom Attributes |

### Kategori Catatan Kepegawaian

| Kategori | Contoh Penggunaan |
|----------|-------------------|
| `PRESTASI` | Penghargaan, sertifikasi |
| `PELANGGARAN` | SP 1, SP 2, SP 3 |
| `IBADAH` | Izin Umroh, Haji |
| `KESEHATAN` | Cuti sakit panjang |
| `LAINNYA` | Catatan lain-lain |

### B. Kebutuhan Fungsional (Fitur)

| Kode | Fitur | Deskripsi |
|------|-------|-----------|
| **REC-01** | Custom Event Logger | Form input fleksibel untuk mencatat kejadian |
| **REC-02** | Education History Tracker | Input data ijazah. Jika pegawai lanjut S2 saat bekerja, data S2 ditambahkan tanpa menghapus data S1 |

---

## 📄 MODUL 2.4: Dokumen & Administrasi (The Compliance)

Memastikan kelengkapan berkas administrasi pegawai.

### A. Tabel Database Pendukung

| Tabel | Fungsi |
|-------|--------|
| `master_dokumen_wajib` | Filter `wajib_untuk = 'PEGAWAI'` |
| `checklist_dokumen_pegawai` | Daftar dokumen yang harus dikumpulkan per pegawai |
| `lampiran_dokumen` | Polymorphic table untuk menyimpan file |

### B. Kebutuhan Fungsional (Fitur)

| Kode | Fitur | Deskripsi |
|------|-------|-----------|
| **DOC-01** | HR Document Checklist | Sistem otomatis menagih dokumen saat seseorang jadi pegawai: KTP, NPWP, Ijazah Terakhir, Sertifikat Pendidik, SKCK |
| **DOC-02** | Expiry Reminder *(Opsional)* | Notifikasi jika ada dokumen yang punya masa berlaku habis (misal: SIM Driver, STR Tenaga Medis) |

---

## 🔄 Rangkuman Alur Kerja User (User Flow)

```mermaid
flowchart TD
    A[1. Admin Setup] --> B[2. Recruitment]
    B --> C[3. Penempatan]
    C --> D[4. Lengkapi Profil]
    D --> E[5. Operasional Harian]
    
    A --> |Membuat Struktur Organisasi 2025,<br/>Unit SMA IT,<br/>Jabatan Guru Matematika| A
    B --> |Cek NIK pelamar,<br/>Klik 'Jadikan Pegawai'| B
    C --> |Tempatkan di jabatan<br/>'Guru Matematika'| C
    D --> |Input Ijazah S1,<br/>Status Menikah| D
```

### Detail Operasional Harian

| Timeline | Kejadian | Aksi |
|----------|----------|------|
| Bulan depan | Pegawai terlambat | Input ke `catatan_kepegawaian` → SP 1 |
| Tahun depan | Pegawai Umroh | Input ke `catatan_kepegawaian` → Izin Umroh |
| Tahun depannya lagi | Promosi | Update jabatan → "Kepala Sekolah" (Mutasi) |

---

## ✅ Definition of Done (Target Selesai Fase 2)

> Admin HR bisa membuat struktur organisasi, mengaktifkan pegawai dari data orang, menempatkan di jabatan, mencatat riwayat pendidikan/keluarga, dan mengelola catatan kepegawaian.

---

## 📋 Checklist Implementasi

### MODUL 2.1: Struktur Organisasi
- [ ] CRUD Struktur Organisasi (Periode)
- [ ] CRUD Unit Organisasi (Tree structure)
- [ ] CRUD Master Jabatan
- [ ] Clone Period functionality
- [ ] Tree View visualization (Vue component)

### MODUL 2.2: Profil Pegawai
- [ ] Employee Activation (Orang → Pegawai)
- [ ] NIP input dengan unique validation
- [ ] Histori Jabatan tracking
- [ ] Mutasi & Promosi workflow

### MODUL 2.3: Riwayat & Catatan
- [ ] CRUD Riwayat Pendidikan
- [ ] CRUD Riwayat Keluarga
- [ ] CRUD Catatan Kepegawaian (Custom Event Logger)

### MODUL 2.4: Dokumen
- [ ] Master Dokumen Wajib (seed data)
- [ ] Checklist Dokumen per Pegawai
- [ ] Upload & Verifikasi Dokumen
- [ ] Expiry Reminder (optional)

---

[← Fase 1: Fondasi](./fase-1-fondasi) | [Fase 3: Santri →](./fase-3-santri)
