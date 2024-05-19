<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
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
use Symfony\Component\HttpFoundation\Response;

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

            if ($role == RoleEnum::TEACHER->value) {
                $teacher_code = $validated['code'];
                if (!$teacher_code) {
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::UserRegister,
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        ['teacher' => __('teacher.teacher_code_required')]
                    );
                }
                Teacher::create([
                    'teacher_code' => $teacher_code,
                    'teacher_name' => $name,
                    'user_id' => $user->id
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => __('user.register_success'),
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendExceptionError($e, ErrorCodeEnum::UserRegister);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(
                ['errors' => [
                    'auth' => __('auth.failed')
                ]], 401);
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
        if (auth()->user()->role == 3) {
            $teacher = Teacher::where('user_id', auth()->user()->id)->first();
            return response()->json(
                [
                    ...auth()->user()->toArray(),
                    "code" => $teacher->teacher_code
                ]
            );
        }
        return response()->json(auth()->user());
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            auth()->user()->update($validated);
            if (auth()->user()->role == 3) {
                $teacher = Teacher::where('user_id', auth()->user()->id)->first();
                $teacher->update(['teacher_name' => $validated['name']]);
            }
            DB::commit();
            return $this->sendResponse(null, __('auth.success.update_profile'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendExceptionError($e, ErrorCodeEnum::AuthUpdateProfile);
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

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $user = auth()->user();

        if (!Hash::check($requestData['current_password'], $user->password)) {
            return response()->json(['message' => __('auth.password')], 401);
        }

        $user->update(['password' => Hash::make($requestData['new_password'])]);

        return response()->json(['message' => __('auth.success.change_password')]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => __('auth.success.logout')]);
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
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
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
