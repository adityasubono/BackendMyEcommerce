<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category' => 'required',
            'brand' => 'required',
            'desc' => 'required',
            'image' => 'required|image',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 409);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->brand = $request->brand;
        $product->desc = $request->desc;
        $product->price = $request->price;
        $product->save();
        //Storing Image
        $url = "http://localhost:8000/storage/";
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $path = $request->file('image')->storeAs('proimages/', $product->id. '.' . $extension);
        $product->image = $path;
        $product->imgpath = $url . $path;
        $product->save();
        return response()->json(['message' => 'Product Successfully Added']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'category' => 'required',
            'brand' => 'required',
            'desc' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 409);
        }

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->category = $request->category;
        $product->brand = $request->brand;
        $product->desc = $request->desc;
        $product->price = $request->price;
        $product->save();
        return response()->json(['message' => 'Product Successfully Update']);

    }

    public function delete(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 409);
        }

        $data = Product::find($request->id);
        Storage::disk('public')->delete($data->image);
        $data->delete();

        return response()->json(['message' => 'Product Successfully Deleted By ID: '.$request->id]);

    }

    public function show(Request $request)
    {
        session(['keys' => $request->keys]);
        $products = Product::where(function ($query) {
            $query->where('products.id', 'LIKE', '%' . session('keys') . '%')
                ->orwhere('products.name', 'LIKE', '%' . session('keys') . '%')
                ->orwhere('products.price', 'LIKE', '%' . session('keys') . '%')
                ->orwhere('products.category', 'LIKE', '%' . session('keys') . '%')
                ->orwhere('products.brand', 'LIKE', '%' . session('keys') . '%');
        })->select('products.*')->get();
        return response()->json(['products' => $products]);
    }

    public function getData()
    {
        $products = Product::all();
        return response()->json(['products' => $products]);
    }
}
