<?php

namespace App\Http\Controllers;

use App\User;
use App\Exceptions\ApiException;
use App\Http\Resource\Collection;
use App\Http\Resource\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

/**
 * @property User user
 */
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->middleware('auth');
        $this->user = $user;
    }

    /**
     * @return Collection
     */
    public function index() {
        return new Collection($this->user->paginate());
    }

    /**
     * @param $id
     * @return Item
     * @throws ApiException
     */
    public function show($id) {
        /** @var User $instance */
        $instance = $this->user->findOrFail($id);

        if (!Gate::allows('view-user', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }
        return new Item($instance);
    }

    /**
     * @param Request $request
     * @return Item
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(Request $request) {
        if (!Gate::allows('create-user')) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        if ($request->has('is_admin') && !Gate::allows('create-admin-user')) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required',
        ]);

        $data = $request->all();

        /** @var User $instance */
        $instance = $this->user->newInstance($data);

        // hash the password
        $instance->password = Hash::make($request->password);
        $instance->save();

        if ($request->has('is_admin') && $request->is_admin) {
            $instance->assignRole(Role::fromName('admin')->uid);
        }

        return new Item($instance);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Item
     * @throws ApiException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id) {
        /** @var User $instance */
        $instance = $this->user->findOrFail($id);

        if (!Gate::allows('update-user', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $this->validate($request, [
            'username' => 'unique:users',
        ]);

        $data = $request->all();

        if ($request->has('password')) {
            // hash the password
            $instance->password = Hash::make($request->password);
        }

        $instance->update($data);

        return new Item($instance);
    }

    /**
     * @param $id
     * @return Item
     * @throws ApiException
     */
    public function destroy($id) {
        /** @var User $instance */
        $instance = $this->user->findOrFail($id);

        if (!Gate::allows('delete-user', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $instance->delete();

        return new Item($instance);
    }
}
