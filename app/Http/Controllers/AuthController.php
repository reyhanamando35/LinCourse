<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegisterStudent()
    {
        return view('auth.student.register');
    }

    public function showLoginStudent()
    {
        return view('auth.student.login');
    }

    public function showRegisterTeacher()
    {
        return view('auth.teacher.register');
    }

    public function showLoginTeacher()
    {
        return view('auth.teacher.login');
    }

    public function registerStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'grade' => 'required|integer|min:7|max:12',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                $user->student()->create([
                    'grade' => $request->grade,
                ]);

                Auth::login($user);
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Registrasi failed, please try again.')->withInput();
        }

        return redirect()->route('dashboard')->with('success', 'Student registration success!');
    }

    public function loginStudent(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->admin) {
                $request->session()->regenerate();
                return redirect()->route('dashboard')->with('success', 'Admin login successful!');
            } 
            elseif ($user->student) {
                $request->session()->regenerate();
                return redirect()->route('dashboard')->with('success', 'Student login successful!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account is not registered as a student.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'Wrong email or password.',
        ])->onlyInput('email');
    }

    public function registerTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'experience_years' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                $user->teacher()->create([
                    'experience_years' => $request->experience_years,
                ]);

                Auth::login($user);
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Registrasi failed, please try again.')->withInput();
        }

        return redirect()->route('dashboard')->with('success', 'Teacher registration success!');
    }

    public function loginTeacher(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->admin) {
                $request->session()->regenerate();
                return redirect()->route('dashboard')->with('success', 'Admin login successful!');
            }
            elseif ($user->teacher) {
                $request->session()->regenerate();
                return redirect()->route('dashboard')->with('success', 'Teacher login successful!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account is not registered as a teacher.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'Wrong email or password.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke route 'index' dengan pesan sukses
        return redirect()->route('index')->with('success', 'Logout successful!');
    }
}
