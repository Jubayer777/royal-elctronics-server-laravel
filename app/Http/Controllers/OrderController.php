<?php

namespace App\Http\Controllers;

use App\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index(){
        $orders=Order::with('user','products')->get();
        return response()->json([
            "success"=>true,
            "message"=>"Showing all products!",
            "data"=>$orders
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'products'=>"required|array",
            'user_id'=>"required",
            'total'=>"required",
            'status'=>"required|string",
            'payment_id'=>"required",
            'address'=>"required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
        
            $order = new Order();
            $order->user_id= $request->input('user_id');
            $order->payment_id= $request->input('payment_id');
            $order->total= $request->input('total');
            $order->status= $request->input('status');
            $order->address= $request->input('address');
            $order->save();
            $products=$request->input('products');
            $newOrder=$order;
            foreach ($products as $product) {
                $product_id=$product['product_id'];
                $quantity=$product['quantity'];
                $newOrder->products()->attach($product_id,['quantity' => $quantity]);
            }
            return response()->json([
                "success"=>true,
                "message"=>"order placed successfully!!"
            ]);
        } 
        catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            
            'user_id'=>"required",
            'total'=>"required",
            'status'=>"required|string",
            'payment_id'=>"required",
            'address'=>"required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            
            $order=Order::findOrFail($id);
            $order->user_id= $request->input('user_id');
            $order->payment_id= $request->input('payment_id');
            $order->total= $request->input('total');
            $order->status= $request->input('status');
            $order->address= $request->input('address');
            $order->save();
            return response()->json([
                "success"=>true,
                "message"=>"order updated successfully!!"
            ]);

        } 
        catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }
    }


    public function ordersByUser($id){
        try{
            $orders=Order::with('user','products')->where('user_id',$id)->get();
            return response()->json([
                'message'=>'getting all orders by specific user',
                'data'=>$orders
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }

    public function destroy($id){
        try{
            $result= Order::findOrFail($id);
            $result->products()->detach();
            $result->delete();
            return response()->json([
                'message'=>'Order deleted successfully'
                
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }
    
    
}
