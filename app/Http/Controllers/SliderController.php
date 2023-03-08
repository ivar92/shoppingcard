<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;

class SliderController extends Controller
{
    public function addslider(){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        return view('admin.addslider');
    }

    public function saveslider(Request $request){

        $this->validate($request, [
                                   'description1' => 'required',
                                   'description2' => 'required',
                                   'slider_image' => 'image|required|max:1999']);

     // 1 : get file name with extern
           $fileNameWithExt = $request->file('slider_image')->getClientOriginalName();
          // 2 : get just file name
          $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
           // 3 : get juste file extension
          $extension = $request->file('slider_image')->getClientOriginalExtension();
          //  4 : file name store
          $fileNameStore = $fileName.'_'.time().'.'.$extension;

          // upload image
          $path = $request->file('slider_image')->storeAs('public/slider_images', $fileNameStore);

          $slider = new Slider();
          $slider->description1 = $request->input('description1');
          $slider->description2 = $request->input('description2');
          $slider->slider_image = $fileNameStore;
          $slider->status = 1;

          $slider->save();

          return back()->with('status', 'Le slider a été enregistré avec succès !!!');

    }

    public function sliders(){
        if (!Session::has('admistratrors')) {
            return view('admin.connecter');
        }
        $sliders = Slider::all();

        return view('admin.sliders')->with('sliders', $sliders);
    }

    public function edit_slider($id){
        $slider = Slider::find($id);

        return view('admin.editSlider')->with('sliders', $slider);
    }

    public function updateslider(Request $request){

        $this->validate($request, [
                                            'description1' => 'required',
                                            'description2' => 'required',]);

        $slider = Slider::find($request->input('id'));
        $slider->description1 = $request->input('description1');
        $slider->description2 = $request->description2;

        if ($request->hasFile('slider_image')) {
             // 1 : get file name with extern
             $fileNameWithExt = $request->file('slider_image')->getClientOriginalName();
             // 2 : get just file name
             $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
             // 3 : get juste file extension
             $extension = $request->file('slider_image')->getClientOriginalExtension();
             //  4 : file name store
             $fileNameStore = $fileName.'_'.time().'.'.$extension;

             // upload image
             $path = $request->file('slider_image')->storeAs('public/slider_images', $fileNameStore);

            //  pour la suppression de l'image ancienne

            Storage::delete('public/slider-images/'.$slider->slider_image);

             $slider->slider_image = $fileNameStore;

             $slider->update();
        }

        $slider->update();
        return redirect('/sliders')->with('status', 'Le slider a été mis à jour avec succès !!!');

    }

    public function delete_slider($id){
        $slider = Slider::find($id);

        Storage::delete('public/slider_images/'.$slider->slider_image);


        $slider->delete();

        return back()->with('status', 'Le slider a été supprimé avec succès !!!');
    }

    public function activer_slider($id){
        $slider = Slider::find($id);

        $slider->status = 1;

        $slider->update();

        return back();
    }

    public function desactiver_slider($id){
        $slider = Slider::find($id);

        $slider->status = 0;

        $slider->update();

        return back();
    }
}
