<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductController extends Controller
{
    public function GetProducts(Request $request)
    {
        $result = DB::table('products')
            ->select('products.*')
            ->get();
        return $result;
    }

    public function InsertProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products',
            'description' => 'required',
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            $validationError = $validator->errors()->first();
            return $validationError;
        }

        $Product = new Product;
        $Product->name = $request->name;
        $Product->description = $request->description;
        $Product->category =  $request->category;
        $Product->save();

        return response()->json(['message' => 'success'], 200);
    }
}
