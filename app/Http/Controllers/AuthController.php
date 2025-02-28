<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Socialite;


class AuthController extends Controller
{
    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
    * @return [string] user_type
    * @return [string] phone
    */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email'=>'required|string|unique:users',
                'phone'=>'required|string|unique:users',
                'password'=>'required|string',
                'user_type'=>'required|in:employer,candidate',
                'c_password' => 'required|same:password'
            ]);
            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $user = new User([
                'uuid'  => (string) Str::uuid(),
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_type' => $request->user_type,
                'password' => bcrypt($request->password),
            ]);

            if($user->save()){
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;

                return response()->json([
                    'message' => 'Successfully created user!',
                    'accessToken'=> $token,
                    'user'=> $user,
                ],201);
            }
            else{
                return response()->json(['error'=>'Provide proper details']);
            }
        } catch (\Exception $exception){
            Log::error($exception);
            return response()->json(['error'=> $exception]);
        }
    }

    /**
    * Login user and create token
    *
    * @param  [string] email
    * @param  [string] password
    * @param  [boolean] remember_me
    */

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
                'remember_me' => 'boolean'
            ]);
            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $credentials = request(['email','password']);
            if(!Auth::attempt($credentials))
            {
                return response()->json([
                    'message' => 'Unauthorized'
                ],401);
            }

            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'user' =>$user,
                'accessToken' =>$token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Error ! Please contact support', 500);
        }
    }

    /**
    * Get the authenticated User
    *
    * @return [json] user object
    */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
    *
    * @return [string] message
    */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
        'message' => 'Successfully logged out'
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginGoogle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required'
            ]);
            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $google = Socialite::driver('google')->userFromToken($request->accessToken);

            if ($google) {
                $user = User::where('email', $google->getEmail())->first();
                if ($user) {
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->plainTextToken;

                    return response()->json([
                        'user' =>$user,
                        'accessToken' =>$token,
                        'token_type' => 'Bearer',
                    ]);
                } else {
                    
                    $user = new User();
                    $user->uuid = (string) Str::uuid();
                    $user->name = $google->getName();
                    $user->email = $google->getEmail();
                    $user->password = bcrypt('Welcome@123');
                    $user->user_type = $request->user_type;
                    $user->save();

                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->plainTextToken;

                    return response()->json([
                        'user' =>$user,
                        'accessToken' =>$token,
                        'token_type' => 'Bearer',
                    ]);

                }
            }

            return $this->simpleReturn('error', "Token not valid");
        } catch (\Exception $e) {
            return $this->simpleReturn('error', $e->getMessage(), 500);
        }
    }

}