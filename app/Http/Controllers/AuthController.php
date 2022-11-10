<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth:api', [

            'except' => [
                'register',
                'verify',
                'login',
            ],

        ]);
    }

    /**
     * register user
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function register(Request $request) {

        /* validation requirement */
        $validator = $this->validation('registration', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false , 400  );
        }

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        return $this->core->setResponse('success', 'Account created successfully!', $user );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login()
    {
        /* validation requirement */
        $validator = $this->validation('login', request());

        if ($validator->fails()) {

            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {

            return $this->errorResponse('Usuario o contraseña incorrecto!', 400);
        }

        return response()->json([
            'success' => 'Successfully login',
            'data'    => new UserResource(auth()->user()),
            'token'   => $token
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->core->setResponse('success', 'Successfully logged out' );
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh(), 'refresh token');
    }

    /**
     * user profile
     *
     * @return JsonResponse
     */
    public function profile() {

        return new UserResource(auth()->user());
    }


    /**
     * validation requirement
     *
     * @param  string $type
     * @param  request $request
     * @return object
     */
    private function validation($type = null, $request) {

        switch ($type) {

            case 'registration':

                $validator = [
                    'firstname' => 'required|max:50|min:2',
                    'lastname' => 'required|max:100|min:2',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6|max:100',
                ];

                break;

            case 'login':

                $validator = [
                    'email' => 'required|string',
                    'password' => 'required|string',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $action = null)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * config('auth.jwt.expires_in', 60),
        ];

        return $this->core->setResponse('success', "Successfully $action", $data );
    }

}
