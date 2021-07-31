<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::latest()->get();
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'    => 'required',
                'description' => 'required|string|min:20',
                'price' => 'required',
                'image' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        if ($request->has('image')) {
            $image=$request->image;
            $extname=$image->getClientOriginalExtension();
            $img_name = substr(md5(uniqid(rand(1,6))).microtime(true), 0, 15).'.'.$extname;
            $image->move(public_path('images'), $img_name);
            $product->image = $img_name;
        }
        $product->save();
        return response(new ProductResource($product), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $pro)
    {
        $product=Product::where('id',$pro)->first();

        if ($request->has('title')) {
            $product->title=$request->title;          
        }
        if ($request->has('description')) {
            $product->description=$request->description;               
        }
        if ($request->has('price')) {
            $product->price=$request->price;
        }
        if ($request->file('image')) {
            if ($product->image) {
                $path = public_path() . "/images/" . $product->image;
                unlink($path);
                $this->storeImg($request->image,$product);
            } else {
                $this->storeImg($request->image,$product);
            }
        }
        $product->save();

        return response()->json(["success"=>[
            "message"=>"Product Updated Successfully",
            "date"=>new ProductResource($product)
        ]],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {   
        if($product){
            if($product->image) {
                $path = public_path() . "/images/" . $product->image;
                unlink($path);
            }
            $product->delete();
            return response('deleted', Response::HTTP_NO_CONTENT);
        }else{
            return response('No product found', Response::HTTP_BAD_REQUEST);
        }
       
    }
}
