<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdministratorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_the_administrator_console(): void
    {
        $superAdmin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)
            ->get(route('admin.administrators.index'))
            ->assertOk()
            ->assertSee('Administrators');

        $this->actingAs($superAdmin)
            ->get(route('admin.administrators.create'))
            ->assertOk()
            ->assertSee('Access permissions');
    }

    public function test_super_admin_can_create_a_scoped_administrator(): void
    {
        $superAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($superAdmin)->post(route('admin.administrators.store'), [
            'name' => 'District Verifier',
            'email' => 'verifier@gyrs.gov.in',
            'phone' => '9876500000',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'permissions' => [
                AdminPermissions::CANDIDATES_VIEW,
                AdminPermissions::CANDIDATES_VERIFY,
                'not-a-real-permission',
            ],
        ]);

        $response->assertRedirect(route('admin.administrators.index'));

        $administrator = User::query()->where('email', 'verifier@gyrs.gov.in')->firstOrFail();

        $this->assertSame(User::ROLE_ADMIN, $administrator->role);
        $this->assertFalse($administrator->isSuperAdmin());
        $this->assertEqualsCanonicalizing(
            [AdminPermissions::CANDIDATES_VIEW, AdminPermissions::CANDIDATES_VERIFY],
            $administrator->adminPermissions(),
        );
        $this->assertTrue(Hash::check('secret-password', $administrator->password));
    }

    public function test_creating_an_administrator_requires_a_unique_email_and_password(): void
    {
        $superAdmin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)->post(route('admin.administrators.store'), [
            'name' => 'Duplicate',
            'email' => $superAdmin->email,
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_scoped_administrator_cannot_manage_other_administrators(): void
    {
        $scoped = User::factory()->subAdmin([AdminPermissions::CANDIDATES_VIEW])->create();

        $this->actingAs($scoped)
            ->get(route('admin.administrators.index'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($scoped)
            ->post(route('admin.administrators.store'), [
                'name' => 'Sneaky Admin',
                'email' => 'sneaky@gyrs.gov.in',
                'password' => 'secret-password',
                'password_confirmation' => 'secret-password',
            ])
            ->assertForbidden();
    }

    public function test_administrator_without_permission_cannot_reach_that_section(): void
    {
        $scoped = User::factory()->subAdmin([AdminPermissions::CANDIDATES_VIEW])->create();

        $this->actingAs($scoped)
            ->get(route('admin.jobs.create'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($scoped)
            ->post(route('admin.jobs.store'))
            ->assertForbidden();
    }

    public function test_administrator_with_permission_can_reach_that_section(): void
    {
        $scoped = User::factory()->subAdmin([AdminPermissions::JOBS_VIEW, AdminPermissions::JOBS_MANAGE])->create();

        $this->actingAs($scoped)
            ->get(route('admin.jobs.index'))
            ->assertOk();

        $this->actingAs($scoped)
            ->get(route('admin.jobs.create'))
            ->assertOk();
    }

    public function test_super_administrator_accounts_cannot_be_removed(): void
    {
        $superAdmin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)
            ->delete(route('admin.administrators.destroy', $superAdmin))
            ->assertRedirect(route('admin.administrators.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $superAdmin->getKey()]);
    }

    public function test_super_admin_can_remove_a_scoped_administrator(): void
    {
        $superAdmin = User::factory()->admin()->create();
        $scoped = User::factory()->subAdmin([AdminPermissions::CANDIDATES_VIEW])->create();

        $this->actingAs($superAdmin)
            ->delete(route('admin.administrators.destroy', $scoped))
            ->assertRedirect(route('admin.administrators.index'));

        $this->assertDatabaseMissing('users', ['id' => $scoped->getKey()]);
    }
}
