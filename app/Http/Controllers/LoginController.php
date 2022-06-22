<?php

namespace App\Http\Controllers;

use app\Http\Requests\LoginRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $comp_name;
    protected $comp_code;

    public function index(){
        return view('login');
    }

    public function postLogin(Request $request){
        //Authentification is user and password are correct or not
        $hash = Hash::make($request->password);
        // dd($hash, request()->all());
        if(Auth::attempt($request->only('username', 'password'))){
            $request->session()->regenerate();
            $data = $request->input();

            $username = Auth::User()->username;
            $comp_name = Auth::User()->comp_name;
            $comp_code = Auth::User()->comp_code;
            $request->session()->put('comp_name', $comp_name);
            $request->session()->put('username', $username);
            $request->session()->put('comp_code', $comp_code);

            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('userlog')->insert(['username' =>Auth::user()->name, 'tbl'=>'ONLINE', 'idtbl'=> '0', 'notbl'=>'', 'act'=>'LOGIN', 'comp_code'=>$comp_code, 'usin'=>1,'datein'=>$current_date_time]);
            
            return redirect()->intended('/pemasukan');
        }
        return redirect('/');
    }

    public function logout(request $request){
        Auth::logout();
        $current_date_time = Carbon::now()->toDateTimeString();
        DB::table('userlog')->insert(['username' =>session()->get('username'), 'tbl'=>'ONLINE', 'idtbl'=> '0', 'notbl'=>'', 'act'=>'LOGOUT', 'comp_code'=>session()->get('comp_code'), 'usin'=>1,'datein'=>$current_date_time]);

        return redirect('/');
    }
}
