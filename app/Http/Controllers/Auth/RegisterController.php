<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/home';

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
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'date_of_birth' => ['required','date'],
    //         'profile_picture' => ['required','image','mimes:jpeg,png,jpg,gif','max:2048'], // Max 2MB

    //     ]);
    // }

    // /**
    //  * Create a new user instance after a valid registration.
    //  *
    //  * @param  array  $data
    //  * @return \App\Models\User
    //  */
    // protected function create(array $data )
    // {
    //         $user=User::create([
    //             'name' => $data['name'],
    //             'email' => $data['email'],
    //             'date_of_birth' => $data['date_of_birth'],
    //             'password' => Hash::make($data['password']),
    //             'gender' => $data['gender'],
    //             'bio' => $data['bio'],

    //         ]);


    //         if ($request->hasFile('profile_picture')) {
    //          // Store the new picture
    //          $path = $request->file('profile_picture')->store('profile_pictures', 'public');
    //          $user->profile_picture = $path;
    //          $user->save();
    //         }

    //         if (!$user) {
    //             Log::error('User creation failed', $data); // Log the error
    //         }
    // Auth::login($user);
    //                 // return redirect()->back()->with('success', 'Profile picture uploaded successfully!');

    // return $user;
    //         // return redirect()->route('login')->with('success', 'Registration successful! Please login.');

    //     }



    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'age'            => ['required', 'integer', 'min:18', 'max:100'],
            'gender'         => ['required', 'string'],
            'bio'            => ['nullable', 'string'],
            'city'           => ['required', 'string', 'max:255'],
            'interested_in'  => ['required', 'string'],
            'salary'         => ['required', 'integer'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $currentYear = date('Y');
        $birthYear = $currentYear - $validatedData['age'];

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }

        $user = User::create([
            'name'            => $validatedData['name'],
            'email'           => $validatedData['email'],
            'password'        => Hash::make($validatedData['password']),
            'gender'          => $validatedData['gender'],
            'age'             => $validatedData['age'],
            'birth_year'      => $birthYear,
            'city'            => $validatedData['city'],
            'interested_in'   => $validatedData['interested_in'],
            'salary'          => $validatedData['salary'],
            'marital_status'  => $validatedData['marital_status'] ?? null,
            'bio'             => $validatedData['bio'] ?? null,
            'profile_picture' => $validatedData['profile_picture'] ?? null,
        ]);

        Auth::login($user);

        return redirect($this->redirectPath());
    }
}
