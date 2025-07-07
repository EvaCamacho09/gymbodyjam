<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClientePublicoController;
use App\Http\Controllers\AsistenciaPublicaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta pública para ver información del cliente
Route::get('/cliente/{token}', [ClientePublicoController::class, 'mostrar'])->name('cliente.publico');

// Ruta de login web simple
Route::get('/login', function () {
    return view('login-simple');
})->name('login');

Route::post('/web-login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/asistencia');
    }

    return back()->withErrors([
        'email' => 'Las credenciales no coinciden con nuestros registros.',
    ]);
})->name('web.login');

Route::post('/web-logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('web.logout');

Route::middleware('auth')->group(function () {
    // Rutas protegidas para monitor de asistencias
    Route::get('/asistencia', function () {
        return view('asistencia-publica');
    })->name('asistencia.publica');

    Route::get('/monitor', function () {
        return view('asistencia-publica');
    })->name('monitor.asistencia');
    
    // Rutas API de asistencia (web auth)
    Route::post('/api/asistencia/buscar-cliente', [AsistenciaPublicaController::class, 'buscarCliente']);
    Route::post('/api/asistencia/registrar', [AsistenciaPublicaController::class, 'registrarAsistencia']);
});


// Ruta principal que sirve la aplicación Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
