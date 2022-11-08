<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validation('create', $request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->createdResponse(new UserResource($user));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! $user = User::find($id)) {
            return $this->notFound();
        }
        return response()->json(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! $user = User::find($id)) {
            return $this->notFound();
        }

        $validator = $this->validation('update', $request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);

        $user->update($data);

        return $this->successResponse(new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $user = User::find($id)) {
            return $this->notFound();
        }

        $user->delete();

        return $this->noContentResponse();
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

            case 'create':

                $validator = [
                    'name' => ['required', 'string'],
                    'password' => ['required', 'min:6', 'max:50'],
                    'email' => ['required', 'email', 'unique:users,email'],
                    'role' => [
                        'required',
                        Rule::in(User::ROLES)
                    ],
                    'phone' => ['nullable', 'min:10'],
                    'address' => ['nullable', 'string'],
                ];

                break;

            case 'update':

                $validator = [
                    'name' => ['required', 'string'],
                    'password' => ['required', 'min:6', 'max:50'],
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($request->get('id'))
                    ],
                    'role' => [
                        'required',
                        Rule::in(User::ROLES)
                    ],
                    'phone' => ['nullable', 'min:10'],
                    'address' => ['nullable', 'string'],
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
