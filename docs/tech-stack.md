---
id: tech-stack
title: 🛠️ Tech Stack
sidebar_position: 2
---

# 🛠️ Tech Stack - PeduliKu v3

---

## 🏛️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           PEDULIKU v3 ARCHITECTURE                          │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│   ┌─────────────────┐         ┌─────────────────┐                          │
│   │   WEB CLIENT    │         │  MOBILE CLIENT  │                          │
│   │   (Inertia +    │         │    (Flutter)    │                          │
│   │    Vue 3)       │         │  Android & iOS  │                          │
│   └────────┬────────┘         └────────┬────────┘                          │
│            │                           │                                    │
│            │  Inertia Protocol         │  REST API                         │
│            │                           │                                    │
│            └───────────┬───────────────┘                                   │
│                        ▼                                                    │
│            ┌───────────────────────┐                                       │
│            │   LARAVEL 12 BACKEND  │                                       │
│            │   (API + Web Server)  │                                       │
│            └───────────┬───────────┘                                       │
│                        │                                                    │
│            ┌───────────┴───────────┐                                       │
│            ▼                       ▼                                        │
│   ┌─────────────────┐    ┌─────────────────┐                               │
│   │   PostgreSQL    │    │      Redis      │                               │
│   │   (Database)    │    │  (Cache/Queue)  │                               │
│   └─────────────────┘    └─────────────────┘                               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Backend & Database

| Komponen | Teknologi | Versi | Keterangan |
|----------|-----------|-------|------------|
| **Framework** | Laravel | 12.x | PHP framework + REST API untuk mobile |
| **PHP Version** | PHP | 8.3+ | Minimum requirement untuk Laravel 12 |
| **Database** | PostgreSQL | 16.x | JSONB, Full-text search, optimized untuk 10K+ data |
| **Cache** | Redis | 7.x | Session, queue, dan cache management |
| **Queue** | Laravel Horizon | - | Dashboard untuk monitoring queue jobs |
| **API Auth** | Laravel Sanctum | - | Token-based authentication untuk mobile |

---

## Frontend Web (Inertia + Vue 3)

| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **Bridge** | Inertia.js | Menghubungkan Laravel dengan Vue tanpa API manual |
| **Framework** | Vue 3 | Composition API, reactive, performant |
| **Build Tool** | Vite | Fast HMR & build tool modern |
| **CSS Framework** | Tailwind CSS 4 | Utility-first CSS |
| **UI Components** | PrimeVue / Naive UI | Component library premium (opsional) |
| **Icons** | Lucide / Heroicons | Icon library modern |
| **Charts** | ApexCharts / Chart.js | Untuk dashboard & reporting |
| **State Management** | Pinia | Vue 3 state management |
| **Table** | TanStack Table | Powerful table dengan sorting, filter, pagination |

---

## Frontend Mobile (Flutter) — *Fase Pengembangan Lanjutan*

| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **Framework** | Flutter | Cross-platform (Android, iOS, Web) |
| **State Management** | Riverpod / Bloc | State management untuk Flutter |
| **HTTP Client** | Dio | HTTP client dengan interceptor |
| **Local Storage** | Hive / SQLite | Offline-first capability |
| **Push Notification** | Firebase FCM | Notifikasi push |

---

## DevOps & Tools

| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **Version Control** | Git | Source code management |
| **Package Manager** | Composer & NPM | PHP & JS dependencies |
| **Local Development** | Laravel Herd / Sail | Development environment |
| **Testing Backend** | Pest PHP | Modern testing framework untuk Laravel |
| **Testing Frontend** | Vitest | Unit testing untuk Vue |
| **Code Quality** | Laravel Pint + ESLint | Code style fixer PHP & JS |

---

## Kenapa Inertia + Vue 3?

| Keunggulan | Penjelasan |
|------------|------------|
| ✅ **SPA Experience** | Navigasi instan tanpa full page reload |
| ✅ **No API Manual** | Inertia handle komunikasi Laravel ↔ Vue |
| ✅ **Server-side Auth** | Tetap pakai Laravel middleware & session |
| ✅ **SEO Friendly** | SSR support jika dibutuhkan |
| ✅ **Fully Customizable** | Bebas desain UI sesuai keinginan |
| ✅ **Performa Ringan** | Vue 3 sangat efisien untuk data besar |
| ✅ **Reactivity** | Update UI real-time tanpa refresh |
| ✅ **Future-proof** | Mudah extend ke mobile via API |
