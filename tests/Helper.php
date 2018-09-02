<?php

use Illuminate\Support\Facades\Hash;

trait Helper
{
    /**
     *
     */
    public function isAValidApiResponse() {
        $this->shouldReturnJson();
    }

    /**
     * @return array
     */
    public function headers() {
        return [
            'API-VERSION' => 'v1',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return mixed
     */
    public function createNonAdmin() {
        return factory(App\User::class)->create([
            'password' => 'password',
        ]);
    }

    /**
     * @return \App\User
     */
    public function createAdmin() {
        /** @var App\User $user */
        $user = factory(App\User::class)->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole(App\Role::fromName('admin')->uid);
        return $user;
    }

    /**
     * @param \App\User $user
     * @param $data
     * @return \App\Phone
     */
    public function createPhone(App\User $user)
    {
        $phone = new App\Phone(factory(App\Phone::class)->raw());
        $user->phones()->save($phone);

        return $phone;
    }

    /**
     * @param bool $isAuthorized
     * @return \App\User
     */
    public function createClient(Bool $isAuthorized = false) {
        $client = App\User::registerClient(factory(App\User::class)->raw());

        if ($isAuthorized) {
            $client->is_authorized = true;
            $client->update();
        }

        return $client;
    }
}