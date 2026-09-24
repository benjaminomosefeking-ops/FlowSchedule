<?php

namespace App\Bundle\FlowScheduler\UI\Controllers\Auth;

use App\Bundle\FlowScheduler\Application\DTOs\RegisterUserDTO;
use App\Bundle\FlowScheduler\Application\UseCases\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserRegisters;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request, RegisterUserUseCase $registerUser): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $registerUser->execute(new RegisterUserDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        ));

        event(new Registered($user));

        Auth::login($user);

        // Send welcome notification
        $user->notify(new UserRegisters());

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Ensure the registration request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($this->ip($request));
    }

    /**
     * Get the IP address of the request.
     */
    protected function ip(Request $request): string
    {
        return $request->ip();
    }
}