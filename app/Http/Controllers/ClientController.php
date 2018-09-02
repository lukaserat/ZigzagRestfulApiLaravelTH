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
 * @property User client
 */
class ClientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param User $client
     */
    public function __construct(User $client)
    {
        $this->middleware('auth', ['except' => 'store']);
        $this->client = $client;
    }

    /**
     * @return Collection
     */
    public function index() {
        return new Collection($this->client->paginate());
    }

    /**
     * @param $id
     * @return Item
     * @throws ApiException
     */
    public function show($id) {
        /** @var User $instance */
        $instance = $this->client->findOrFail($id);

        if (!Gate::allows('view-client', $instance)) {
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
        $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required',
        ]);

        $data = $request->all();

        // hash the password
        $data['password'] = Hash::make($request->password);

        $instance = $this->client->registerClient($data);

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
        $instance = $this->client->findOrFail($id);

        if (!Gate::allows('update-client', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $this->validate($request, [
            'username' => 'unique:users',
        ]);

        $data = $request->all();

        if ($request->has('password')) {
            // hash the password
            $data['password'] = Hash::make($request->password);
        }

        $instance->update($data);

        return new Item($instance);
    }

    /**
     * @param $id
     * @return Item
     * @throws ApiException
     * @throws \Exception
     */
    public function destroy($id) {
        /** @var User $instance */
        $instance = $this->client->findOrFail($id);

        if (!Gate::allows('delete-client', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $instance->delete();

        return new Item($instance);
    }

    /**
     * @param mixed $id
     * @return Item
     * @throws ApiException
     */
    public function authorizeClient($id)
    {
        /** @var User $instance */
        $instance = $this->client->findOrFail($id);

        if (!Gate::allows('authorize-client', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $instance->is_authorized = true;

        $instance->update();

        return new Item($instance);
    }

    /**
     * @param $id
     * @return Item
     * @throws ApiException
     */
    public function deAuthorizeClient($id)
    {
        /** @var User $instance */
        $instance = $this->client->findOrFail($id);

        if (!Gate::allows('deauthorize-client', $instance)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $instance->is_authorized = false;

        $instance->update();

        return new Item($instance);
    }
}
