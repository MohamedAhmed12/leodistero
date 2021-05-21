<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('pages.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        $cities = City::all();

        return view('pages.users.create',[
            'countries'=>$countries,
            'cities'=>$cities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           =>  ['required','min:3','max:255'],
            'email'          =>  ['required','min:3','max:255','email','unique:users,email'],
            'password'       =>  ['nullable','min:8','max:64','confirmed'],
            'phone_number'   =>  ['required','min:3','max:255','unique:users,phone_number'],
            'address_line_1' =>  ['nullable','string'],
            'address_line_2' =>  ['nullable','string'],
            'company'        =>  ['nullable','string'],
            'country_id'     =>  ['nullable','exists:countries,id'],
            'city_id'        =>  ['nullable','exists:cities,id'],
            'id_front'       =>  ['nullable','image'],
            'id_back'        =>  ['nullable','image'],
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone_number = $request->phone_number;
        $user->address_line_1 = $request->address_line_1;
        $user->address_line_2 = $request->address_line_2;
        $user->company = $request->company;
        $user->country_id = $request->country_id;
        $user->city_id = $request->city_id;
        $user->zip_code = $request->zip_code;


        $user->save();

        if ($request->has('id_front') && $request->file('id_front') !=null) {
            $user->addMedia($request->file('id_front'))->toMediaCollection('id_front');
        }

        if ($request->has('id_back') && $request->file('id_back') !=null) {
            $user->addMedia($request->file('id_back'))->toMediaCollection('id_back');
        }

        return redirect()->route('users.show',$user->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('pages.users.show',[
            'user'=>$user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $countries = Country::all();
        $cities = City::all();

        return view('pages.users.edit',[
            'user'=>$user,
            'countries'=>$countries,
            'cities'=>$cities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'           => ['required','min:3','max:255'],
            'email'          => ['required','min:3','max:255','email','unique:users,email,'.$user->id],
            'password'       => ['nullable','min:8','max:64','confirmed'],
            'phone_number'   => ['required','min:3','max:255','unique:users,phone_number,'.$user->id],
            'address_line_1' => ['nullable','string'],
            'address_line_2' => ['nullable','string'],
            'company'        => ['nullable','string'],
            'country_id'     => ['nullable','exists:countries,id'],
            'city_id'        => ['nullable','exists:cities,id'],
            'zip_code'       => ['nullable','max:10'],
            'id_front'       => ['nullable','image'],
            'id_back'        => ['nullable','image'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address_line_1 = $request->address_line_1;
        $user->address_line_2 = $request->address_line_2;
        $user->company = $request->company;
        $user->country_id = $request->country_id;
        $user->city_id = $request->city_id;
        $user->zip_code = $request->zip_code;

        if($request->has('password') && $request->get('password',false) == true){
            $user->password = Hash::make($request->password);
        }

        if ($request->has('id_front') && $request->file('id_front') !=null) {
            $user->clearMediaCollection('id_front');
            $user->addMedia($request->file('id_front'))->toMediaCollection('id_front');
        }

        if ($request->has('id_back') && $request->file('id_back') !=null) {
            $user->clearMediaCollection('id_back');
            $user->addMedia($request->file('id_back'))->toMediaCollection('id_back');
        }


        $user->save();

        return redirect()->route('users.show',$user->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try{
            // if($user->()->count()==0){
                $user->delete();
            // }else{
                // session()->flash('can_not_be_deleted','Can not be deleted as there is at least one order associated to this user');
            // }
        }catch(\Exception $ex){}

        return redirect()->route('users.index');
    }

    /**
     * Change Password Form
     *
     * @param Request $request
     * @return void
     */
    public function change_password_form(Request $request){
        return view('pages.users.change_password');
    }

    /**
     * Change Password
     *
     * @param Request $request
     * @return void
     */
    public function change_password(Request $request){
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'password'=>['required','confirmed'],
        ]);

        auth()->user()->update(['password'=>Hash::make($request->password)]);

        return redirect()->back()->withMessage('Password Updated');
    }


    /**
     * Change Password Form
     *
     * @param Request $request
     * @return void
     */
    public function profile_form(Request $request){
        return view('pages.users.profile');
    }

    /**
     * Change Password
     *
     * @param Request $request
     * @return void
     */
    public function update_profile(Request $request){
        $request->validate([
            'name'=>['required','min:0','max:64']
        ]);

        auth()->user()->update($request->only([
            'name'
        ]));

        return redirect()->back()->withMessage('Name Updated');
    }
}
