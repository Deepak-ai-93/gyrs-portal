<?php

namespace Tests\Feature;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_redirect_stores_role_and_redirects_to_google(): void
    {
        $response = $this->get(route('auth.google.redirect', ['role' => 'candidate']));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', (string) $response->headers->get('Location'));
        $this->assertEquals(User::ROLE_CANDIDATE, session('google_auth_role'));
    }

    public function test_redirect_stores_admin_role_in_session(): void
    {
        $response = $this->get(route('auth.google.redirect', ['role' => 'admin']));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', (string) $response->headers->get('Location'));
        $this->assertEquals(User::ROLE_ADMIN, session('google_auth_role'));
    }

    public function test_redirect_defaults_to_candidate_role_for_invalid_roles(): void
    {
        $response = $this->get(route('auth.google.redirect', ['role' => 'super_hacker']));

        $response->assertRedirect();
        $this->assertEquals(User::ROLE_CANDIDATE, session('google_auth_role'));
    }

    public function test_callback_registers_new_candidate_with_google(): void
    {
        $this->mockGoogleUser([
            'id' => 'google-uid-001',
            'name' => 'Rahul Sharma',
            'email' => 'rahul.sharma@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/avatar1.jpg',
        ]);

        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'rahul.sharma@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Rahul Sharma', $user->name);
        $this->assertEquals('google-uid-001', $user->google_id);
        $this->assertEquals(User::ROLE_CANDIDATE, $user->role);
        $this->assertEquals(User::STATUS_PENDING, $user->status);
        $this->assertNotNull($user->enrollment_id);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_callback_logs_in_existing_candidate_by_google_id(): void
    {
        $existing = User::factory()->candidate()->create([
            'email' => 'existing.google@example.com',
            'google_id' => 'google-uid-002',
        ]);

        $this->mockGoogleUser([
            'id' => 'google-uid-002',
            'name' => 'Existing Candidate',
            'email' => 'existing.google@example.com',
        ]);

        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticatedAs($existing);
    }

    public function test_callback_links_google_id_for_existing_candidate_by_email(): void
    {
        $existing = User::factory()->candidate()->create([
            'email' => 'email.only@example.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        $this->mockGoogleUser([
            'id' => 'google-uid-003',
            'name' => 'Email Candidate',
            'email' => 'email.only@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/avatar3.jpg',
        ]);

        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticatedAs($existing);

        $existing->refresh();
        $this->assertEquals('google-uid-003', $existing->google_id);
        $this->assertEquals('https://lh3.googleusercontent.com/avatar3.jpg', $existing->avatar);
    }

    public function test_callback_handles_null_name_safely(): void
    {
        $this->mockGoogleUser([
            'id' => 'google-uid-004',
            'name' => null,
            'nickname' => null,
            'email' => 'priya.patel@example.com',
        ]);

        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('candidate.dashboard'));

        $user = User::where('email', 'priya.patel@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Priya Patel', $user->name);
    }

    public function test_callback_logs_in_existing_admin_and_links_google_id(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin.officer@gyrs.gov.in',
            'google_id' => null,
        ]);

        $this->mockGoogleUser([
            'id' => 'google-uid-admin',
            'name' => 'Admin Officer',
            'email' => 'admin.officer@gyrs.gov.in',
            'avatar' => 'https://lh3.googleusercontent.com/admin.jpg',
        ]);

        $response = $this->withSession(['google_auth_role' => User::ROLE_ADMIN])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $admin->refresh();
        $this->assertEquals('google-uid-admin', $admin->google_id);
    }

    public function test_callback_logs_in_existing_admin_when_initiated_from_candidate_page(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'gyrsportal@gmail.com',
            'google_id' => null,
        ]);

        $this->mockGoogleUser([
            'id' => 'google-uid-gyrsportal',
            'name' => 'Super Administrator',
            'email' => 'gyrsportal@gmail.com',
        ]);

        // Even if the OAuth flow was initiated from the candidate page:
        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_callback_rejects_admin_login_if_no_admin_account_exists(): void
    {
        $this->mockGoogleUser([
            'id' => 'google-uid-stranger',
            'name' => 'Random User',
            'email' => 'random.stranger@example.com',
        ]);

        $response = $this->withSession(['google_auth_role' => User::ROLE_ADMIN])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_callback_handles_oauth_cancellation_gracefully(): void
    {
        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback', ['error' => 'access_denied']));

        $response->assertRedirect(route('candidate.login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_callback_handles_socialite_exception_gracefully(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('user')->andThrow(new Exception('Network timeout during token exchange'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->withSession(['google_auth_role' => User::ROLE_CANDIDATE])
            ->get(route('auth.google.callback'));

        $response->assertRedirect(route('candidate.login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Helper to mock the Socialite user and Google provider.
     */
    private function mockGoogleUser(array $data): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn($data['id'] ?? 'mock-google-id');
        $abstractUser->shouldReceive('getName')->andReturn($data['name'] ?? null);
        $abstractUser->shouldReceive('getNickname')->andReturn($data['nickname'] ?? null);
        $abstractUser->shouldReceive('getEmail')->andReturn($data['email'] ?? 'mock@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn($data['avatar'] ?? null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }
}
