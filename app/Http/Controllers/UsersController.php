<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use App\Users;
use App\Http\Resources\Users as UserResource;

class UsersController extends Controller
{

    public function index()
    {
        //Get Userdata
        $users = Users::paginate(15);

        return UserResource::collection($users);
    }

    public function checkEmail($email)
    {
        $user = Users::where('email', $email)->get();

        return count($user);
    }

    public function checkUser(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = Users::where('email', $email)->get();
        $body = [];
        if (sizeof($user) > 0) {
            if (password_verify($password, $user[0]['password'])) {
                $response = 'success';
                $body = [ 'id' => $user[0]['id'] ];

            } else {
                $response = 'wrong_credential';
            }
        } else {
            $response = 'not_exist';
        }

        return response()->json([ 'response' => $response, 'body' => $body ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function add(Request $request)
    {
        //first check email
        $email_exits = static::checkEmail($request->input('email'));
        $body = [];
        if ($email_exits == 0) {

            $user = new Users;

            $user->nickname = (string) $request->input('nickname');
            $user->email = (string) $request->input('email');
            $user->password = (string) password_hash($request->input('password'), PASSWORD_DEFAULT);
            $user->location = " ";
            $user->user_unique_code = " ";
            $user->timestamp = time();
            $user->msc = 0;
            $user->s = 0;
            $user->p = 0;
            $user->e = 0;

            if ($user->save()) {
                $response = 'success';
                $id = $user->id;
                $body = [ 'id' => $id ];
            } else {
                $response = 'database_error';
            }
        } else {
            $response = 'email_exits';
        }

        return response()->json([ 'response' => $response, 'body' => $body ]);
    }

    public function setData(Request $request)
    {
        $user = Users::findOrFail($request->id) ;

        $user->id = $request->input('id');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        // $user->created_at=time();

        if ($user->save()) {
            return new UserResource($user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Users::findOrFail($id);

        return new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = Users::findOrFail($id);
        if ($user->delete()) {
            return new UserResource($user);
        }
    }

}
