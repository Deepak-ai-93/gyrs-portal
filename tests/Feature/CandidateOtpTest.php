<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CandidateOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'otp.sms_driver' => 'twilio',
            'otp.resend_seconds' => 0,
            'services.twilio.account_sid' => 'AC-test',
            'services.twilio.auth_token' => 'test-token',
            'services.twilio.from' => '+15005550006',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'api.twilio.com/*' => Http::response(['sid' => 'SM-test'], 201),
        ]);
    }

    public function test_registered_candidate_receives_an_otp_by_sms(): void
    {
        User::factory()->candidate()->create(['phone' => '9876543210']);

        $response = $this->post('/candidate/otp/request', ['phone' => '98765 43210']);

        $response->assertRedirect(route('candidate.login', ['method' => 'otp']));
        $response->assertSessionHas('status');
        $response->assertSessionHas('otp_phone', '9876543210');

        $this->assertDatabaseHas('otp_codes', [
            'phone' => '+919876543210',
            'purpose' => OtpService::PURPOSE_LOGIN,
        ]);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'https://api.twilio.com/2010-04-01/Accounts/AC-test/Messages.json'
                && $data['To'] === '+919876543210'
                && $data['From'] === '+15005550006'
                && str_contains($data['Body'], 'GYRS');
        });

        $this->assertGuest();
    }

    public function test_candidate_can_sign_in_with_a_valid_otp(): void
    {
        $candidate = User::factory()->candidate()->create(['phone' => '9876543210']);

        $this->post('/candidate/otp/request', ['phone' => '9876543210']);
        $code = $this->sentCode();

        $response = $this->post('/candidate/otp/verify', [
            'phone' => '9876543210',
            'code' => $code,
            'remember' => '1',
        ]);

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticatedAs($candidate);
        $this->assertNotNull($candidate->fresh()->phone_verified_at);
        $this->assertNotNull(OtpCode::query()->latest()->first()->consumed_at);
    }

    public function test_wrong_otp_does_not_sign_the_candidate_in(): void
    {
        User::factory()->candidate()->create(['phone' => '9876543210']);

        $this->post('/candidate/otp/request', ['phone' => '9876543210']);
        $code = $this->sentCode();
        $wrong = $code === '000000' ? '111111' : '000000';

        $response = $this->from(route('candidate.login', ['method' => 'otp']))->post('/candidate/otp/verify', [
            'phone' => '9876543210',
            'code' => $wrong,
        ]);

        $response->assertRedirect(route('candidate.login', ['method' => 'otp']));
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_expired_otp_is_rejected(): void
    {
        $candidate = User::factory()->candidate()->create(['phone' => '9876543210']);

        OtpCode::query()->create([
            'phone' => '+919876543210',
            'purpose' => OtpService::PURPOSE_LOGIN,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->post('/candidate/otp/verify', [
            'phone' => '9876543210',
            'code' => '123456',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_otp_is_locked_after_too_many_attempts(): void
    {
        config(['otp.max_attempts' => 3, 'otp.verify_per_ten_minutes' => 50]);

        User::factory()->candidate()->create(['phone' => '9876543210']);

        $this->post('/candidate/otp/request', ['phone' => '9876543210']);
        $code = $this->sentCode();
        $wrong = $code === '000000' ? '111111' : '000000';

        foreach (range(1, 3) as $attempt) {
            $this->post('/candidate/otp/verify', ['phone' => '9876543210', 'code' => $wrong]);
        }

        $response = $this->post('/candidate/otp/verify', ['phone' => '9876543210', 'code' => $code]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_unknown_number_gets_the_same_generic_response_and_no_sms(): void
    {
        $response = $this->post('/candidate/otp/request', ['phone' => '9800000000']);

        $response->assertRedirect(route('candidate.login', ['method' => 'otp']));
        $response->assertSessionHas('status');
        Http::assertNothingSent();
        $this->assertDatabaseCount('otp_codes', 0);
    }

    public function test_registration_sends_a_code_and_prompts_for_phone_verification(): void
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
            'skills' => ['Python'],
        ]);

        $response->assertRedirect(route('candidate.phone.verify'));
        $this->assertAuthenticated();

        $candidate = User::query()->where('phone', '9876500000')->firstOrFail();
        $this->assertNull($candidate->phone_verified_at);
        $this->assertDatabaseHas('otp_codes', [
            'phone' => '+919876500000',
            'purpose' => OtpService::PURPOSE_VERIFY_PHONE,
        ]);

        Http::assertSentCount(1);
    }

    public function test_authenticated_candidate_can_verify_their_phone(): void
    {
        $candidate = User::factory()->candidate()->create(['phone' => '9876543210']);

        $this->actingAs($candidate)->get('/candidate/phone/verify')
            ->assertOk()
            ->assertSee('Send verification code');

        $this->actingAs($candidate)->post('/candidate/phone/verify')->assertRedirect(route('candidate.phone.verify'));

        $code = $this->sentCode();

        $response = $this->actingAs($candidate)->post('/candidate/phone/confirm', [
            'phone' => '9876543210',
            'code' => $code,
        ]);

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertNotNull($candidate->fresh()->phone_verified_at);
    }

    public function test_changing_the_mobile_number_clears_its_verification(): void
    {
        $candidate = User::factory()->candidate()->create([
            'phone' => '9876543210',
            'phone_verified_at' => now(),
        ]);
        $district = District::factory()->create();

        $response = $this->actingAs($candidate)->patch('/candidate/profile', [
            'name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => '9812345678',
            'district_id' => $district->getKey(),
            'qualification' => $candidate->qualification,
            'cgpa' => $candidate->cgpa,
            'skills' => [],
        ]);

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertNull($candidate->fresh()->phone_verified_at);
    }

    public function test_otp_requests_are_rate_limited(): void
    {
        config(['otp.request_per_hour' => 1]);
        User::factory()->candidate()->create(['phone' => '9876543210']);

        $this->post('/candidate/otp/request', ['phone' => '9876543210'])
            ->assertSessionMissing('error');

        $this->post('/candidate/otp/request', ['phone' => '9876543210'])
            ->assertSessionHas('error');
    }

    private function sentCode(): string
    {
        $code = null;

        Http::assertSent(function ($request) use (&$code) {
            $body = (string) ($request->data()['Body'] ?? '');

            if (preg_match('/code is (\d+)/i', $body, $matches) === 1) {
                $code = $matches[1];
            }

            return true;
        });

        $this->assertNotNull($code, 'No OTP was sent to Twilio.');

        return (string) $code;
    }
}
