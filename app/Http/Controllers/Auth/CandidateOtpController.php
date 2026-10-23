<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\OtpRequestRequest;
use App\Http\Requests\Candidate\OtpVerifyRequest;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use App\Support\PhoneNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class CandidateOtpController extends Controller
{
    public function requestCode(OtpRequestRequest $request, OtpService $otp): RedirectResponse
    {
        $phone = (string) $request->input('phone');
        $candidate = $this->candidateFor($phone);

        if ($candidate !== null) {
            try {
                $otp->request($candidate, OtpService::PURPOSE_LOGIN, $request->ip());
            } catch (Throwable $exception) {
                report($exception);

                return redirect()
                    ->route('candidate.login', ['method' => 'otp'])
                    ->withInput()
                    ->with('error', 'We could not send the SMS right now. Please try again in a moment.');
            }
        }

        // The response intentionally does not reveal whether the number is
        // registered, so it cannot be used to enumerate candidates.
        $request->session()->put('otp_phone', $phone);

        return redirect()
            ->route('candidate.login', ['method' => 'otp'])
            ->with('status', 'If this number is registered with GYRS, a one-time code is on its way by SMS.');
    }

    public function verifyCode(OtpVerifyRequest $request, OtpService $otp): RedirectResponse
    {
        $phone = (string) $request->input('phone');
        $candidate = $this->candidateFor($phone);

        $isValid = $candidate !== null
            && $otp->verify($candidate, (string) $request->input('code'), OtpService::PURPOSE_LOGIN);

        if (! $isValid) {
            throw ValidationException::withMessages([
                'code' => 'That code is invalid or has expired. Request a new code and try again.',
            ]);
        }

        if ($candidate->phone_verified_at === null) {
            $candidate->forceFill(['phone_verified_at' => now()])->save();
        }

        Auth::login($candidate, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('otp_phone');

        return redirect()->intended(route('candidate.dashboard'))
            ->with('status', 'Signed in with mobile OTP.');
    }

    public function verifyPhone(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->phone_verified_at !== null) {
            return redirect()
                ->route('candidate.dashboard')
                ->with('info', 'Your mobile number is already verified.');
        }

        return view('candidate.auth.verify-phone', [
            'phone' => $user->phone,
            'maskedPhone' => PhoneNumber::mask($user->phone),
            'hasActiveCode' => $this->hasActiveCode($user),
        ]);
    }

    public function sendPhoneCode(Request $request, OtpService $otp): RedirectResponse
    {
        $user = $request->user();

        if (PhoneNumber::national($user->phone) === null) {
            return redirect()
                ->route('candidate.dashboard')
                ->with('error', 'Add a valid 10-digit mobile number to your profile before verifying it.');
        }

        try {
            $otp->request($user, OtpService::PURPOSE_VERIFY_PHONE, $request->ip());
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('candidate.phone.verify')
                ->with('error', 'We could not send the SMS right now. Please try again in a moment.');
        }

        return redirect()
            ->route('candidate.phone.verify')
            ->with('status', 'A '.$this->codeLength().'-digit code has been sent to '.PhoneNumber::mask($user->phone).'.');
    }

    public function confirmPhone(OtpVerifyRequest $request, OtpService $otp): RedirectResponse
    {
        $user = $request->user();

        if (! $otp->verify($user, (string) $request->input('code'), OtpService::PURPOSE_VERIFY_PHONE)) {
            throw ValidationException::withMessages([
                'code' => 'That code is invalid or has expired. Request a new code and try again.',
            ]);
        }

        $user->forceFill(['phone_verified_at' => now()])->save();

        return redirect()
            ->route('candidate.dashboard')
            ->with('status', 'Mobile number verified. You can now sign in with OTP.');
    }

    private function candidateFor(string $phone): ?User
    {
        $national = PhoneNumber::national($phone);

        if ($national === null) {
            return null;
        }

        return User::query()
            ->where('role', User::ROLE_CANDIDATE)
            ->where('phone', $national)
            ->first();
    }

    private function codeLength(): int
    {
        return min(8, max(4, (int) config('otp.code_length', 6)));
    }

    private function hasActiveCode(User $user): bool
    {
        $phone = PhoneNumber::e164($user->phone);

        if ($phone === null) {
            return false;
        }

        return OtpCode::query()
            ->where('phone', $phone)
            ->where('purpose', OtpService::PURPOSE_VERIFY_PHONE)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->exists();
    }
}
