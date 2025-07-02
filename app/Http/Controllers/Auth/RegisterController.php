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
        // dd($request);
        // Validate the request data first (including profile_picture)
        $validatedData = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'age'  => ['required', 'integer', 'min:18', 'max:100'],
            'gender'         => ['required', 'string'], // Adjust validation as needed
            'bio'            => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:255'], // Add this line
            'interested_in'=>['required', 'string'],
            'salary'=>['required','integer'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);


        // Calculate birth year based on the current year and age
        $currentYear = date('Y');
        $birthYear = $currentYear - $validatedData['age'];
        // Handle the file upload and store it in the 'public' disk under 'profile_pictures'
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            // Add the file path to the validated data array
            $validatedData['profile_picture'] = $path;
        }

        // Create the user using the create method with mass assignment
        $user = User::create([
            'name'           => $validatedData['name'],
            'email'          => $validatedData['email'],
            'password'       => Hash::make($validatedData['password']),
            'gender'         => $validatedData['gender'],
            'age' => $validatedData['age'], // Save age
            'birth_year' => $birthYear, // Save birth year
            'city' => $validatedData['city'], // Add this line
            'interested_in'=>$validatedData['interested_in'],
            'salary'=>$validatedData['salary'],
            'bio'            => $validatedData['bio'] ?? null,
            'profile_picture' => $validatedData['profile_picture'] ?? null,
        ]);


        // Optionally, log the user in or perform further actions
        Auth::login($user);

        return redirect($this->redirectPath());

        // return View('login');
        // return view('auth.login');


    }
}
