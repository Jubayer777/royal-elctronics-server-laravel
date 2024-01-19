<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(){
        $categories =Category::with(array('product','product.brand'))->get();
        return response()->json([
            "success"=>true,
            "message"=>"Showing all categories!",
            "data"=>$categories
        ]);
    }
    
    public function productsByCategory($id){
        try{
            $category=Category::with(array('product','product.brand'))->findOrFail($id);
            return response()->json([
                'message'=>'getting all product by specific category',
                'data'=>$category
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }


    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'category_name'=>"required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            $category= new Category();
            $category->category_name= $request->category_name;
            $category->save();
            return response()->json([
                "success"=>true,
                "message"=>"Category added successfully!!"
            ]);

        } catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'category_name'=>"required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            $category= Category::findOrFail($id);
            $category->category_name= $request->category_name;
            $category->save();
            return response()->json([
                "success"=>true,
                "message"=>"Category updated successfully!!"
            ]);

        } catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }
    }


    public function destroy($id){
        try{
            $result= Category::with('product')->findOrFail($id);
            $products =$result->product;      
            foreach ($products as $product) {
                $p_id = $product->id;
                Product::findOrFail($p_id)->delete();
            }
            $result->delete();
            
            return response()->json([
                'message'=>'Category deleted successfully'
                
            ]);

        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }
    
}
