<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductImages;

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
        $form = json_decode($request->form, true);
        $validator = Validator::make($form, [
            'name' => 'required',
            'description' => 'required',
            'category'  => 'required',
            'date_and_time'  => 'required',
        ]);

        // picture validation
        if (!isset($request['files'])) {
            $validator->sometimes('files', 'required|array', function () {
                return true;
            });
            $customMessages = [
                'files.required' => 'Upload picture is required!',
            ];
            $validator->setCustomMessages($customMessages);
        }

        if ($validator->fails()) {
            $validationError = $validator->errors()->first();
            return $validationError;
        }

        $Product = new Product;
        $Product->name = $form['name'];
        $Product->description = $form['description'];
        $Product->category =  $form['category'];
        $Product->date_and_time =  $form['date_and_time'];
        $Product->save();

        foreach ($request->file('files') as $file) {
            $ProductImages = new ProductImages();
            $file_name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $name = explode('.', $file_name)[0] . '-' . uniqid() . '.' . $ext;
            $name = str_replace(' ', '', $name);
            $file->move(public_path('product_pictures'), $name);
            $ProductImages->product_id = $Product->id;
            $ProductImages->path = '/product_pictures/' . $name;
            $ProductImages->save();
        }
        return 'success';
    }
}
