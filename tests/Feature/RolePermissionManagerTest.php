<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Livewire\System\RolePermissionManager;

class RolePermissionManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $musyrifUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the standard database records (users, roles, permissions)
        $this->seed();

        $this->adminUser = User::where('email', 'admin@alfithroh.pondok')->firstOrFail();
        $this->musyrifUser = User::where('email', 'musyrif@alfithroh.pondok')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/system/roles-permissions');
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_roles_manager(): void
    {
        // Musyrif does not have 'manage-roles' permission or 'super-admin' role
        $response = $this->actingAs($this->musyrifUser)->get('/system/roles-permissions');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_roles_manager(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/system/roles-permissions');
        $response->assertStatus(200);
        $response->assertSeeLivewire(RolePermissionManager::class);
    }

    public function test_admin_can_create_new_role(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('newRoleName', 'ustadz-tahfidz')
            ->call('createRole')
            ->assertHasNoErrors()
            ->assertSet('newRoleName', '');

        $this->assertTrue(Role::where('name', 'ustadz-tahfidz')->exists());
    }

    public function test_admin_can_toggle_role_permission(): void
    {
        $role = Role::where('name', 'musyrif')->firstOrFail();
        $permissionName = 'view-audit-log';

        $this->assertFalse($role->hasPermissionTo($permissionName));

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $role->id)
            ->call('togglePermission', $permissionName);

        $this->assertTrue($role->fresh()->hasPermissionTo($permissionName));

        // Toggle again to revoke
        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $role->id)
            ->call('togglePermission', $permissionName);

        $this->assertFalse($role->fresh()->hasPermissionTo($permissionName));
    }

    public function test_admin_cannot_modify_super_admin_permissions(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->firstOrFail();
        $permissionName = 'view-audit-log';

        $this->assertTrue($superAdminRole->hasPermissionTo($permissionName));

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $superAdminRole->id)
            ->call('togglePermission', $permissionName)
            ->assertDispatched('toast-show', type: 'error', message: 'Izin untuk peran Super-Admin tidak dapat diubah (dikelola penuh otomatis).');

        // Permission should remain true
        $this->assertTrue($superAdminRole->fresh()->hasPermissionTo($permissionName));
    }

    public function test_admin_can_assign_roles_to_user(): void
    {
        $operatorUser = User::where('email', 'operator@alfithroh.pondok')->firstOrFail();
        
        $this->assertFalse($operatorUser->hasRole('musyrif'));

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->call('openUserModal', $operatorUser->id)
            ->call('toggleUserRole', 'musyrif')
            ->call('saveUserRoles');

        $this->assertTrue($operatorUser->fresh()->hasRole('musyrif'));
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $musyrif = User::where('email', 'musyrif@alfithroh.pondok')->firstOrFail();
        $this->assertTrue($musyrif->is_active);

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->call('toggleUserStatus', $musyrif->id)
            ->assertHasNoErrors()
            ->assertDispatched('toast-show', type: 'success', message: "Status pengurus '{$musyrif->name}' berhasil diperbarui.");

        $this->assertFalse($musyrif->fresh()->is_active);

        // Toggle back to active
        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->call('toggleUserStatus', $musyrif->id)
            ->assertHasNoErrors();

        $this->assertTrue($musyrif->fresh()->is_active);
    }

    public function test_admin_cannot_disable_self(): void
    {
        $this->assertTrue($this->adminUser->is_active);

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->call('toggleUserStatus', $this->adminUser->id)
            ->assertDispatched('toast-show', type: 'error', message: 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

        $this->assertTrue($this->adminUser->fresh()->is_active);
    }

    public function test_admin_cannot_disable_last_super_admin(): void
    {
        // Make sure only 1 super-admin exists and is active (adminUser)
        $superAdminCount = User::role('super-admin')->where('is_active', true)->count();
        $this->assertEquals(1, $superAdminCount);

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->call('toggleUserStatus', $this->adminUser->id)
            ->assertDispatched('toast-show', type: 'error', message: 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

        // Create another super-admin
        $anotherAdmin = User::factory()->create([
            'email' => 'admin2@alfithroh.pondok',
            'username' => 'admin2',
            'is_active' => true,
        ]);
        $anotherAdmin->assignRole('super-admin');

        // Now we have two active super-admins.
        // Let's deactivate adminUser by acting as anotherAdmin
        Livewire::actingAs($anotherAdmin)
            ->test(RolePermissionManager::class)
            ->call('toggleUserStatus', $this->adminUser->id)
            ->assertHasNoErrors();

        $this->assertFalse($this->adminUser->fresh()->is_active);

        // Manager user with manage-roles permission but not super-admin
        $manager = User::factory()->create(['email' => 'manager@alfithroh.pondok', 'username' => 'manager', 'is_active' => true]);
        $manager->givePermissionTo('manage-roles');

        // manager tries to deactivate anotherAdmin (who is the last active super-admin)
        Livewire::actingAs($manager)
            ->test(RolePermissionManager::class)
            ->call('toggleUserStatus', $anotherAdmin->id)
            ->assertDispatched('toast-show', type: 'error', message: 'Tidak dapat menonaktifkan akun Super-Admin ini karena ini adalah satu-satunya akun administrator utama yang aktif.');

        $this->assertTrue($anotherAdmin->fresh()->is_active);
    }

    public function test_admin_can_reset_role_permissions(): void
    {
        $role = Role::where('name', 'musyrif')->firstOrFail();
        $role->givePermissionTo('view-audit-log');
        $this->assertTrue($role->hasPermissionTo('view-audit-log'));

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $role->id)
            ->call('resetPermissions')
            ->assertHasNoErrors()
            ->assertDispatched('toast-show', type: 'success', message: "Semua wewenang untuk peran '{$role->name}' berhasil dikosongkan.");

        $this->assertEmpty($role->fresh()->permissions);
    }

    public function test_admin_can_copy_role_permissions(): void
    {
        $musyrif = Role::where('name', 'musyrif')->firstOrFail();
        $pengasuh = Role::where('name', 'pengasuh')->firstOrFail();

        $musyrif->syncPermissions(['view-audit-log', 'manage-users']);
        $pengasuh->syncPermissions(['view-perizinan']);

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $pengasuh->id)
            ->set('copyFromRoleId', $musyrif->id)
            ->call('copyPermissions')
            ->assertHasNoErrors()
            ->assertDispatched('toast-show', type: 'success', message: "Wewenang berhasil disalin dari peran '{$musyrif->name}' ke peran '{$pengasuh->name}'.");

        $this->assertTrue($pengasuh->fresh()->hasPermissionTo('view-audit-log'));
        $this->assertTrue($pengasuh->fresh()->hasPermissionTo('manage-users'));
        $this->assertFalse($pengasuh->fresh()->hasPermissionTo('view-perizinan'));
    }

    public function test_security_activities_are_logged(): void
    {
        $role = Role::where('name', 'musyrif')->firstOrFail();
        
        // Clean existing logs if any
        \Spatie\Activitylog\Models\Activity::query()->delete();

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $role->id)
            ->call('togglePermission', 'view-audit-log');

        $this->assertEquals(1, \Spatie\Activitylog\Models\Activity::where('log_name', 'security')->count());
        $log = \Spatie\Activitylog\Models\Activity::where('log_name', 'security')->first();
        $this->assertStringContainsString('Telah memberikan wewenang', $log->description);
    }

    public function test_admin_can_filter_permissions_by_group(): void
    {
        $role = Role::where('name', 'musyrif')->firstOrFail();

        Livewire::actingAs($this->adminUser)
            ->test(RolePermissionManager::class)
            ->set('selectedRoleId', $role->id)
            ->set('selectedGroup', 'Kepengurusan')
            ->assertSee('Melihat Data Santri') // Kepengurusan
            ->assertDontSee('Melihat Semua Person') // Core & Person
            ->set('selectedGroup', 'all')
            ->assertSee('Melihat Data Santri')
            ->assertSee('Melihat Semua Person');
    }
}

