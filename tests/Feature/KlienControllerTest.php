<?php

namespace Tests\Feature;

use App\Models\Klien;
use App\Models\Module;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KlienControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'CAB')->value('id');
        $user->save();

        return $user;
    }

    private function makeKlien(array $overrides = []): Klien
    {
        return Klien::create(array_merge([
            'isholding' => true,
            'kodeklinik' => 'BR-'.uniqid(),
            'tipe' => 'A',
            'nama' => 'Cabang Utama',
            'nohp' => '08120000000',
            'email' => 'utama@test.com',
            'alamat' => 'Jl. Utama No. 1',
        ], $overrides));
    }

    public function test_page_requires_access(): void
    {
        $this->authorizedUser();
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/klien');

        $response->assertRedirect(route('error'));
    }

    public function test_can_create_a_new_branch(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/klien/store', [
            'kodeklinik' => 'BR2',
            'nama' => 'Cabang Kedua',
            'tipe' => 'B',
            'nohp' => '08123456789',
            'email' => 'cabang2@test.com',
            'alamat' => 'Jl. Contoh No. 2',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_klien', ['kodeklinik' => 'BR2', 'nama' => 'Cabang Kedua']);
    }

    public function test_create_fails_when_a_required_field_is_missing(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->postJson('/klien/store', [
            'kodeklinik' => 'BR3',
            'nama' => 'Cabang Tanpa Alamat',
            'tipe' => 'B',
            'nohp' => '08123456789',
            'email' => 'cabang3@test.com',
            // 'alamat' sengaja tidak diisi — kolom DB NOT NULL
        ]);

        $response->assertStatus(422);
    }

    public function test_can_create_a_branch_with_satusehat_ihs_id(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/klien/store', [
            'kodeklinik' => 'BR-IHS',
            'nama' => 'Cabang Terdaftar SATUSEHAT',
            'tipe' => 'B',
            'nohp' => '08123456789',
            'email' => 'ihs@test.com',
            'alamat' => 'Jl. Contoh No. 3',
            'ihs_id' => '1000IHS0001',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_klien', ['kodeklinik' => 'BR-IHS', 'ihs_id' => '1000IHS0001']);
    }

    public function test_can_update_a_branch(): void
    {
        $user = $this->authorizedUser();
        $klien = $this->makeKlien();

        $response = $this->actingAs($user)->post("/klien/update/{$klien->id}", [
            'kodeklinik' => $klien->kodeklinik,
            'nama' => 'Nama Baru',
            'tipe' => $klien->tipe,
            'nohp' => $klien->nohp,
            'email' => $klien->email,
            'alamat' => $klien->alamat,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_klien', ['id' => $klien->id, 'nama' => 'Nama Baru']);
    }

    public function test_cannot_delete_the_last_remaining_branch(): void
    {
        $user = $this->authorizedUser();
        $klien = $this->makeKlien();

        $response = $this->actingAs($user)->post("/klien/delete/{$klien->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('m_klien', ['id' => $klien->id]);
    }

    public function test_can_delete_a_branch_when_more_than_one_exists(): void
    {
        $user = $this->authorizedUser();
        $this->makeKlien();
        $secondKlien = $this->makeKlien(['kodeklinik' => 'BR2', 'nama' => 'Cabang Kedua']);

        $response = $this->actingAs($user)->post("/klien/delete/{$secondKlien->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('m_klien', ['id' => $secondKlien->id]);
    }

    public function test_active_list_endpoint_is_reachable_without_cab_access(): void
    {
        $this->makeKlien();

        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = (string) Module::where('code', 'MTU')->value('id'); // akses User, bukan CAB
        $user->save();

        $response = $this->actingAs($user)->get('/klien/getActiveList');

        $response->assertOk();
        $response->assertJsonStructure(['data' => [['id', 'nama', 'kodeklinik']]]);
    }
}
