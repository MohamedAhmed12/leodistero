<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\Images;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\LoginUserFormRequest;
use App\Http\Requests\RegisterUserFormRequest;
use App\Http\Requests\UpdateProfileFormRequest;

class AuthController extends Controller
{
    /**
     * Authenticate user
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' =>  ['required', 'email'],
            'password' =>  ['required', 'min:1'],
        ]);

        if ($user = User::where('email', $request->email)->first()) {

            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'user' => $user,
                    'token' => $user->createToken(config('app.key'))->accessToken
                ]);
            }
        }

        return response()->json(['message' => 'Record not found'], 422);
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return void
     */
    public function register(RegisterUserFormRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken(config('app.key'))->accessToken
        ]);
    }

    /**
     * user forget password
     *
     * @param Request $request
     * @return void
     */
    public function forget(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        } else {
            return response()->json(['message' => __($status)], 401);
        }
    }

    /**
     * user reset password
     *
     * @param Request $request
     * @return void
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        $res = $status == Password::PASSWORD_RESET ? [['message' => __($status)], 200] : [['message' => __($status)], 401];

        return response()->json(...$res);
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $user = auth()->user()->token();
        $user->revoke();
        return response()->json([], 204);
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return void
     */
    public function me(Request $request)
    {
        return response()->json(auth()->user());
    }

    /**
     * Update Record
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function updateProfile(UpdateProfileFormRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();

        if ($request->input('password') ?? false) {
            $data['password'] = Hash::make('password');
        } else {
            unset($data['password']);
        }
        $data = Arr::where($data, function($val, $key) {
            return !is_null($val);
        });

        $user->update($data);

        return $user;
    }

    /**
     * Update Record
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function checkValidation()
    {
        $user = auth()->user();
        $userAttr = $user->attributesToArray();
        
        if (
            empty($userAttr['country_id']) ||
            empty($userAttr['state_id']) ||
            empty($userAttr['address_line_1']) ||
            empty($userAttr['official_id']) ||
            empty($userAttr['zip_code'])
        ) {
            return 0;
        }
        
        return 1;
    }
}
