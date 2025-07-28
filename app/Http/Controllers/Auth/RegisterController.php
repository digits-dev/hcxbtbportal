<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\AdmModels\AdmPrivileges;
use App\Models\AdmModels\AdmUserProfiles;
use App\Models\AdmUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class RegisterController extends Controller
{
   public function sendEmailVerificationInstructions(Request $request)
    {
        $validatedFields = $request->validate([
            'register_email' => 'required|email|max:50|unique:adm_users,email',
        ], [
            'register_email.required' => 'Please add Email',
            'register_email.email' => 'Email must be a valid email address',
            'register_email.max' => 'Email may not be greater than 50 characters',
            'register_email.unique' => 'The email address is already registered.',
        ]);

        
        $key = Str::random(32);
        $iv = Str::random(16);

        
        $encryptedEmail = openssl_encrypt($validatedFields['register_email'], 'aes-256-cbc', $key, 0, $iv);
        $encryptedEmailBase64 = base64_encode($encryptedEmail);
        
        session(['encryption_key' => $key, 'encryption_iv' => $iv]);

        $cleanEncryptedEmail = str_replace('/', '_', $encryptedEmailBase64);
        
        Mail::to($validatedFields['register_email'])->send(new EmailVerificationMail($validatedFields['register_email'], $cleanEncryptedEmail));
        
        
    }

    public function getRegisterIndex($email){
        $key = session('encryption_key');
        $iv = session('encryption_iv');
  
        if (!$key || !$iv) {
            return Inertia::render('Auth/RegisterLinkExpired');
        }

        $encryptedEmail = base64_decode(str_replace('_', '/', $email));
        $decryptedEmail = openssl_decrypt($encryptedEmail, 'aes-256-cbc', $key, 0, $iv);
   
        if ($decryptedEmail === false) {
            return Inertia::render('Auth/RegisterLinkExpired');
        }
        
        return Inertia::render('Auth/Register', [
            'email' => $decryptedEmail
        ]);
    }

    public function registerSubmit(Request $request){

        $validatedFields = $request->validate([
            'first_name' => 'required|string|max:15|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:15|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:50|unique:adm_users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'first_name.required' => 'Please add First Name',
            'first_name.string' => 'First Name must be a text',
            'first_name.max' => 'First Name may not be greater than 15 characters',
            'first_name.regex' => 'First Name must contain only letters',
            
            'last_name.required' => 'Please add Last Name',
            'last_name.string' => 'Last Name must be a text',
            'last_name.max' => 'Last Name may not be greater than 15 characters',
            'last_name.regex' => 'Last Name must contain only letters',
            
            'email.required' => 'Please add Email',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email may not be greater than 50 characters',
            'email.unique' => 'The email address is already registered.',

            'password.required' => 'Please add Password',
            'password.min' => 'Password must be at least 8 characters',
            
            'confirm_password.required' => 'Please confirm your password',
            'confirm_password.min' => 'Password must be at least 8 characters',
            'confirm_password.same' => 'Confirmation Password does not match the password',
        ]);
        

        $user = AdmUser::create([
            'name' => $validatedFields['first_name'] . ' ' . $validatedFields['last_name'], 
            'email' => $validatedFields['email'], 
            'email_verified_at' => now(),
            'theme' => 'skin-blue',
            'status' => 'INACTIVE',
            'waiver_count'=> '0',
            'id_adm_privileges' => AdmPrivileges::HOMECREDITSTAFF,
            'password' => Hash::make($validatedFields['password']), 
        ]);

        $file = $request->file('profile_photo');

        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('profile_pictures', $fileName, 'public');

        AdmUserProfiles::create([
            'adm_user_id' => $user->id, 
            'file_name' => $path, 
            'created_by' => $user->id,
        ]);

    }

    public function forgetSessionKey(){
        session()->forget('encryption_key');
		session()->forget('encryption_iv');

        return redirect('/login');
    }

}
