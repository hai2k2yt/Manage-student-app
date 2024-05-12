<?php

namespace App\Http\Controllers;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Exception;
use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword']]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $username = $validated['username'];
            $name = $validated['name'];
            $password = bcrypt($validated['password']);
            $role = $validated['role'];

            $user = User::create([
                'username' => $username,
                'name' => $name,
                'password' => $password,
                'role' => $role
            ]);

            if($role == RoleEnum::TEACHER->value) {
                $teacher_code = $validated['code'];
                if(!$teacher_code) {
                    return $this->sendError(__('teacher.code_not_exist'), ErrorCodeEnum::UserRegister);
                }
                Teacher::create([
                    'teacher_code' => $teacher_code,
                    'teacher_name' => $name,
                    'user_id' => $user->id
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendExceptionError($e, ErrorCodeEnum::UserRegister);
        }
    }

    public function login(): JsonResponse
    {
        $credentials = request(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Wrong username or password'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            auth()->user()->update($validated);
            if(auth()->user()->role == 3) {
                $teacher = Teacher::where('user_id', auth()->user()->id)->first();
                $teacher->update(['teacher_name' => $validated['name']]);
            }
            DB::commit();
            return $this->sendResponse(null, __('auth.update_profile_success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError(__('auth.update_profile_error'), ErrorCodeEnum::AuthUpdateProfile);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['username' => 'required|email']);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }


        $newPassword = Str::random(8);
        $user->update(['password' => Hash::make($newPassword)]);

        return response()->json(['message' => 'Password updated successfully', 'new_password' => $newPassword]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        $teacher_code = '';
        $user = auth()->user();
        if($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if($teacher) {
                $teacher_code = $teacher->teacher_code;
            }
        }
        return response()->json([
            'access_token' => $token,
            'user' => [
                ...$user->toArray(),
                ...($user->role == RoleEnum::TEACHER->value ? ['code' => $teacher_code] : [])
            ],
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
