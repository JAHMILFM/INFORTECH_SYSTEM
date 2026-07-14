<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(['SuperAdmin', 'Soporte', 'Ventas'])],
        ]);

        $this->userService->createUser($validated);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'  => ['required', Rule::in(['SuperAdmin', 'Soporte', 'Ventas'])],
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $this->userService->updateUser($user, $validated);
            return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);
            return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
