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
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'job_title'   => ['nullable', 'string', 'max:150'],
            'phone'       => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]{6,25}$/'],
            'document_id' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-]{4,20}$/'],
        ]);

        $this->profileService->updateProfile($user, $validated);

        return back()->with('success', 'Perfil profesional actualizado exitosamente.');
    }

    public function updateSignature(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'signature_data' => [
                'nullable',
                'string',
                'max:800000',
                'regex:/^data:image\/(png|jpeg|jpg|webp);base64,[A-Za-z0-9+\/=\-_]+$/'
            ],
        ]);

        $signatureData = null;

        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $mime = $file->getMimeType() ?: 'image/png';
            $signatureData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        } elseif (!empty($request->input('signature_data'))) {
            $signatureData = $request->input('signature_data');
        }

        if (empty($signatureData)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No se proporcionó ninguna firma válida.'], 422);
            }
            return back()->withErrors(['error' => 'Debes dibujar o subir un archivo de firma.']);
        }

        $this->profileService->updateSignature($user, $signatureData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Firma digital oficial guardada exitosamente.',
                'signature_data' => $signatureData,
            ]);
        }

        return back()->with('success', 'Firma digital oficial registrada con éxito.');
    }

    public function deleteSignature(Request $request)
    {
        $user = $request->user();
        $this->profileService->deleteSignature($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Firma digital eliminada.',
            ]);
        }

        return back()->with('success', 'Firma digital eliminada correctamente.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password'      => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->profileService->changePassword($request->user(), $validated['new_password']);

        return back()->with('success', 'Contraseña actualizada de forma segura.');
    }
}
