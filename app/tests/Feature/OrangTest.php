<?php

namespace Tests\Feature;

use App\Models\Orang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_orang_index_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('orang.index'));

        $response->assertOk();
    }

    public function test_orang_can_be_created(): void
    {
        $orangData = [
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('orang.store'), $orangData);

        $response->assertRedirect(route('orang.index'));

        $this->assertDatabaseHas('orang', [
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
        ]);
    }

    public function test_nik_must_be_16_digits(): void
    {
        $orangData = [
            'nik' => '12345', // Invalid: not 16 digits
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('orang.store'), $orangData);

        $response->assertSessionHasErrors('nik');
    }

    public function test_nik_must_be_unique(): void
    {
        // Create first orang
        Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        // Try to create another with same NIK
        $response = $this->actingAs($this->user)
            ->post(route('orang.store'), [
                'nik' => '3201010101010001', // Duplicate
                'nama' => 'Jane Doe',
                'gender' => 'P',
                'tanggal_lahir' => '1992-05-20',
                'tempat_lahir' => 'Bandung',
            ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_orang_can_be_updated(): void
    {
        $orang = Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('orang.update', $orang), [
                'nik' => '3201010101010001',
                'nama' => 'John Doe Updated',
                'gender' => 'L',
                'tanggal_lahir' => '1990-01-15',
                'tempat_lahir' => 'Jakarta',
            ]);

        $response->assertRedirect(route('orang.index'));

        $this->assertDatabaseHas('orang', [
            'id' => $orang->id,
            'nama' => 'John Doe Updated',
        ]);
    }

    public function test_orang_can_be_soft_deleted(): void
    {
        $orang = Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('orang.destroy', $orang));

        $response->assertRedirect(route('orang.index'));

        // Should be soft deleted (not in default query)
        $this->assertDatabaseHas('orang', [
            'id' => $orang->id,
        ]);

        // But deleted_at should be set
        $this->assertSoftDeleted('orang', [
            'id' => $orang->id,
        ]);
    }

    public function test_orang_can_be_restored(): void
    {
        $orang = Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        // Soft delete first
        $orang->delete();

        // Restore
        $response = $this->actingAs($this->user)
            ->post(route('orang.restore', $orang->id));

        $response->assertRedirect(route('orang.trashed'));

        // Should be restored (deleted_at = null)
        $this->assertDatabaseHas('orang', [
            'id' => $orang->id,
            'deleted_at' => null,
        ]);
    }

    public function test_orang_can_be_permanently_deleted(): void
    {
        $orang = Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        // Soft delete first
        $orang->delete();

        // Force delete
        $response = $this->actingAs($this->user)
            ->delete(route('orang.force-delete', $orang->id));

        $response->assertRedirect(route('orang.trashed'));

        // Should be permanently deleted
        $this->assertDatabaseMissing('orang', [
            'id' => $orang->id,
        ]);
    }

    public function test_orang_search_by_name(): void
    {
        Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        Orang::create([
            'nik' => '3201010101010002',
            'nama' => 'Jane Smith',
            'gender' => 'P',
            'tanggal_lahir' => '1992-05-20',
            'tempat_lahir' => 'Bandung',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orang.index', ['search' => 'John']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('People/Index')
            ->has('orang.data', 1)
        );
    }

    public function test_orang_filter_by_gender(): void
    {
        Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ]);

        Orang::create([
            'nik' => '3201010101010002',
            'nama' => 'Jane Smith',
            'gender' => 'P',
            'tanggal_lahir' => '1992-05-20',
            'tempat_lahir' => 'Bandung',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orang.index', ['gender' => 'P']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('People/Index')
            ->has('orang.data', 1)
        );
    }

    public function test_custom_attribute_can_be_stored(): void
    {
        $orangData = [
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
            'custom_attribute' => [
                'santri_id' => 'SAN001',
                'kelas' => '10A',
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('orang.store'), $orangData);

        $response->assertRedirect(route('orang.index'));

        $this->assertDatabaseHas('orang', [
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
        ]);

        $orang = Orang::where('nik', '3201010101010001')->first();
        $this->assertEquals([
            'santri_id' => 'SAN001',
            'kelas' => '10A',
        ], $orang->custom_attribute);
    }

    public function test_custom_attribute_is_optional(): void
    {
        $orangData = [
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('orang.store'), $orangData);

        $response->assertRedirect(route('orang.index'));

        $this->assertDatabaseHas('orang', [
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
        ]);
    }

    public function test_custom_attribute_can_be_updated(): void
    {
        $orang = Orang::create([
            'nik' => '3201010101010001',
            'nama' => 'John Doe',
            'gender' => 'L',
            'tanggal_lahir' => '1990-01-15',
            'tempat_lahir' => 'Jakarta',
            'custom_attribute' => [
                'santri_id' => 'SAN001',
                'kelas' => '10A',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('orang.update', $orang), [
                'nik' => '3201010101010001',
                'nama' => 'John Doe',
                'gender' => 'L',
                'tanggal_lahir' => '1990-01-15',
                'tempat_lahir' => 'Jakarta',
                'custom_attribute' => [
                    'santri_id' => 'SAN002',
                    'kelas' => '11B',
                    'status' => 'aktif',
                ],
            ]);

        $response->assertRedirect(route('orang.index'));

        $orang->refresh();
        $this->assertEquals([
            'santri_id' => 'SAN002',
            'kelas' => '11B',
            'status' => 'aktif',
        ], $orang->custom_attribute);
    }
}
