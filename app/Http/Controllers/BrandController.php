<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(){
        $brands =Brand::with(array('product','product.category'))->get();
        return response()->json([
            "success"=>true,
            "message"=>"Showing all brands!",
            "data"=>$brands
        ]);
    }


    public function productsByBrand($id){ 
        try{
            $brand=Brand::with(array('product','product.category'))->findOrFail($id);
            return response()->json([
                'message'=>'getting all product by specific brand',
                'data'=>$brand
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }


    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'brand_name'=>"required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            $brand= new Brand();
            $brand->brand_name= $request->brand_name;
            $brand->save();
            return response()->json([
                "success"=>true,
                "message"=>"Brand added successfully!!"
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
            'brand_name'=>"required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }
        try{
            $brand= Brand::findOrFail($id);
            $brand->brand_name= $request->brand_name;
            $brand->save();
            return response()->json([
                "success"=>true,
                "message"=>"Brand updated successfully!!"
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
            $result= Brand::with('product')->findOrFail($id);
            $products =$result->product;
            
            // Product::findOrFail($products[0]->id)->delete();
            
            foreach ($products as $product) {
                $p_id = $product->id;
                Product::findOrFail($p_id)->delete();
            }
            $result->delete();
            
            return response()->json([
                'message'=>'Brand deleted successfully'
                
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }
    
}
