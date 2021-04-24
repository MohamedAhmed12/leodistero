<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{

    /**
     * Return User Resoruce
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function show(Request $request, User $user)
    {
        return response()->json($user);
    }

    /**
     * Create New User
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           =>  ['required', 'min:3', 'max:255'],
            'email'          =>  ['required', 'min:3', 'max:255', 'email', 'unique:users,email'],
            'password'       =>  ['nullable', 'min:8', 'max:64', 'confirmed'],
            'phone_number'   =>  ['required', 'min:3', 'max:255', 'unique:users,phone_number'],
            'address_line_1' =>  ['nullable', 'string'],
            'address_line_2' =>  ['nullable', 'string'],
            'company'        =>  ['nullable', 'string'],
            'country_id'     =>  ['nullable', 'exists:countries,id'],
            'city_id'        =>  ['nullable', 'exists:cities,id'],
            'state_id'       =>  ['nullable', 'exists:states,id'],
            'id_front'       =>  ['nullable', 'image'],
            'id_back'        =>  ['nullable', 'image'],
        ]);

        $data = $request->all();


        $user = User::create($data);


        if ($request->has('id_front') && $request->file('id_front') != null) {
            $user->addMedia($request->file('id_front'))->toMediaCollection('id_front');
        }

        if ($request->has('id_back') && $request->file('id_back') != null) {
            $user->addMedia($request->file('id_back'))->toMediaCollection('id_back');
        }

        return $user;
    }

    /**
     * Update Record
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validated();

        if ($request->input('password') ?? false) {
            $data['password'] = Hash::make('password');
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->file('image')) {
            $user->clearMediaCollection('image');
            $user->addMedia($request->file('image')->getRealPath())->usingName($request->file('image')->getClientOriginalName())->toMediaCollection('image');
        }

        $user->refresh();

        return $user;
    }


    /**
     * Destroy Record
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();

        return response()->json([], 204);
    }


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
     * Authenticate user
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {

        $request->validate([
            'name'  =>  ['required', 'string', 'min:3', 'max:25'],
            'email'  =>  ['required', 'email'],
            'password'  =>  ['required', 'min:8', 'max:32', 'confirmed'],
        ]);
        $data = array_merge($request->only('name', 'email', 'password'));

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken(config('app.key'))->accessToken
        ]);
    }




    /**
     * Authenticate user
     *
     * @param Request $request
     * @return void
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([], 204);
        } else {
            return response()->json([], $status);
        }
    }
}
