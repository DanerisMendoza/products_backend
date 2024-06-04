<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductImages;

class ProductController extends Controller
{
    public function DeleteProduct(Request $request)
    {
        $id = $request['product_id'];
        $Product = Product::findOrFail($id);
        if (!$Product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $Product->delete();
        return 'success';
    }

    public function UpdateProduct(Request $request)
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

        // delete selected product and images
        $Product = Product::findOrFail($form['id']);
        $Product->delete();

        $ProductImages = ProductImages::where('product_id', $form['id']);
        $ProductImages->delete();


        // add new

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


    public function GetProducts(Request $request)
    {
        // \Log::info($request);
        $perPage = $request->input('perPage', 5);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $sortBy = $request->input('sortBy');
        $sortDesc = $request->input('sortDesc');
        $category = $request->input('category');

        // Create a base query to reuse
        $baseQuery = DB::table('products')
            ->select('products.*')
            // search filtering
            ->when(!!$search, function ($q) use ($search) {
                $q->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('products.name', 'like', '%' . $search . '%')
                        ->orWhere('products.description', 'like', '%' . $search . '%');
                });
            })
            // category filtering
            ->when($category, function ($q) use ($category) {
                $q->where('products.category',  $category);
            })
            // sort latest
            ->orderBy('products.created_at', 'desc');

        // Get the total count of filtered products
        $total = $baseQuery->count();

        // Apply pagination
        $result = $baseQuery->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Adding array images on each data
        $result->each(function ($q) {
            $images = DB::table('product_images')
                ->select('product_images.path')
                ->where('product_images.product_id', $q->id)
                ->get();
            $imageArray = [];
            $images->each(function ($image) use (&$imageArray) {
                $image_type = substr($image->path, -3);
                $image_format = '';
                if ($image_type == 'png' || $image_type == 'jpg') {
                    $image_format = $image_type;
                }
                $base64str = base64_encode(file_get_contents(public_path($image->path)));
                $imageArray[] = [
                    'base64img' => 'data:image/' . $image_format . ';base64,' . $base64str
                ];
            });
            $q->images = $imageArray;
        });

        // Return the result with total count
        return response()->json([
            'total' => $total,
            'data' => $result
        ]);
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
