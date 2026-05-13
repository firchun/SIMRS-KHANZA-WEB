<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesktopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $request->validate(['id_user' => 'required|string', 'password' => 'required|string']);

    $record = null;
    $source = null;

    $userRow = DB::table('user')
        ->whereRaw("AES_DECRYPT(id_user, 'nur') = ?", [$request->id_user])
        ->whereRaw("AES_DECRYPT(password, 'windi') = ?", [$request->password])
        ->first();
    if ($userRow) {
        $record = $userRow;
        $source = 'user';
    }

    if (!$record) {
        $adminRow = DB::table('admin')
            ->whereRaw("AES_DECRYPT(usere, 'nur') = ?", [$request->id_user])
            ->whereRaw("AES_DECRYPT(passworde, 'windi') = ?", [$request->password])
            ->first();
        if ($adminRow) {
            $record = $adminRow;
            $source = 'admin';
        }
    }

    if (!$record) {
        throw ValidationException::withMessages(['id_user' => ['The provided credentials are incorrect.']]);
    }

    $nama = $source === 'user' ? ($record->nama ?? $request->id_user) : $request->id_user;
    $role = $source === 'user' ? ($record->level ?? 'Operator') : 'Admin';

    $authUser = App\Models\User::firstOrCreate(
        ['id_user' => $request->id_user],
        [
            'name' => $nama,
            'email' => $request->id_user . '@simrs.khanza',
            'password' => bcrypt($request->password),
            'role' => $role,
        ]
    );

    if ($authUser->wasRecentlyCreated === false) {
        $authUser->update(['name' => $nama, 'role' => $role]);
    }

    Auth::login($authUser);
    $request->session()->regenerate();

    $token = $authUser->createToken('khanza-web')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $authUser->id,
            'id_user' => $authUser->id_user,
            'name' => $authUser->name,
            'email' => $authUser->email,
            'role' => $authUser->role,
        ]
    ]);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DesktopController::class, 'index']);
    Route::get('/desktop', [DesktopController::class, 'index']);
    Route::get('/desktop/modules/{module}', [DesktopController::class, 'module']);
});
