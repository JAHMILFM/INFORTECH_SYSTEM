<?php

namespace App\Modules\Profile\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Profile\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        return view('profile.index', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $this->profileService->updateProfile($user, $validated);

        return back()->with('success', 'Perfil actualizado exitosamente.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->profileService->changePassword($request->user(), $validated['new_password']);

        return back()->with('success', 'Contraseña actualizada de forma segura.');
    }
}
