<?php

namespace App\Http\Controllers;

use App\Phone;
use App\User;
use App\Exceptions\ApiException;
use App\Http\Resource\Collection;
use App\Http\Resource\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * @property Phone phone
 * @property User user
 */
class PhoneController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Phone $phone
     * @param User $user
     */
    public function __construct(Phone $phone, User $user)
    {
        $this->middleware('auth');
        $this->phone = $phone;
        $this->user = $user;
    }

    /**
     * @param $ownerId
     * @return Collection
     */
    public function index($ownerId) {
        /** @var User $owner */
        $owner = $this->user->findOrFail($ownerId);
        return new Collection($owner->phones()->paginate());
    }

    /**
     * @param $ownerId
     * @param $id
     * @return Item
     * @throws ApiException
     */
    public function show($ownerId, $id) {
        /** @var Phone $instance */
        $instance = $this->phone->findOrFail($id);
        /** @var User $owner */
        $owner = $this->user->findOrFail($ownerId);

        if (!Gate::allows('view-phone', [$owner, $instance])) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        return new Item($instance);
    }

    /**
     * @param Request $request
     * @param $ownerId
     * @return Item
     * @throws ApiException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, $ownerId) {
        /** @var User $owner */
        $owner = $this->user->findOrFail($ownerId);
        if (!Gate::allows('create-phone', $owner)) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $this->validate($request, [
            // no duplicate phone for every user
            'value' => 'required|regex:/\+63\s?\d{3}\s?\d{3}\s?\d{4}/|unique:phones,value,id,null,user_id,'.$owner->id,
        ]);

        $data = $request->all();

        /** @var Phone $instance */
        $instance = $this->phone->newInstance($data);
        $owner->phones()->save($instance);

        return new Item($instance);
    }

    /**
     * @param Request $request
     * @param $ownerId
     * @param $id
     * @return Item
     * @throws ApiException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $ownerId, $id) {
        /** @var User $owner */
        $owner = $this->user->findOrFail($ownerId);
        /** @var Phone $instance */
        $instance = $this->phone->findOrFail($id);

        if (!Gate::allows('update-phone', [$owner, $instance])) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $this->validate($request, [
            'value' => 'required|regex:/\+63\s?\d{3}\s?\d{3}\s?\d{4}/|unique:phones,value,id,'.$instance->id.',user_id,'.$owner->id,
        ]);

        $data = $request->all();

        $instance->update($data);

        return new Item($instance);
    }

    /**
     * @param $ownerId
     * @param $id
     * @return Item
     * @throws ApiException
     * @throws \Exception
     */
    public function destroy($ownerId, $id) {
        /** @var User $owner */
        $owner = $this->user->findOrFail($ownerId);
        /** @var Phone $instance */
        $instance = $this->phone->findOrFail($id);

        if (!Gate::allows('delete-phone', [$owner, $instance])) {
            throw new ApiException('Not allowed to perform the action.', 401);
        }

        $instance->delete();

        return new Item($instance);
    }
}
