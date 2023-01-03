<?php

namespace Tests\Unit;

use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserServiceTest extends TestCase
{

    use RefreshDatabase;
 
    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * @var UserService
     */
    protected $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(UserService::class);
    }

    /**
     * Tests the method getUserFromRequest passing an existing user_id
     *
     * @return void
     */
    public function testGetUserFromRequestUserFound()
    {
        $request = Request::createFromGlobals();
        $request->merge(['user_id' => 1]);

        $user = $this->service->getUserFromRequest($request);

        $this->assertInstanceOf(\App\Models\User::class, $user, 'The getUser method did not return an User object');
        $this->assertNotNull($user, 'The user found is null');
    }

    /**
     * Tests the method getUserFromRequest passing a non existing user_id
     *
     * @return void
     */
    public function testGetUserFromRequestUserNotFound()
    {
        $request = Request::createFromGlobals();
        $request->merge(['user_id' => 4198541]);

        $user = $this->service->getUserFromRequest($request);

        $this->assertNull($user, 'The user found is null');
    }

}
