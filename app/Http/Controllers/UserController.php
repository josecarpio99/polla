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
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = User::query()
            ->whereNot('id', auth()->user()->id)
            ->search($search)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return UserResource::collection($query);
    }

    public function list()
    {
        $role = auth()->user()->role;

        $users = User::query()
            ->select('id', 'username')
            ->when($role !== 'superadmin', function($query) use($role) {
                if ($role === 'admin') {
                    $ids = auth()->user()->pos->pluck('id');
                    $ids[] = auth()->user()->id;
                    $query->whereIn('id', $ids);
                } elseif($role === 'pos') {
                    $query->where('id', auth()->user()->id);
                }
            })
            ->get();

        return response()->json($users);
    }

    public function pos()
    {
        return UserResource::collection(
            User::where('role', 'pos')->get()
        );
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
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

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
                    'username' => ['required', 'min:6', 'max:30','alpha_num', 'unique:users,username'],
                    'email' => ['nullable', 'email', 'unique:users,email'],
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
                    'password' => ['nullable', 'min:6', 'max:50'],
                    'username' => [
                        'required',
                        'alpha_num',
                        'min:6',
                        'max:30',
                        Rule::unique('users', 'username')->ignore($request->get('id'))
                    ],
                    'email' => [
                        'nullable',
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
