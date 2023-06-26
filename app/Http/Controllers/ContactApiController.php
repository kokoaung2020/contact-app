<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Gallery;
use App\Models\RecordSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Storage;

class ContactApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contact = Contact::when(request('keywork'),function($q){
            $keywork = request('keywork');

            RecordSearch::create([
                "record" => $keywork,
                "user_id" => Auth::id()
            ]);

            $q->orWhere("name","like","%".$keywork."%")
              ->orWhere("phone","like","%".$keywork."%")
              ->orWhere("email","like","%".$keywork."%")
              ->orWhere("address","like","%".$keywork."%");

        })->latest('id')
        ->when(request()->trash,fn($q)=>$q->onlyTrashed())
        ->paginate(5)
        ->withQueryString()->onEachSide(1);

        if(RecordSearch::all() !== null){
            $record = RecordSearch::all();
            $count = $record->count();
        }

        return response()->json([
            "contact" => ContactResource::collection($contact),
            "record" => $record,
            "count" => $count
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|max:50",
            "phone" => "required|numeric|min:5",
            "email" => "nullable|email|min:7",
            "address" => "nullable|min:5",
            "photos" => "nullable",
            "photos.*" => "file|mimes:png,jpg,jpeg|max:1024"
        ]);



        // $contact = new Contact();
        // $contact->name = $request->name;
        // $contact->phone = $request->phone;
        // $contact->email = $request->email;
        // $contact->address = $request->address;
        // $contact->user_id = 1;
        // $contact->save();

        $contact = Contact::create([
            "name" => $request->name,
            "phone" => $request->phone,
            "email" => $request->email,
            "address" => $request->address,
            "user_id" => Auth::id()
        ]);

        $photos = [];
        foreach($request->file('photos') as $key=>$photo){
            $newName = uniqid()."_contact.".$photo->extension();

            $img = Image::make($photo);

            //large
            $img->resize(1000,null,fn($constraint)=>$constraint->aspectRatio());
            Storage::makeDirectory("public/large");
            $img->save("storage/large/$newName");

            //small
            $img->resize(500,null,fn($constraint)=>$constraint->aspectRatio());
            Storage::makeDirectory("public/small");
            $img->save("storage/small/$newName");

            $photos[$key] = new Gallery([
                'name' => $newName,
                'user_id' => Auth::id()
            ]);
        }

        $contact->galleries()->saveMany($photos);

        return response()->json([
            "message" => "Product Created",
            "success" => true,
            "product" => new ContactResource($contact)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contact = Contact::find($id);

        if(is_null($contact)){
            return response()->json(["message"=>"Page not found"],404);
        }

        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "name"=>"nullable|max:50",
            "phone"=>"nullable|numeric|min:5",
            "email"=>"nullable|email|min:7",
            "address"=>"nullable|min:5"
        ]);

        $contact = Contact::find($id);

        if(is_null($contact)){
            return response()->json(["message"=>"Page not found"],404);
        }

        if($request->has("name")){
            $contact->name = $request->name;
        }

        if($request->has("phone")){
            $contact->phone = $request->phone;
        }

        if($request->has("email")){
            $contact->email = $request->email;
        }

        if($request->has("address")){
            $contact->address = $request->address;
        }

        $contact->update();

        return response()->json([
            "message"=>"Contact Updated",
            "success"=>true,
            "contact"=> new ContactResource($contact)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = Contact::withTrashed()->find($id);

        if(is_null($contact)){
            return response()->json(["message"=>"Page not found"],404);
        }

        if(request("delete") === "force"){

            $contact->forceDelete();

            $message = "Deleted Successfully";
        }
        elseif(request("delete") === "restore"){

            $contact->restore();

            $message = "Restore Successfully";
        }
        else{

            $contact->delete();

            $message = "Move to Trash Successfully";
        }



        return response()->json(["message"=>$message]);

    }
}
