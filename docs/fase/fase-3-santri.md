---
id: fase-3-santri
title: Fase 3 - Santri & Akademik
sidebar_position: 3
---

# 🎓 FASE 3: Manajemen Santri & Akademik

---

> **Fokus**: Mengaktifkan peran "Santri", hubungan dengan Wali, dan manajemen kesiswaan.

:::note
Fase ini akan dikembangkan setelah Fase 1 dan Fase 2 selesai.
:::

---

## 3.1 Fitur Utama (Scope)

### 🧑‍🎓 Manajemen Santri (Role Activation)

- Assign Role Santri ke orang
- Generate NIS otomatis
- `histori_status_santri` (Aktif, Cuti, Lulus, Keluar)

---

### 👨‍👩‍👧 Relasi Wali (Parental Relationship)

- Implementasi tabel `relasi_wali_santri`
- **Logic**: Menghubungkan Santri dengan Orang lain (yang mungkin sudah ada di database sebagai Pegawai atau Orang biasa) sebagai Wali Asuh/Bayar

---

### 📚 Dokumen Santri

- Setup `master_dokumen_wajib` kategori Santri (Akta, KK, Rapor SD)
- Implementasi `checklist_dokumen_santri`
- Verifikasi dokumen oleh Panitia/Admin

---

### 📝 Pendaftaran/Mutasi Masuk

- Form input data santri pindahan atau baru

---

## ✅ Definition of Done (Target Selesai Fase 3)

> Admin Akademik bisa mendaftarkan santri, menghubungkan santri dengan walinya (bapak/ibu), dan memverifikasi berkas pendaftaran.

---

## 📋 Checklist Implementasi

- [ ] Santri Activation (Orang → Santri)
- [ ] NIS Generator
- [ ] Histori Status Santri
- [ ] Relasi Wali Santri
- [ ] Master Dokumen Wajib Santri
- [ ] Checklist Dokumen Santri
- [ ] Form Pendaftaran Santri Baru
- [ ] Form Mutasi Masuk

---

[← Fase 2: HRIS](./fase-2-hris)
