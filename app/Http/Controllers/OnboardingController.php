<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Team;
use App\Models\User;

class OnboardingController extends Controller
{
    public function show()
    {
        // Si ya completó el onboarding, redirigir al dashboard
        if (Auth::user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:boss,employee',
            'company_name' => 'nullable|required_if:role,boss|string|max:255',
            'company_description' => 'nullable|string|max:1000',
            'team_code' => 'nullable|string|max:20',
        ]);

        $user = User::query()->findOrFail(Auth::id());
        $role = $request->role;
        $teamCodeInput = strtoupper(trim((string) $request->input('team_code', '')));

        if ($role === 'boss' && $user->ownedTeams()->exists()) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'No puedes crear otro equipo porque ya tienes uno creado.']);
        }

        $team = null;
        if ($role === 'employee' && !empty($teamCodeInput)) {
            $team = Team::where('invitation_code', strtoupper($teamCodeInput))->first();

            if (!$team) {
                return back()
                    ->withInput()
                    ->withErrors(['team_code' => 'El código del equipo no es válido']);
            }
        }

        // Guardar el estado del onboarding ANTES de cualquier otra acción
        $user->role = $role;
        $user->onboarding_completed = true;
        $user->save();

        if ($role === 'boss') {
            $teamCode = $this->generateTeamCode();

            $team = Team::create([
                'name' => $request->company_name,
                'invitation_code' => $teamCode,
                'owner_id' => $user->id,
                'description' => $request->company_description,
            ]);

            $team->users()->attach($user->id, ['role' => 'admin']);

            return redirect()->route('dashboard')->with('status', '¡Equipo creado con éxito! Tu código es: ' . $teamCode);
        }

        if ($team) {
            $team->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
        }

        return redirect()->route('dashboard')->with('status', '¡Bienvenido al dashboard!');
    }

    private function generateTeamCode()
    {
        do {
            $code = strtoupper(Str::random(3) . '-' . rand(1000, 9999) . '-' . Str::random(3));
        } while (Team::where('invitation_code', $code)->exists());

        return $code;
    }
}
