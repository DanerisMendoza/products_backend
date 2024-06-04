<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function insertUser(Request $request)
    {
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->gender = $request->input('gender');
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password')); // Hash the password
        $user->save();
        return response()->json(['message' => 'User inserted successfully'], 201);
    }

    public function deleteUser(Request $request)
    {
        $id = $request->input('id');

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function GetUsers(Request $request)
    {
        $users = User::all();
        return $users;
    }

    public function Login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
    
        // Fetch the user by username or email
        $login = DB::table('users')
            ->where('username', $request->username)
            ->orWhere('email', $request->username)
            ->select('users.password', 'users.username', 'users.user_role')
            ->first();
    
        if (!$login) {
            return response()->json(
                [
                    'message' => 'Please check the username or password.',
                    'icon' => 'error'
                ]
            );
        }
    
        if (Hash::check($request->password, $login->password)) {
            $passwordGrantClient = Client::where('password_client', 1)->first();
    
            $response = [
                'grant_type' => 'password',
                'client_id' => $passwordGrantClient->id,
                'client_secret' => $passwordGrantClient->secret,
                'username' => $login->username, // This should be the username from the database
                'password' => $request->password,
                'scope' => '*',
            ];
    
            $tokenRequest = Request::create('/oauth/token', 'post', $response);
            $response = app()->handle($tokenRequest);
            $data = json_decode($response->getContent());
    
            if (isset($data->access_token)) {
                $token = $data->access_token;
                return response()->json(
                    [
                        'message' => 'Login Successfully!',
                        'icon' => 'success',
                        'token' => $token,
                        'user_role' => $login->user_role,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Failed to generate access token.',
                        'icon' => 'error'
                    ],
                    500
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'The password was incorrect',
                    'icon' => 'error'
                ],
            );
        }
    }
    

    public function GetUserDetails()
    {
        $userId = Auth::user()->id;
        $userDetail = DB::table('users')
            ->where('users.id', $userId)
            ->select(
                'users.id',
                'users.gender',
                'users.email',
                'users.address',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.user_role',
                DB::raw("CONCAT(users.first_name, ' ', users.middle_name, ' ', users.last_name) as name"),
                'users.profile_pic_path'
            )
            ->first();
        if ($userDetail->profile_pic_path != null) {
            $image_type = substr($userDetail->profile_pic_path, -3);
            $image_format = '';
            if ($image_type == 'png' || $image_type == 'jpg') {
                $image_format = $image_type;
            }
            $base64str = '';
            $base64str = base64_encode(file_get_contents(public_path($userDetail->profile_pic_path)));
            $userDetail->base64img = 'data:image/' . $image_format . ';base64,' . $base64str;
        }
        return $userDetail;
    }

    public function Logout(Request $request)
    {
        $user = $request->user();
        $user->token()->revoke();
        return ['message' => 'success'];
    }

    public function Register(Request $request)
    {
        $pass = Hash::make($request->password);
        // Create a new user
        $newVoter = new User();
        $newVoter->first_name = $request->firstname;
        $newVoter->middle_name = $request->midname;
        $newVoter->last_name = $request->lastname;
        $newVoter->user_role = 2;
        $newVoter->suffix = $request->suffix;
        $newVoter->email = $request->email;
        $newVoter->age = $request->age;
        $newVoter->address = $request->address;
        $newVoter->gender = $request->gender;
        $newVoter->username = $request->username;
        $newVoter->password = $pass;
        // Save the user to the database
        $newVoter->save();
        // return $newVoter;
        return response()->json(['message' => 'Successfully Registered'], 200);
    }

}
