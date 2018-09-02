<?php

class FeatureTest extends TestCase
{
    use Helper;

    /** @test */
    public function should_be_able_to_create_client() {
        // create, as guest
        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/clients', $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);

        // create, as a non-admin
        $admin = $this->createNonAdmin();
        $this->actingAs($admin);
        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/clients', $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);
    }

    /** @test */
    public function client_should_be_able_to_view_and_update_its_info() {
        $client = $this->createClient();
        $this->actingAs($client);

        // update
        $data = factory(App\User::class)->raw();
        $this->json('PUT', 'api/clients/'.$client->id, $data, $this->headers());
        $this->assertResponseStatus(200);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);

        // view
        $this->json('GET', 'api/clients/'.$client->id, $data, $this->headers());
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function client_should_not_be_able_to_update_other_clients_info() {
        // update
        $client = $this->createClient();
        $this->actingAs($client);

        $clientToUpdate = $this->createClient();
        $data = factory(App\User::class)->raw();
        $this->json('PUT', 'api/clients/'.$clientToUpdate->id, $data, $this->headers());
        $this->assertResponseStatus(401);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $clientToUpdate->username,
        ]);
    }

    /** @test */
    public function admin_should_be_able_to_perform_CRUD_to_resource_client() {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        // create
        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/clients', $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);

        // update
        $existing = $this->createClient();
        $data = factory(App\User::class)->raw();
        $this->json('PUT', 'api/clients/'.$existing->id, $data, $this->headers());
        $this->assertResponseStatus(200);
        $existing->refresh();
        $this->assertEquals($existing->username, $data['username']);

        // delete
        $this->json('DELETE', 'api/clients/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);
        $existing->refresh();
        $this->assertTrue($existing->trashed());

        // view
        $existing = $this->createClient();
        $this->json('GET', 'api/clients/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);

        // list(paginated)
        $this->json('GET', 'api/clients/', [], $this->headers());
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([ 'data', 'links', 'meta' ]);
        $this->seeJsonContains([
            'links' => [
                'first' => $this->baseUrl.'/api/clients?page=1',
                'last' => $this->baseUrl.'/api/clients?page=2',
                'prev' => null,
                'next' => $this->baseUrl.'/api/clients?page=2',
            ]
        ]);
    }

    /** @test */
    public function only_authorize_client_should_be_able_to_create_user() {
        // create, as authorized
        $authorizedClient = $this->createClient(true);
        $this->actingAs($authorizedClient);

        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/users', $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);

        // create, as not-authorized
        $client = $this->createClient();
        $this->actingAs($client);

        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/users', $data, $this->headers());
        $this->assertResponseStatus(401);
    }

    /** @test */
    public function non_admin_should_no_be_able_to_create_user() {
        // non admin
        $user = $this->createNonAdmin();
        $this->actingAs($user);

        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/users', $data, $this->headers());
        $this->assertResponseStatus(401);
    }

    /** @test */
    public function a_user_can_view_and_update_his_info() {
        $user = $this->createNonAdmin();
        $this->actingAs($user);

        // update
        $data = factory(App\User::class)->raw();
        $this->json('PUT', 'api/users/'.$user->id, $data, $this->headers());
        $this->assertResponseStatus(200);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);

        // view
        $this->json('GET', 'api/users/'.$user->id, $data, $this->headers());
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_should_not_be_able_to_update_other_users_info() {
        // update
        $user = $this->createNonAdmin();
        $this->actingAs($user);

        $userToUpdate = $this->createNonAdmin();
        $data = factory(App\User::class)->raw();
        $this->json('PUT', 'api/users/'.$userToUpdate->id, $data, $this->headers());
        $this->assertResponseStatus(401);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $userToUpdate->username,
        ]);
    }

    /** @test */
    public function admin_should_be_able_to_perform_CRUD_to_resource_user() {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        // create
        $data = factory(App\User::class)->raw();
        $this->json('POST', 'api/users', $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\User)->getTable(), [
            'username' => $data['username'],
        ]);

        // update
        $existing = $this->createNonAdmin();
        $data = factory(App\User::class)->raw();
        $this->json('PUT', 'api/users/'.$existing->id, $data, $this->headers());
        $this->assertResponseStatus(200);
        $existing->refresh();
        $this->assertEquals($existing->username, $data['username']);

        // delete
        $this->json('DELETE', 'api/users/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);
        $existing->refresh();
        $this->assertTrue($existing->trashed());

        // view
        $existing = $this->createNonAdmin();
        $this->json('GET', 'api/users/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);

        // list(paginated)
        $this->json('GET', 'api/users/', [], $this->headers());
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([ 'data', 'links', 'meta' ]);
        $this->seeJsonContains([
            'links' => [
                'first' => $this->baseUrl.'/api/users?page=1',
                'last' => $this->baseUrl.'/api/users?page=2',
                'prev' => null,
                'next' => $this->baseUrl.'/api/users?page=2',
            ]
        ]);
    }

    /** @test */
    public function a_user_can_add_phone_numbers() {
        $user = $this->createNonAdmin();
        $this->actingAs($user);

        // create
        $data = factory(App\Phone::class)->raw();
        $this->json('POST', 'api/phones/'.$user->id, $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\Phone())->getTable(), [
            'value' => $data['value'],
        ]);
    }

    /** @test */
    public function user_should_not_be_able_to_create_phone_numbers_with_invalid_format() {
        $owner = $this->createNonAdmin();
        $this->actingAs($owner);

        // create
        $data = factory(App\Phone::class)->raw();
        $data['value'] = '12345678';
        $this->json('POST', 'api/phones/'.$owner->id, $data, $this->headers());
        $this->assertResponseStatus(422);

        // update
        $existing = $this->createPhone($owner);
        $data = factory(App\Phone::class)->raw();
        $data['value'] = '12 34 56xx';
        $this->json('PUT', 'api/phones/'.$owner->id.'/'.$existing->id, $data, $this->headers());
        $this->assertResponseStatus(422);
    }

    /** @test */
    public function a_user_can_update_and_delete_his_phone_numbers() {
        $user = $this->createNonAdmin();
        $this->actingAs($user);

        /** @var App\Phone $phone */
        $phone = $this->createPhone($user);

        // update
        $data = factory(App\Phone::class)->raw();
        $this->json('PUT', 'api/phones/'.$user->id.'/'.$phone->id, $data, $this->headers());
        $this->assertResponseStatus(200);
        $phone->refresh();
        $this->assertEquals($phone->value, $data['value']);

        // delete
        $this->json('DELETE', 'api/phones/'.$user->id.'/'.$phone->id, [], $this->headers());
        $this->assertResponseStatus(200);
        $phone->refresh();
        $this->assertTrue($phone->trashed());
    }

    /** @test */
    public function user_should_not_be_able_to_create_duplicate_phone_numbers() {
        $owner = $this->createNonAdmin();
        $this->actingAs($owner);

        /** @var App\Phone $phone */
        $phone = $this->createPhone($owner);

        // adding it again
        $this->json('POST', 'api/phones/'.$owner->id, $phone->toArray(), $this->headers());
        $this->assertResponseStatus(422);

        // adding it for another user should work
        $anotherUser = $this->createNonAdmin();
        $this->actingAs($anotherUser);
        $this->json('POST', 'api/phones/'.$anotherUser->id, $phone->toArray(), $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\Phone())->getTable(), [
            'value' => $phone->value,
            'user_id' => $phone->user_id,
        ]);
    }

    /** @test */
    public function admin_should_be_able_to_perform_CRUD_to_resource_phone() {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $owner = $this->createNonAdmin();

        // create
        $data = factory(App\Phone::class)->raw();
        $this->json('POST', 'api/phones/'.$owner->id, $data, $this->headers());
        $this->assertResponseStatus(201);
        $this->seeInDatabase(with(new App\Phone)->getTable(), [
            'value' => $data['value'],
            'user_id' => $owner->id,
        ]);

        // update
        $existing = $this->createPhone($owner);
        $data = factory(App\Phone::class)->raw();
        $this->json('PUT', 'api/phones/'.$owner->id.'/'.$existing->id, $data, $this->headers());
        $this->assertResponseStatus(200);
        $existing->refresh();
        $this->assertEquals($existing->value, $data['value']);

        // delete
        $this->json('DELETE', 'api/phones/'.$owner->id.'/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);
        $existing->refresh();
        $this->assertTrue($existing->trashed());

        // view
        $existing = $this->createPhone($owner);
        $this->json('GET', 'api/phones/'.$owner->id.'/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);

        // list(paginated)
        $this->json('GET', 'api/phones/'.$owner->id, [], $this->headers());
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([ 'data', 'links', 'meta' ]);
        $this->seeJsonContains([
            'links' => [
                'first' => $this->baseUrl.'/api/phones/'.$owner->id.'?page=1',
                'last' => $this->baseUrl.'/api/phones/'.$owner->id.'?page=1',
                'prev' => null,
                'next' => null,
            ]
        ]);
    }

    /** @test */
    public function a_user_can_view_and_list_his_phone_numbers() {
        $owner = $this->createNonAdmin();
        $this->actingAs($owner);

        // view
        $existing = $this->createPhone($owner);
        $this->json('GET', 'api/phones/'.$owner->id.'/'.$existing->id, [], $this->headers());
        $this->assertResponseStatus(200);

        // list(paginated)
        $this->json('GET', 'api/phones/'.$owner->id, [], $this->headers());
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([ 'data', 'links', 'meta' ]);
        $this->seeJsonContains([
            'links' => [
                'first' => $this->baseUrl.'/api/phones/'.$owner->id.'?page=1',
                'last' => $this->baseUrl.'/api/phones/'.$owner->id.'?page=1',
                'prev' => null,
                'next' => null,
            ]
        ]);
    }

    /** @test */
    public function admin_should_be_able_to_change_authorization_of_client()
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        // authorize
        $clientToUpdate = $this->createClient();
        $this->assertFalse($clientToUpdate->isAuthorized());
        $this->json('PUT', 'api/clients/'.$clientToUpdate->id.'/authorize', [], $this->headers());
        $this->assertResponseStatus(200);
        $clientToUpdate->refresh();
        $this->assertTrue($clientToUpdate->isAuthorized());

        // de-authorize
        $this->json('PUT', 'api/clients/'.$clientToUpdate->id.'/de-authorize', [], $this->headers());
        $this->assertResponseStatus(200);
        $clientToUpdate->refresh();
        $this->assertFalse($clientToUpdate->isAuthorized());
    }

    /** @test */
    public function only_admin_can_change_authorization_of_a_client()
    {
        $nonAdmin = $this->createNonAdmin();
        $this->actingAs($nonAdmin);

        // authorize
        $clientToUpdate = $this->createClient();
        $this->assertFalse($clientToUpdate->isAuthorized());
        $this->json('PUT', 'api/clients/'.$clientToUpdate->id.'/authorize', [], $this->headers());
        $this->assertResponseStatus(401);
        $clientToUpdate->refresh();
        $this->assertFalse($clientToUpdate->isAuthorized());
    }
}
