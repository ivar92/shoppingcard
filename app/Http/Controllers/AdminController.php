<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Admistratror;
use Illuminate\Support\Facades\Hash;
class AdminController extends Controller
{
    public function dashboard(){
        if(!Session::has('adminitrator')){
            return view('admin.dashboard');
            
         }else {
            return view('admin.connecter'); # code...
         }
        
    }

    public function login(){
        return view('admin.connecter');
    }

    public function signup(){
        return view('admin.senregistrer');
    }

    public function disconnected(){
        Session::forget('admistratrors');

        return view('admin.connecter');
    }

    public function acceder_compt(Request $request){
        
        $this->validate($request, [
                                    'nom' => 'required',
                                    'prenom' => 'required',
                                    'email' => 'required|email|unique:admistratrors',
                                    'password' => 'required|min:6']);
        $administrator = new Admistratror();
        $administrator->nom = $request->input('nom');
        $administrator->prenom = $request->input('prenom');
        $administrator->email = $request->input('email');
        $administrator->password = bcrypt($request->input('password'));

        $administrator->save();
        return back()->with('status', 'Votre compte Administrateur a été créé avec succès !!');

    }

    public function accee_compte(Request $request){
        $this->validate($request, ['email' =>'required',
        'password' => 'required']);

        $administrator = Admistratror::where('email', $request->input('email'))->first();

        if ($administrator) {
            if(Hash::check($request->input('password'), $administrator->password)) {
                Session::put('admistratrors', $administrator);
                return view('/admin.dashboard');
            } else {
                return back()->with('status', 'Mauvais mot de passe ou email');
            }

        } else {
           return back()->with('status', 'Pas de compte avec cet email, veuillez créer un compte.');
        }
    }


}
