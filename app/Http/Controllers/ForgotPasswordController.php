<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\forgotpasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ResetCode;

class ForgotPasswordController extends Controller
{
    public function forgot(){
        $user = User::where('email',request('email'))->first();
    
        if ($user){
        $code = mt_rand(1000, 9999);

        
            $resetCode = ResetCode::create([
               'user_id'=>$user->id,
               'code' => $code,
            ]);
           
            Mail::to($user->email)->send(new forgotpasswordMail($user->name,$resetCode->code));
        return response()->json(['message' => 'check your mail for password reset code'], 200);


    } else {
        return response()->json(['message' => 'Invalid email'], 401);
    }
}



public function Check(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'code' => 'required|exists:reset_codes,code', // Correct table name and column name
        'password' => 'required',
    ]);

    if ($validatedData) {
        // Find the reset code
        $resetcode = ResetCode::where('code', $request->input('code'))->first();

        if ($resetcode) {
            $user = User::find($resetcode->user_id);

            if ($user) {
                // Update the user's password
                $user->update([
                    'password' => bcrypt($request->input('password')),
                ]);

                // Delete the used reset code
                $resetcode->delete();

                return response()->json(['message' => 'Password reset successfully'],200);
            } else {
                return response()->json(['message' => 'User not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Reset code not found'], 404);
        }
    } else {
        return response()->json(['message' => 'Validation error'], 400);
    }
}
}