<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_login_with_email(): void
    {
        $candidate = User::factory()->candidate()->create([
            'email' => 'candidate@example.com',
            'password' => 'secret-password',
        ]);

        $response = $this->post('/candidate/login', [
            'identifier' => 'candidate@example.com',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticatedAs($candidate);
    }

    public function test_candidate_login_rejects_invalid_password(): void
    {
        User::factory()->candidate()->create(['email' => 'candidate@example.com']);

        $response = $this->from('/candidate/login')->post('/candidate/login', [
            'identifier' => 'candidate@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/candidate/login');
        $response->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_admin_login_requires_admin_role(): void
    {
        User::factory()->candidate()->create(['email' => 'candidate@example.com']);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'candidate@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => 'admin-password',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'admin-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_candidate_can_register(): void
    {
        $district = District::factory()->create();

        $response = $this->post('/candidate/register', [
            'name' => 'New Candidate',
            'email' => 'new.candidate@example.com',
            'phone' => '9876500000',
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
            'district_id' => $district->getKey(),
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'cgpa' => '8.5',
            'skills' => ['Python', 'SQL'],
        ]);

        $response->assertRedirect(route('candidate.phone.verify'));
        $this->assertDatabaseHas('users', [
            'email' => 'new.candidate@example.com',
            'role' => User::ROLE_CANDIDATE,
        ]);
        $this->assertAuthenticated();
    }
}
