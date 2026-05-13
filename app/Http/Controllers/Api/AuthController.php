<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
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

        $authUser = User::firstOrCreate(
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
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
