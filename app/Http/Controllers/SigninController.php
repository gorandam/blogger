<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

class SigninController extends Controller
{
    public function signin(Request $request)
    {
      //First we validate our signin data
      $this->validate($request, [
         'email' => 'required|email',
         'password' => 'required'
      ]);
      if(Auth::attempt([
        'email' => $request->input('email'),
        'password' => $request->input('password'),
      ], $request->has('remember'))) { // here if request has rembember key Laravel will automaticly create and store remember token and cookie for us
        return redirect()->route('admin.index');
      }
      return redirect()->back()->with('fail', 'Authentication failed!');
    }
}
