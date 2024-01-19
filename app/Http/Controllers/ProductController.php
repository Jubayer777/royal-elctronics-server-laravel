<?php

namespace App\Http\Controllers;

use App\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class ProductController extends Controller
{
    
    public function index(){
        $products =Product::with('category','brand')->get();
        return response()->json([
            "success"=>true,
            "message"=>"Showing all products!",
            "data"=>$products
        ]);
    }

    public function search($name){
        $products =Product::where('product_name', 'LIKE', "%{$name}%")->get();
        if(count($products)){
            return response()->json([
                "success"=>true,
                "message"=>"Showing all matching products!",
                "data"=>$products
            ]);
        }
        else
        {
            return response()->json([
                "success"=>false,
                "message"=>"No products matched!"
            ]);
        }
    }

    public function latest(){
        $products =Product::with('category','brand')->orderBy('id', 'desc')->take(10)->get();
        return response()->json([
            "success"=>true,
            "message"=>"Showing all latest products!",
            "data"=>$products
        ]);
    }
    

    public function store(Request $request){
        
        $validator = Validator::make($request->all(),[
            'product_name'=>"required|string",
            'description'=>"required",
            'price'=>"required",
            'category_id'=>"required",
            'brand_id'=>"required",
            'image' => 'required|image|nullable|max:2000',
            'product_quantity'=>"required",
        ]);
        if($validator->fails()){
            return response()->json([
                "success"=>false,
                "errors"=>$validator->errors()
            ],401);
        }

        if($request->hasFile('image')){
            $file= $request->file('image');
            $filename= $file->getClientOriginalName();
            $fileNameToStore = date('His').'-'.$filename;
            $file->move(public_path('upload'),$fileNameToStore);
        } 

        try{
            $product = new Product();
            $product->product_name= $request->input('product_name');
            $product->description= $request->input('description');
            $product->price= $request->input('price');
            $product->category_id= $request->input('category_id');
            $product->brand_id= $request->input('brand_id');
            $product->image= $fileNameToStore;
            $product->product_quantity= $request->input('product_quantity');
            $product->save();
            return response()->json([
                "success"=>true,
                "message"=>"Product added successfully!!"
            ]);
        } 
        catch(Exception $e){
            return response()->json([
                "success"=>false,
                "message"=>"Something wrong!"
            ],400);
        }
    }

    public function show($id)
    {
        try{
            $product=Product::with('category','brand')->findOrFail($id);
            return response()->json([
                'message'=>'getting specific product',
                'data'=>$product
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }

    
    public function update(Request $request, $id)
    {
        $validator=Validator::make($request->all(),[
            'product_name'=>"required|string",
            'description'=>"required",
            'price'=>"required",
            'category_id'=>"required",
            'brand_id'=>"required",
            'image' => 'required|nullable|max:2000',
            'product_quantity'=>"required",
        ]);
        if($validator->fails())
            {
                return response()->json([
                    "success"=>false,
                    "errors"=>$validator->errors()
                ],401);
            }

        if($request->hasFile('image')){
            $file= $request->file('image');
            $filename= $file->getClientOriginalName();
            $fileNameToStore = date('His').'-'.$filename;
            $file->move(public_path('upload'),$fileNameToStore);
        }else{
            $fileNameToStore=$request->input('image');
        }
        try{
            $product=Product::findOrFail($id);
            $product->product_name= $request->input('product_name');
            $product->description= $request->input('description');
            $product->price= $request->input('price');
            $product->category_id= $request->input('category_id');
            $product->brand_id= $request->input('brand_id');
            $product->image= $fileNameToStore;
            $product->product_quantity= $request->input('product_quantity');
            $product->save();
            return response()->json([
                "message"=>"Product updated successfully",
                "data"=>$product
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
            Product::findOrFail($id)->delete();
            return response()->json([
                'message'=>'Product deleted successfully'
            ]);
        } catch(Exception $e){
            return response()->json([
                "message"=>"something wrong.."
            ],400);
        }
    }

    
}
