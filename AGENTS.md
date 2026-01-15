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

## Key Libraries
- Laravel 12.x framework
- Vue 3 with Inertia.js
- Tailwind CSS 4.0
- Laravolt Indonesia for address data
- Spatie Permission for RBAC
- Laravel Sanctum for API authentication

## Foreign Key Constraints
- Always respect insertion order: addresses → people → family cards → relationships
- UUIDs used for all primary keys
- Use morphMany for polymorphic relationships (e.g., documents)
