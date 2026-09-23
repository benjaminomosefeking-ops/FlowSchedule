<?php

namespace App\Bundle\FlowScheduler\UI\Controllers\Auth;

use App\Bundle\FlowScheduler\Application\DTOs\RegisterUserDTO;
use App\Bundle\FlowScheduler\Application\UseCases\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserRegisters;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user->email_verified_at = now();
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        // Send welcome notification
        $user->notify(new UserRegisters());

        return redirect(route('dashboard', absolute: false));
    }
}