<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{

    public function index(){
        $admins =Admin::with('user')->get();
        return response()->json([
            "success"=>true,
            "message"=>"Showing all admin!",
            "data"=>$admins
        ]);
    }


    public function store(Request $request){
        $validator =Validator::make($request->all(),[
            "email"=>"required|email"
            
        ]);
        if($validator->fails())
        {
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            $user = User::where('email', '=', $request->input('email'))->first();
            if($user ===null){

                return response()->json([
                    "success"=>false,
                    "message"=>"This email isn't registered yet!!"
                ],401);

            }
            else{
                $user_id = User::where('email',$request->email) -> first()->id;
                if(Admin::where('user_id', '=', $user_id)->exists()){
                    return response()->json([
                        "success"=>false,
                        "message"=>'This user is already an admin'
                    ],401);
                }
                else{
                    $admin = new Admin();
                    $admin->user_id=$user_id;
                    $admin->save();
                    return response()->json([
                        "success"=>true,
                        'message'=>'Admin added successfully',
                    ],201);
                }
            }

        } 
        catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>$e->getMessage()
            ],400);
        }
    }


    public function checkAdmin(Request $request){
        $validator =Validator::make($request->all(),[
            "user_id"=>"required"
        ]);
        if($validator->fails())
        {
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            if(Admin::where('user_id','=', $request->user_id)->exists()){
                return response()->json([
                    "success"=>true,
                    "message"=>"This user is admin"
                ]);
            }
            else{
                return response()->json([
                    "success"=>false,
                    "message"=>"This user is not admin"
                ],400);
            }

        } catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }
    }
    

    public function destroy($id){
        try{
            $admin= Admin::with('user')->findOrFail($id);
            User::findOrFail($admin->user_id)->delete();
            $admin->delete();
            return response()->json([
                'message'=>'Admin removed successfully'
                
            ]);

        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }
    
}
