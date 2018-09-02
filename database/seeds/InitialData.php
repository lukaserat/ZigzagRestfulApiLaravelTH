<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Webpatser\Uuid\Uuid;

class InitialData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        // create roles
        $this->seedRoles();

        // seed the superuser
        $this->seedRootUser();

        if (env('APP_ENV') !== 'production') {
            $this->seedFakeData();
        }
    }

    /**
     * @throws Exception
     */
    private function seedFakeData()
    {
        // seed 2 authorized clients
        factory(App\User::class, 2)->create([
            'is_client' => true,
            'is_authorized' => true,
        ]);

        // seed 1 unauthorized clients
        factory(App\User::class)->create([
            'is_client' => true,
            'is_authorized' => false,
        ]);

        // seed 5 non admin users
        factory(App\User::class, 5)->create();

        // seed 5 admin users
        factory(App\User::class, 5)->create()->each(function (App\User $u) {
            $u->assignRole(App\Role::fromName('admin')->uid);
        });

        // seed 5 phones for every user
        App\User::chunk(10, function($users) {
            foreach ($users as $user) {
                $user->phones()->saveMany(factory(App\Phone::class, 5)->make());
            }
        });
    }

    private function seedRoles() {
        App\Role::create([
            'name' => 'admin',
            'uid' => (string) UUID::generate(4),
        ]);
    }

    private function seedRootUser()
    {
        /** @var App\User $root */
        $root = factory(App\User::class)->create([
            'username' => 'root',
            'password' => Hash::make('root'),
        ]);
        $root->assignRole(App\Role::fromName('admin')->uid);
    }
}
