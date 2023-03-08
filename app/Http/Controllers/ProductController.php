<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use Session;
use App\Cart;


class ProductController extends Controller
{
    public function addproduct(){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        $categories = Category::All()->pluck('category_name', 'category_name');

        return view('admin.addproduct')->with('categories', $categories);
    }

    public function saveproduct(Request $request){
        $this->validate($request, ['product_name' => 'required',
                                   'product_price' => 'required',
                                   'product_category' => 'required',
                                   'product_image' => 'image|nullable|max:1999']);

        if($request->hasFile('product_image')) {
            // 1 : get file name with extern
            $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
            // 2 : get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // 3 : get juste file extension
            $extension = $request->file('product_image')->getClientOriginalExtension();
            //  4 : file name store
            $fileNameStore = $fileName.'_'.time().'.'.$extension;



            // upload image
            $path = $request->file('product_image')->storeAs('public/product_image', $fileNameStore);
            $path = $request->file('product_image')->storeAs('public/product_ilage', $fileNameStore);
        } else {
            $fileNameStore = 'noimage.jpg';
        }

        $product = new Product();
        $product->product_name = $request->input('product_name');
        $product->product_price = $request->input('product_price');
        $product->product_category = $request->input('product_category');
        $product->product_image = $fileNameStore;
        $product->status = 1;

        $product->save();

        return back()->with('status', 'Le produit a été enregistré avec succès !!!');

    }

    public function edit_product($id){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        $product = Product::find($id);

        $categories = Category::All()->pluck('category_name', 'category_name');

        return view('admin.editproduct')->with('product', $product)->with('categories', $categories);

    }

    public function product(){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        $products = Product::all();

        return view('admin.product')->with('products', $products);
    }

    public function updateproduct(Request $request){
        $this->validate($request, ['product_name' => 'required',
                                   'product_price' => 'required',
                                   'product_category' => 'required',
                                   'product_image' => 'image|nullable|max:1999']);
        $product = Product::find($request->input('id'));
        $product->product_name = $request->input('product_name');
        $product->product_price = $request->product_price;
        $product->product_category = $request->input('product_category');

        if ($request->hasFile('product_image')) {
             // 1 : get file name with extern
             $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
             // 2 : get just file name
             $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
             // 3 : get juste file extension
             $extension = $request->file('product_image')->getClientOriginalExtension();
             //  4 : file name store
             $fileNameStore = $fileName.'_'.time().'.'.$extension;

             // upload image
             $path = $request->file('product_image')->storeAs('public/product_image', $fileNameStore);

            //  pour la suppression de l'image ancienne
            if ($product->product_image != 'noimage.jpg') {
                Storage::delete('public/product-image/'.$product->product_image);
            }

             $product->product_image = $fileNameStore;
        }

        $product->update();
        return redirect('/product')->with('status', 'Le produit a été mis à jour avec succès !!!');

    }
    public function delete_product($id){
        $product = Product::find($id);

        if ($product->product_image != 'noimage.jpg') {
            Storage::delete('public/product_image/'.$product->product_image);
        }

        $product->delete();

        return back()->with('status', 'Le produit a été supprimé avec succès !!!');
    }

    public function activer_product($id){
        $product = Product::find($id);

        $product->status = 1;

        $product->update();

        return back();
    }

    public function desactiver_product($id){
        $product = Product::find($id);

        $product->status = 0;

        $product->update();

        return back();
    }

    public function select_par_cat($category_name){

        $products = Product::all()->where('product_category', $category_name)->where('status', 1);

        $categories = Category::all();

        return view('client.shop')->with('products', $products)->with('categories', $categories);
    }



}
