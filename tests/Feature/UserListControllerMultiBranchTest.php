<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Module;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Regresi audit keamanan: UserListController tidak men-scope satu pun lookup
 * by-id ke klienfk — staf dari cabang lain bisa lihat daftar staf, ganti
 * password, aktif/nonaktifkan, atau hapus akun milik cabang yang bukan
 * miliknya. Ini CRITICAL karena bisa dipakai untuk mengambil alih akun
 * (ganti password lalu login sebagai user itu).
 */
class UserListControllerMultiBranchTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(int $klienfk): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = (string) Module::where('code', 'MTU')->value('id');
        $user->save();

        return $user;
    }

    private function makeUserInBranch(int $klienfk, string $username): User
    {
        $employee = Employee::create(['fullname' => 'Staf '.$username, 'nik' => 'NIK-'.uniqid()]);

        return User::forceCreate([
            'klienfk' => $klienfk,
            'fk_employee' => $employee->id,
            'username' => $username,
            'display_name' => 'Staf '.$username,
            'email' => $username.'@test.com',
            'password' => bcrypt('rahasia-asli'),
            'access' => '1',
            'is_active' => true,
        ]);
    }

    public function test_get_list_only_shows_users_from_the_same_branch(): void
    {
        $this->authorizedUser(1);
        $this->makeUserInBranch(1, 'staf_cabang_1');
        $this->makeUserInBranch(2, 'staf_cabang_2');

        $response = $this->actingAs(User::first())->post('/users/getList');

        $response->assertOk();
        $usernames = collect($response->json('data'))->pluck('username');
        $this->assertTrue($usernames->contains('staf_cabang_1'));
        $this->assertFalse($usernames->contains('staf_cabang_2'));
    }

    public function test_cannot_change_password_of_a_user_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $victim = $this->makeUserInBranch(2, 'korban_cabang_2');

        $response = $this->actingAs(User::first())->post('/users/updateData', [
            'id' => $victim->id,
            'pwd' => 'password-diambil-alih',
            'nik' => 'NIK-UPDATED',
            'fullname' => 'Nama Diubah Paksa',
        ]);

        $response->assertStatus(404);
        $this->assertTrue(Hash::check('rahasia-asli', $victim->refresh()->password));
    }

    public function test_cannot_deactivate_a_user_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $victim = $this->makeUserInBranch(2, 'korban_nonaktif');

        $response = $this->actingAs(User::first())->post('/users/deactivateUser', ['id' => $victim->id]);

        $response->assertStatus(404);
        $this->assertTrue($victim->refresh()->is_active);
    }

    public function test_cannot_delete_a_user_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $victim = $this->makeUserInBranch(2, 'korban_hapus');

        $response = $this->actingAs(User::first())->post('/users/deleteUser', ['id' => $victim->id]);

        $response->assertStatus(400);
        $this->assertDatabaseHas('m_users', ['id' => $victim->id]);
    }

    // Tidak ada test HTTP untuk resetPass(): method ini sudah di-scope juga
    // (konsisten dengan yang lain), tapi tidak ada satu pun route yang
    // mengarah ke sana (dicek lewat `php artisan route:list`) — dead code,
    // tidak bisa dites lewat HTTP karena memang tidak bisa dipanggil lewat
    // HTTP sama sekali saat ini.

    public function test_cannot_open_edit_page_of_a_user_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $victim = $this->makeUserInBranch(2, 'korban_edit');

        $response = $this->actingAs(User::first())->post('/users/edit', ['id' => $victim->id]);

        $response->assertStatus(404);
    }
}
