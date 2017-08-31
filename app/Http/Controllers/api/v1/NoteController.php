<?php

namespace App\Http\Controllers\api\v1;

use App\Note;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $note_data = [];

        $user = JWTAuth::parseToken()->authenticate();

        $notes = Note::all();


        foreach ($notes as $note){

            $entry = [
                'id' => $note->slug,
                'title' => $note->title,
                'body' => $note->body,
                'imageUrl' => $note->imageUrl,
                'reminderDate' => $note->reminderDate
            ];


            $note_data[] = $entry;
        }


        $data['notes'] = $note_data;
        $data['user'] = $user;

        return response()->json($data, 200);


    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(
                [
                    'error' => 'User not found',
                    "meta" => [
                        "status" =>  "USER_NOT_FOUND"
                    ]
                ], 404);
        }


        $reminderDate = (new Carbon($request->get('reminderDate')))->toDateTimeString();



        $validator = Validator::make($request->all(), [
           'title' => 'required|string|max:255',
           'body' => 'required|string',
           'reminderDate' => 'required|date',
            'imageUrl' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        

        if ($validator->fails()) {

            $errors['errors'] = $validator->errors()->all();
            return response()->json($errors, 400) ;
        }

        $pathToImage = null;

        if(($request->file('imageUrl')) !== null){
            $uploadedFileImage = $request->file('imageUrl');
            $clientOriginalName = $uploadedFileImage->getClientOriginalName();
            $pathToImage = Storage::putFileAs(
                'public/notes-images',
                $uploadedFileImage,
                str_replace(".", "", $clientOriginalName)."_".str_random(10).".".$uploadedFileImage->getClientOriginalExtension()
            );

        }

        $note = new Note();




        $note->title = $request->get('title');
//        $note->title = "Dummy Title";
        $note->body = $request->get('body');
//        $note->body = "Dummy body";
        $note->imageUrl = $pathToImage;

        $note->reminderDate = $reminderDate;
//        $note->reminderDate = "2014-02-05 10:8:2";
        $note->user_id = $user->id;
        $note->slug = str_slug(substr($request->get('title'),0,10)."-".$reminderDate."-".rand(10000,900000));


        $note->save();

        return response()->json([
            'message' => 'Note created successfully',
            'note' => [
                'title' => $note->title,
                'slug' => $note->slug

            ],
            "meta" => [
                "status" =>  "NOTE_CREATED"
            ]
        ], 201);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
