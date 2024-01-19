<?php

namespace App\Http\Controllers;

use App\User;
use App\Jobs\VerifyEmailJob;
use App\Jobs\ForgotPasswordJob;
use App\Mail\SendVerifyEmail;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','verifyUser','forgotPassword','resetPassword']]);
    }

    public function register(Request $request){
        // dd($request,'jj');
        $validator =Validator::make($request->all(),[
            "name"=>"required|string",
            "email"=>"required|email|unique:users",
            "password"=>"required|min:6|max:30|confirmed",
        ]);

        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);     

        }
        try{
            $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);
            $credentials = $request->only('email', 'password');

            $myTTL = 30; //minutes
            JWTAuth::factory()->setTTL($myTTL);
            $access_token = JWTAuth::attempt($credentials);

            $encodedAccessToken = base64_encode($access_token); 
            $user->jwt_token = $encodedAccessToken;
            $user->save();
            $email_obj = new SendVerifyEmail($user, $encodedAccessToken);
            dispatch(new VerifyEmailJob($user, $email_obj ,$encodedAccessToken));

            $new_user= User::where('email',$request->email) -> first();
            return response()->json([
                "success"=>true,
                'message'=>'User registered successfully. please check your email',
                'user'=>$new_user,
                'token'=>$access_token
            ],201);

        } 
        catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>$e->getMessage()
            ],400);
        } 
    }


    public function login(Request $request){
        $validator =Validator::make($request->all(),[
            "email"=>"required|email",
            "password"=>"required|min:6|max:30",
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
                $credentials = $request->only('email', 'password');

                $myTTL = 30; //minutes
                JWTAuth::factory()->setTTL($myTTL);

                if(!$token= JWTAuth::attempt($credentials)){
                    return response()->json(['error'=> "Unmatched credential"], 401);
                }

                $verified_at=Auth::user()->email_verified_at;
                if($verified_at !==null){
                    return response()->json([
                        "success"=>true,
                        'message'=>'User login successfully',
                        'user'=>Auth::user(),
                        'token'=>$token
                    ],201);
                }
                else{
                    return response()->json([
                        "success"=>false,
                        'message'=>'User is not verified',
                        'user'=>Auth::user(),
                        'token'=>$token
                    ],201);
                }
            } 
        catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }  
    }


    public function logout(){
        Auth::logout();
        return response()->json([
            'message'=>'User logged out successfully',
            
        ]);
    }
    
    public function verifyUser(Request $request){
        $email = $request->email;
        $encrypted_token = $request->token;
        $decrypted_token = base64_decode($encrypted_token);

        $user = User::where('email', $email)->first();
        if(!$user || $user->jwt_token != $encrypted_token) {
            return response()->json([
                "success"=>false,
                'message'=>'Invalid Request',
            ]);
        }
        else {
            if($user->email_verified_at) {
                return response()->json([
                    "success"=>false,
                    'message'=>'email already verified',
                    'user'=>$user,
                    'token'=>$decrypted_token,  
                ]);
            }
            else{
                $user->email_verified_at = now();
                $user->save();
                return response()->json([
                    "success"=>true,
                    'message'=>'email verified successfully',
                    'user'=>$user,
                    'token'=>$decrypted_token,  
                ]);
            }
        }
    }


    public function forgotPassword(Request $request){
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if($user){
            $token = uniqid();
            $user->reset_token = $token;
            $user->save();
            $encodedEmail = base64_encode($user->email);
            $encodedToken = base64_encode($token);
            dispatch(new ForgotPasswordJob($user->email, $encodedEmail, $encodedToken));
            return response()->json([
                "success"=>true,
                'message'=>'A password reset link has been sent to your mail' 
            ]);
        }
        else{
            return response()->json([
                "success"=>false,
                'message'=>'your email is not registered yet, register first!!' 
            ]);
        }
    }

    
    public function resetPassword(Request $request){
        $user = User::where('email', $request->email)->firstOrFail();
        if($user->reset_token != $request->token){
            return response()->json([
                "success"=>false,
                'message'=>'Invalid request' 
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->reset_token = uniqid();
        $user->save();
        return response()->json([
            "success"=>true,
            'message'=>'password reset successfully'  
        ]);
    }
}
