<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'user',
        ]);

        $token = $this->generateToken($user);

        return response()->json(['token' => $token], 200);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Tạo và lưu token vào bảng personal_access_tokens
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function user(Request $request)
    {
        // Tìm người dùng dựa trên api_token
        $user = User::where('api_token', $request->bearerToken())->first();

        // Kiểm tra người dùng và xác thực token
        if (!$user || !hash_equals($user->api_token, hash('sha256', $request->bearerToken()))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Trả về thông tin người dùng dưới dạng JSON
        return response()->json($user);
    }
    private function generateToken($user)
    {
        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->token_created_at = Carbon::now();
        $user->save();

        return $token;
    }
}
