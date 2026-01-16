# AGENTS.md - Guidelines for Coding Agents

## Build, Lint, and Test Commands

### Essential Commands
- `composer test` - Run all PHPUnit tests
- `php artisan test --filter TestMethodName` - Run single test
- `php artisan test --filter ClassName` - Run all tests in a class
- `vendor/bin/phpunit tests/Feature/OrangTest.php` - Run specific test file
- `composer run build` - Build frontend assets (Vite)
- `npm run build` - Alternative build command
- `npm run dev` - Start development server with Vite HMR
- `composer run dev` - Full dev stack (PHP server, queue, Vite)

### Code Formatting
- `./vendor/bin/pint` - Format PHP code (Laravel Pint, PSR-12)
- `./vendor/bin/pint app/Console/Commands/SimpleSantriImportCommand.php` - Format single file
- **Always run Pint after code changes**

### Audit Log Management
- `php artisan audit:cleanup` - Clean up audit logs older than retention period
- `php artisan audit:cleanup --days=30` - Clean up logs older than 30 days

## Project Documentation

Refer to `docs/` directory for project-specific documentation:

### Core Documentation
| Document | Path | Purpose |
|----------|------|---------|
| **Project Introduction** | `docs/intro.md` | Roadmap, current focus, ERD overview |
| **Tech Stack** | `docs/tech-stack.md` | Architecture, technologies, why Inertia+Vue |
| **Design Decisions** | `docs/design-decisions.md` | Tenancy model, address data strategy, auth config, org hierarchy |

### Phase Documentation
| Phase | Path | Focus Area | Status |
|-------|------|------------|--------|
| **Fase 1** | `docs/fase/fase-1-fondasi.md` | Foundation, auth, people management | 🔵 CURRENT |
| **Fase 2** | `docs/fase/fase-2-hris.md` | HRIS, employee management, organization structure | 🔵 IN PROGRESS |
| **Fase 3** | `docs/fase/fase-3-santri.md` | Student management, wali relationships, academics | ⚪ LATER |

### Module Breakdown by Phase

#### Fase 1: Foundation & People Management
- **System Bootstrapping**: Database seeder, Laravolt Indonesia integration, Redis setup
- **Authentication & RBAC**: Google OAuth, configurable password/WhatsApp login, role & permission management
- **People Management**: CRUD Orang with NIK validation, Kartu Keluarga, lazy-loading address dropdown
- **Global File Storage**: Polymorphic `lampiran_dokumen` table for all file attachments

#### Fase 2: HRIS & Organization
- **MODUL 2.1 - Struktur Organisasi**: Dynamic hierarchy via `unit_organisasi` (self-referencing), period cloning, tree view visualization
- **MODUL 2.2 - Profil Pegawai**: Employee activation (Orang → Pegawai), NIP input, position history (`histori_jabatan_pegawai`)
- **MODUL 2.3 - Riwayat & Catatan**: Education history, family history, ibadah history (Umroh/Haji), custom event logger
- **MODUL 2.4 - Dokumen**: Document checklist, mandatory document master, expiry reminders

#### Fase 3: Santri & Academic
- **Santri Activation**: Orang → Santri role, auto-generated NIS, status history (Aktif/Cuti/Lulus/Keluar)
- **Wali Relationships**: Connect santri with wali (may be existing pegawai or regular orang)
- **Dokumen Santri**: Document verification for registration (Akta, KK, Rapor SD)

### Important Context

#### Architecture & Design
- **Single-tenancy architecture**: 1 pesantren = 1 app (can be refactored to multi-tenant later)
- **Address data strategy**: Use Laravolt Indonesia package (84K+ desa data)
  - Lazy-loading dropdown: Provinsi → Kota → Kecamatan → Desa
  - Use AJAX autocomplete with `ILIKE` or full-text search
  - Index columns `nama`, `kode` in PostgreSQL
  - Cache static data (provinsi, kota) in Redis
- **Organization hierarchy**: Dynamic via database (self-referencing `unit_organisasi`)
  - Tree structure: Yayasan → Pendidikan → SMA IT → Tata Usaha
  - Unlimited hierarchy levels via `parent_id`
  - Supports drag & drop reordering and period cloning
- **Authentication**: Google OAuth primary, password/WhatsApp configurable via `.env`
- **NIP**: Manual input, flexible format, unique constraint in database

#### Current Development Focus
- **Fase 1** (Foundation & People Management): Core infrastructure, authentication, CRUD Orang with NIK validation, Kartu Keluarga
- **Fase 2** (HRIS & Organization): Employee activation, organization structure, position history, education history, document checklist
- **Fase 3** (Santri & Academic): Coming after Fase 1 & 2 completion

#### Database Relationships
- ORANG can become PERAN_PEGAWAI (employee) or PERAN_SANTRI (student)
- One person can hold multiple positions simultaneously (e.g., Staff in multiple units)
- All main entities use UUID primary keys with soft deletes
- Polymorphic relationships for documents (lampiran_dokumen)

## PHP/Laravel Guidelines

