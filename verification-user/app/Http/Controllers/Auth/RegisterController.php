<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    public function register(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()]);
        }
        try{
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->verification_code = sha1(time());
            $user->save();

            if ($user != null) {
                MailController::sendSignupEmail($user->name, $user->email, $user->verification_code);
                return response()->json(["message" => "You have done signup successfully, Please check your email for the varification link"]);
                //return redirect()->back()->with(session()->flash('alert-success','You have done signup successfully, Please check your email for the varification link'));
            }
            return response()->json(["message" => "Something went wrong!!!"]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Something went wrong!!!"]);
        }
    }

    public function verifyUser(Request $request) {
        $verification_code = \Illuminate\Support\Facades\Request::get('code');
        if ($verification_code) {
            $user = User::where(['verification_code' => $verification_code])->first();
            if ($user != null) {
                $user->is_verified = 1;
                $user->save();
                return response()->json(["message" => "You have verified successfully, please login"]);
            }
            return response()->json(["message" => "Verification failed"]);
            
        } else {
            return response()->json(["message" => "Verification failed"]);
        }
        
    }
}
