<?php

namespace App\Http\Controllers\api\v1;

use App\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

        $user = JWTAuth::parseToken()->authenticate();
        $data['notes']= DB::table('notes')
              ->where('id', $user->id)
            ->paginate(4);
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
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'reminder_date' => 'required|date',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {

            $errors['errors'] = $validator->errors()->all();
            return response()->json($errors, 200) ;
        }

        $pathToImage = null;

        if(($request->file('image')) !== null){
            $uploadedFileImage = $request->file('image');
            $clientOriginalName = $uploadedFileImage->getClientOriginalName();
            $pathToImage = Storage::putFileAs(
                'public/notes-images',
                $uploadedFileImage,
                str_replace(".", "", $clientOriginalName)."_".str_random(10).".".$uploadedFileImage->getClientOriginalExtension()
            );

        }

        $note = new Note();

        $note->title = $request->get('title');
        $note->body = $request->get('body');
        $note->image_url = $pathToImage;
        $note->reminder_date = $request->get('reminder_date');
        $note->user_id = $user->id;
        $note->slug = str_slug($request->get('title')." ".$request->get('reminder_date'));



        $note->save();

        return response()->json([
            'message' => 'Note created successfully'
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
