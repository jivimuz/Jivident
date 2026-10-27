<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Role;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'ROL')->value('id');
        $user->save();

        return $user;
    }

    public function test_can_create_a_role_with_selected_modules(): void
    {
        $user = $this->authorizedUser();
        $moduleIds = Module::whereNotIn('route', ['', '#'])->limit(2)->pluck('id');

        $response = $this->actingAs($user)->post('/roles/store', [
            'name' => 'Kasir',
            'modules' => $moduleIds->toArray(),
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_roles', [
            'name' => 'Kasir',
            'menuAccess' => $moduleIds->sort()->implode(','),
        ]);
    }

    public function test_can_update_a_role(): void
    {
        $user = $this->authorizedUser();
        $role = Role::create(['name' => 'Dokter', 'menuAccess' => '1']);
        $newModuleIds = Module::whereNotIn('route', ['', '#'])->limit(3)->pluck('id');

        $response = $this->actingAs($user)->post("/roles/update/{$role->id}", [
            'name' => 'Dokter Umum',
            'modules' => $newModuleIds->toArray(),
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_roles', [
            'id' => $role->id,
            'name' => 'Dokter Umum',
            'menuAccess' => $newModuleIds->sort()->implode(','),
        ]);
    }

    public function test_can_delete_a_role(): void
    {
        $user = $this->authorizedUser();
        $role = Role::create(['name' => 'Apoteker', 'menuAccess' => '1,2']);

        $response = $this->actingAs($user)->post("/roles/delete/{$role->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('m_roles', ['id' => $role->id]);
    }

    public function test_newly_created_role_shows_up_as_a_selectable_template_on_the_access_page(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = (string) Module::where('code', 'MTA')->value('id');
        $user->save();

        $moduleId = Module::whereNotIn('route', ['', '#'])->value('id');
        $role = Role::create(['name' => 'Kasir', 'menuAccess' => (string) $moduleId]);

        $response = $this->actingAs($user)->post('/access/getMenuList', [
            'role' => $role->id,
        ]);

        $response->assertOk();
        $response->assertSee('Kasir');
    }

    public function test_user_without_role_access_is_redirected_to_error(): void
    {
        $this->authorizedUser();
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/roles');

        $response->assertRedirect(route('error'));
    }
}
