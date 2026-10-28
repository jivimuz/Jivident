<?php

namespace Tests\Feature;

use App\Models\Klien;
use App\Models\Module;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBranchAssignmentTest extends TestCase
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
        $user->access = (string) Module::where('code', 'MTU')->value('id');
        $user->save();

        return $user;
    }

    public function test_add_form_lists_available_branches(): void
    {
        $user = $this->authorizedUser();
        Klien::create([
            'isholding' => true, 'kodeklinik' => 'BR1', 'tipe' => 'A',
            'nama' => 'Cabang Satu', 'nohp' => '08120000000', 'email' => 'a@a.com', 'alamat' => '-',
        ]);

        $response = $this->actingAs($user)->post('/users/add');

        $response->assertOk();
        $response->assertSee('Cabang Satu');
        $response->assertSee('name="klienfk"', false);
    }

    public function test_creating_a_user_assigns_the_selected_branch(): void
    {
        $user = $this->authorizedUser();
        $klien = Klien::create([
            'isholding' => false, 'kodeklinik' => 'BR2', 'tipe' => 'B',
            'nama' => 'Cabang Dua', 'nohp' => '08120000001', 'email' => 'b@b.com', 'alamat' => '-',
        ]);

        $response = $this->actingAs($user)->post('/users/saveData', [
            'klienfk' => $klien->id,
            'nik' => 'NIK-'.uniqid(),
            'fullname' => 'Pegawai Cabang Dua',
            'fk_joblevel' => 1,
            'fk_jobtype' => 1,
            'fk_designation' => 1,
            'gajipokok' => 0,
            'username' => 'pegawai_'.uniqid(),
            'email' => uniqid().'@test.com',
            'pwd' => 'password123',
            'is_pemeriksapasien' => false,
        ]);

        $response->assertOk();

        $createdUser = User::where('email', 'like', '%@test.com')->latest('id')->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals($klien->id, $createdUser->klienfk);
    }

    public function test_creating_a_user_without_selecting_a_branch_defaults_to_one(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/users/saveData', [
            'nik' => 'NIK-'.uniqid(),
            'fullname' => 'Pegawai Default',
            'fk_joblevel' => 1,
            'fk_jobtype' => 1,
            'fk_designation' => 1,
            'gajipokok' => 0,
            'username' => 'pegawai_'.uniqid(),
            'email' => uniqid().'@test.com',
            'pwd' => 'password123',
            'is_pemeriksapasien' => false,
        ]);

        $response->assertOk();

        $createdUser = User::whereHas('employee', fn ($q) => $q->where('fullname', 'Pegawai Default'))->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals(1, $createdUser->klienfk);
    }
}
