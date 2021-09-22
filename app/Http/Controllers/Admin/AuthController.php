<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::check()){
            return redirect('admin/dashboard');
        }
        if ($request->method() == "POST") {
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                return redirect('admin/dashboard');
            }
        }
        return view('admin/auth/login');
    }

    /**
     * Logout
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('admin/login');
    }
}
