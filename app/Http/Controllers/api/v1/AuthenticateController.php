<?php

namespace App\Http\Controllers\api\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');



        try {
            // attempt to verify the credentials and create a token for the user

            if (!$token = JWTAuth::attempt($credentials)) {

                return response()->json(['error' => 'invalid_credentials'], 401);
            }



        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(
            [
                'message' => 'token_generated',
                'data' => [
                    'token' => $token,
                ]
            ]
        );
    }

    public function register(Request $request){


        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users|max:255',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            $errors['errors'] = $validator->errors()->all();

            return response()->json($errors) ;
        }

       $user  = new User();
       $user->name = $request->get('name');
       $user->email = $request->get('email');
       $user->password = bcrypt($request->get('password')) ;

       $user->save();



        return $this->authenticate($request);
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshToken()
    {
        $token = JWTAuth::parseToken();

        $newToken = $token->refresh();

        return response()->json([
            'message' => 'token_refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }


}