### Imports and Namespaces
- Order: Illuminate classes → External packages → App\ classes
- Use alphabetical order within each group
- Example:
  ```php
  use Carbon\Carbon;
  use Illuminate\Console\Command;
  use Illuminate\Support\Facades\DB;
  use Laravolt\Indonesia\Models\Village;
  use App\Models\Orang;
  ```

### Type System
- PHP 8.2+ required
- Always use typed return types: `public function handle(): void`
- Use typed parameters: `public function store(Request $request): Response`
- Models use UUID primary keys (HasUuids trait)
- Use enum for fixed values: `enum('gender', ['L', 'P'])`

### Naming Conventions
- **Classes**: PascalCase (KartuKeluargaController, Orang)
- **Methods**: camelCase (store, update, destroy, cleanAlamatLengkap)
- **Variables**: camelCase ($santriData, $peopleToInsert)
- **Database tables**: snake_case (kartu_keluarga, orang, alamat)
- **Vue components**: PascalCase (ModuleLayout, AddressSearchSelect)
- **Routes**: kebab-case (admin.kartu-keluarga.index)

### Code Style
- 4-space indentation (enforced by .editorconfig)
- LF line endings
- No trailing whitespace
- Maximum line length: ~120 characters
- **NO CODE COMMENTS** unless explicitly requested
- Use now() instead of Carbon::now()

### Database Operations
- Use DB::transaction() for multi-table operations
- Always rollback on exceptions
- Chunk large inserts (100 records per batch)
- Use foreign keys with onDelete('set null') or 'cascade'
- Example:
  ```php
  DB::beginTransaction();
  try {
      DB::table('alamat')->insert($data);
      DB::commit();
  } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
  }
  ```

### Error Handling
- Try-catch blocks for external operations (API calls, file operations)
- Log errors, don't expose sensitive data to users
- Use $this->error() in Commands for error messages
- Return early on validation failures

### Validation
- Always validate user input in controllers
- Use Rule::unique() for unique field updates
- Example:
  ```php
  $validated = $request->validate([
      'nik' => ['required', 'string', 'size:16', Rule::unique('orang', 'nik')->ignore($orang->id)],
      'nama' => ['required', 'string'],
  ]);
  ```

## Vue.js Guidelines

### Component Structure
- Use `<script setup>` syntax
- Props always typed with defineProps()
- Emits always defined with defineEmits()
- Reactive state with ref() or reactive()
- Example:
  ```javascript
  const props = defineProps({
      modelValue: { type: String, default: null },
  });
  const emit = defineEmits(['update:modelValue']);
  ```

### Imports
- Order: Vue → Inertia → Other libraries → Local components
- Example:
  ```javascript
  import { ref, watch } from 'vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import ModuleLayout from '@/Layouts/ModuleLayout.vue';
  ```

### Styling
- Use Tailwind CSS classes
- Dark mode support: dark: prefix for dark mode variants
- Use existing components (PrimaryButton, TextInput, etc.)
- Don't add custom CSS in style tags unless necessary

### Naming
- Components: PascalCase
- Variables: camelCase
- Constants: SCREAMING_SNAKE_CASE

## Testing Guidelines

- Use RefreshDatabase trait for database tests
- Create test users with factories: `User::factory()->create()`
- Test both success and failure cases
- Use actingAs() for authentication
- Test validation errors separately
- Example:
  ```php
  $response = $this->actingAs($this->user)
      ->post(route('orang.store'), $data);
  $response->assertSessionHasErrors('nik');
  ```

## Architecture Patterns

- Use Laravel Resource Controllers for CRUD operations
- Model relationships defined with type hints
- Soft deletes enabled on main entities (Orang, etc.)
- Use Inertia for SPA with Vue.js
- Route model binding for show/edit/update/destroy actions

## Key Libraries & Technologies

### Backend
| Library | Version | Purpose |
|---------|---------|---------|
| Laravel | 12.x | Main framework |
| PHP | 8.3+ | Minimum requirement |
| PostgreSQL | 16.x | Database with JSONB support |
| Redis | 7.x | Cache, session, queue |
| Laravel Sanctum | - | API authentication for mobile |
| Spatie Permission | - | RBAC (Role-Based Access Control) |
| Laravolt Indonesia | - | Indonesia region data (84K+ desa) |
| Pest PHP | - | Modern testing framework |

### Frontend
| Library | Purpose |
|---------|---------|
| Inertia.js | Bridge Laravel ↔ Vue (no manual API) |
| Vue 3 | Frontend framework with Composition API |
| Vite | Fast HMR & build tool |
| Tailwind CSS 4 | Utility-first CSS |
| Pinia | Vue 3 state management |
| TanStack Table | Powerful tables with sorting/filter/pagination |
| PrimeVue / Naive UI | Premium component library (optional) |
| Lucide / Heroicons | Icon library |
| ApexCharts / Chart.js | Dashboard & reporting |

### Why Inertia + Vue 3?
- SPA experience without full page reload
- No manual API needed (server-side auth preserved)
- SEO friendly (SSR support if needed)
- Fully customizable UI
- Lightweight performance
- Real-time reactivity
- Future-proof for mobile API extension

## Foreign Key Constraints
- Always respect insertion order: addresses → people → family cards → relationships
- UUIDs used for all primary keys
- Use morphMany for polymorphic relationships (e.g., documents)
