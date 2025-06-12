<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'type' => ['sometimes', 'in:normal,researcher,admin'], // Allow setting user type
            ]);

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'avatar' => $avatarPath,
                'type' => $validated['type'] ?? 'normal',
            ]);

            $token = $user->createToken('api-token', ['*'], now()->addDays(30))->plainTextToken;

            return $this->successResponse([
                'user' => $user->makeHidden(['password']),
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'User registered successfully', 201);

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
                'remember' => ['boolean'],
            ]);

            if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $user = Auth::user();

            // Revoke existing tokens if needed
            if ($request->get('revoke_existing_tokens', false)) {
                $user->tokens()->delete();
            }

            $tokenExpiry = $request->get('remember', false) ? now()->addDays(30) : now()->addHours(24);
            $token = $user->createToken('api-token', ['*'], $tokenExpiry)->plainTextToken;

            return $this->successResponse([
                'user' => $user->makeHidden(['password']),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenExpiry->toISOString(),
            ], 'Login successful');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed', 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Option to logout from all devices
            if ($request->get('logout_all_devices', false)) {
                $request->user()->tokens()->delete();
                return $this->successResponse(null, 'Logged out from all devices');
            }

            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Logged out successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user()->load(['articleComments', 'communityPosts']);
            return $this->successResponse($user->makeHidden(['password']));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch profile', 500);
        }
    }


    public function updateProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'min:3', 'max:255'],
                'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id, 'max:255'],
                'password' => ['sometimes', 'string', 'min:8'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'current_password' => ['required_with:password', 'string'],
            ]);

            // Verify current password if changing password
            if (isset($validated['password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    return $this->errorResponse('Current password is incorrect', 400);
                }
                $validated['password'] = Hash::make($validated['password']);
                unset($validated['current_password']);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            // Update the user with validated data
            $user->update($validated);

            // Force refresh from database and clear any cached attributes
            $user = $user->fresh();

            // Alternative approach - you can also try this instead of fresh():
            // $user->refresh();

            // Clear model cache if you're using any caching
            $user->touch(); // Updates updated_at timestamp

            return $this->successResponse(
                $user->makeHidden(['password']),
                'Profile updated successfully'
            );

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return $this->errorResponse('Profile update failed', 500);
        }
    }

//    public function updateProfile(Request $request): \Illuminate\Http\JsonResponse
//    {
//        try {
//            $user = $request->user();
//            $validated = $request->validate([
//                'name' => ['sometimes', 'string', 'min:3', 'max:255'],
//                'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id, 'max:255'],
//                'password' => ['sometimes', 'string', 'min:8'],
//                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
//                'current_password' => ['required_with:password', 'string'],
//            ]);
//
//            // Verify current password if changing password
//            if (isset($validated['password'])) {
//                if (!Hash::check($validated['current_password'], $user->password)) {
//                    return $this->errorResponse('Current password is incorrect', 400);
//                }
//                $validated['password'] = Hash::make($validated['password']);
//                unset($validated['current_password']);
//            }
//
//            // Handle avatar upload
//            if ($request->hasFile('avatar')) {
//                if ($user->avatar) {
//                    Storage::disk('public')->delete($user->avatar);
//                }
//                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
//            }
//
//            $user->update($validated);
//
//            return $this->successResponse(
//                $user->fresh()->makeHidden(['password']),
//                'Profile updated successfully'
//            );
//
//        } catch (ValidationException $e) {
//            return $this->errorResponse('Validation failed', 422, $e->errors());
//        } catch (\Exception $e) {
//            return $this->errorResponse('Profile update failed', 500);
//        }
//    }

    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            $token = $user->createToken('api-token', ['*'], now()->addDays(30))->plainTextToken;

            return $this->successResponse([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(30)->toISOString(),
            ], 'Token refreshed successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Token refresh failed', 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $request->validate([
                'password' => ['required', 'string'],
                'confirmation' => ['required', 'in:DELETE'],
            ]);

            $user = $request->user();

            if (!Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Password is incorrect', 400);
            }

            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Delete all tokens
            $user->tokens()->delete();

            // Delete user
            $user->delete();

            return $this->successResponse(null, 'Account deleted successfully');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Account deletion failed', 500);
        }
    }


    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
            ]);

            // Delete any existing reset tokens for this email
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Generate new token
            $token = Str::random(60);

            // Store the token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            // Send email (you'll need to create this)
            $user = User::where('email', $request->email)->first();

            // For now, we'll return the token in response (remove this in production)
            return $this->successResponse([
                'message' => 'Password reset link sent to your email',
            ], 'Password reset link sent');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send reset link', 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
                'old_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            // Find the user
            $user = User::where('email', $request->email)->first();

            // Verify old password
            if (!Hash::check($request->old_password, $user->password)) {
                return $this->errorResponse('Old password is incorrect', 400);
            }

            // Update user password
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Revoke all existing tokens for security
            $user->tokens()->delete();

            return $this->successResponse(null, 'Password reset successfully');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Password reset failed', 500);
        }
    }

}
