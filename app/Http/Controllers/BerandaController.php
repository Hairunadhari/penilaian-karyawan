<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index(){
        $user = Auth::user();
        if ($user != null) {
            if ($user->role == 'admin' || $user->role == 'hrd') {
                Auth::logout();
            }
        }
        return view('frontend.beranda');
    }
    public function beranda(){
        $user = Auth::user();
        if ($user->role == 'admin' || $user->role == 'hrd') {
            Auth::logout();
            return redirect('/');
        }
        $penilaian = Penilaian::where('user_id',$user->id)->first();
        return view('frontend.beranda1',compact('penilaian'));
    }
}
