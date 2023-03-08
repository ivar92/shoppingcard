<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Session;

class CategoryController extends Controller
{
    public function addcategory(){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        return view('admin.addcategory');
    }

    public function categories(){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        $categories = Category::all();

        return view('admin.categories')->with('categories', $categories);
    }

    public function savecategory(Request $request){
        $this->validate($request, ['category_name' => 'required| unique:categories']);

        $category = new Category();
        $category->category_name = $request->input('category_name');

        $category->save();

        return back()->with('status', 'La catégorie a été enregistrée avec succès !!!');
    }

    public function edit_category($id){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        $category = Category::find($id);

        return view('admin.editcategory')->with('category', $category);
    }

    public function updatecategory(Request $request){

        $this->validate($request, ['category_name' => 'required']);

        $category = Category::find($request->input('id'));

        $category->category_name = $request->input('category_name');
        $category->update();

        return redirect('/categories')->with('status', 'La catégorie a été modifiée avec succès !!!');
    }

    public function delete_category($id){
        $category = Category::find($id);

        $category->delete();

        return back()->with('status', 'La catégorie a été supprimé avec succès !!!');
    }
}
