<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::get('/assets/{path}', function ($path) {
    $basePath = storage_path('app/assets');
    $fullPath = realpath($basePath . '/' . $path);
    
    // Prevención de Path Traversal (LFI): Asegurar que el archivo resuelto siga dentro de la carpeta assets
    if (!$fullPath || !str_starts_with($fullPath, realpath($basePath))) {
        abort(404);
    }

    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mime = File::mimeType($fullPath);
    if (str_ends_with($path, '.css')) $mime = 'text/css';
    if (str_ends_with($path, '.js')) $mime = 'application/javascript';
    if (str_ends_with($path, '.woff2')) $mime = 'font/woff2';
    
    return Response::file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Prevenir fuerza bruta
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Modules\Company\Controllers\CompanyController;
use App\Modules\Profile\Controllers\ProfileController;
use App\Modules\ServiceRecord\Controllers\ServiceRecordController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Modules\Dashboard\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [\App\Modules\Search\Controllers\SearchController::class, 'index'])->name('search');
    
    // Exportación Global Maestra (Solo SuperAdmin)
    Route::get('/export-master', [\App\Modules\Dashboard\Controllers\DashboardController::class, 'exportMaster'])
        ->middleware('role:SuperAdmin')
        ->name('export.master');

    // Configuración
    Route::resource('companies', CompanyController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->middleware('role:SuperAdmin')->name('companies.destroy');

    // Servicios por empresa
    Route::get('companies/{company}/services/{type}',            [ServiceRecordController::class, 'index'])  ->name('companies.services.index');
    Route::get('companies/{company}/services/{type}/export',     [ServiceRecordController::class, 'export']) ->name('companies.services.export');
    Route::post('companies/{company}/services/{type}',           [ServiceRecordController::class, 'store'])  ->name('companies.services.store');
    Route::put('companies/{company}/services/{type}/{record}', [\App\Modules\ServiceRecord\Controllers\ServiceRecordController::class, 'update']) ->name('companies.services.update');
    Route::post('companies/{company}/services/{type}/{record}/toggle-status', [\App\Modules\ServiceRecord\Controllers\ServiceRecordController::class, 'toggleStatus'])->name('companies.services.toggle');
    
    // Configuración Rápida Zimbra
    Route::post('/companies/{company}/zimbra-config', [CompanyController::class, 'updateZimbraConfig'])->name('companies.updateZimbraConfig');
    Route::post('/companies/{company}/zimbra-password/reveal', [CompanyController::class, 'revealZimbraPassword'])->name('companies.zimbra.reveal');

    // Configuración Rápida Nextcloud
    Route::post('/companies/{company}/nextcloud-config', [CompanyController::class, 'updateNextcloudConfig'])->name('companies.updateNextcloudConfig');
    Route::post('/companies/{company}/nextcloud-password/reveal', [CompanyController::class, 'revealNextcloudPassword'])->name('companies.nextcloud.reveal');

    Route::middleware('role:SuperAdmin')->delete('companies/{company}/services/{type}/{record}', [\App\Modules\ServiceRecord\Controllers\ServiceRecordController::class, 'destroy']) ->name('companies.services.destroy');

    // Importar Excel por empresa
    Route::get('companies/{company}/import',  [\App\Modules\ServiceRecord\Controllers\ImportController::class, 'show'])  ->name('companies.import.show');
    Route::post('companies/{company}/import', [\App\Modules\ServiceRecord\Controllers\ImportController::class, 'store']) ->name('companies.import.store');
    
    // Gestión de Usuarios y Auditoría (Solo SuperAdmin)
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::resource('users', \App\Modules\User\Controllers\UserController::class)->except(['create', 'show', 'edit']);
        Route::get('audit-logs', [\App\Modules\AuditLog\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});
