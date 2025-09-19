<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Mockery;

class UserServiceTest extends TestCase
{
    public function test_register_creates_user()
    {
        // Sample input data
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123'
        ];

        // Mock the UserRepositoryInterface
        $mockRepo = Mockery::mock(UserRepositoryInterface::class);
        $mockRepo->shouldReceive('create')
                 ->once()
                 ->with(Mockery::on(function($arg) use ($data) {
                     // Ensure role is added if not present
                     return $arg['name'] === $data['name']
                         && $arg['email'] === $data['email']
                         && $arg['role'] === 'user';
                 }))
                 ->andReturn(new User([
                     'name' => $data['name'],
                     'email' => $data['email'],
                     'role' => 'user',
                 ]));

        $service = new UserService($mockRepo);

        $user = $service->register($data);

        // Assertions
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('user', $user->role);
    }

    public function test_register_with_admin_role()
    {
        $data = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role' => 'admin'
        ];

        $mockRepo = Mockery::mock(UserRepositoryInterface::class);
        $mockRepo->shouldReceive('create')
                 ->once()
                 ->with(Mockery::on(function ($arg) use ($data) {
                     return $arg['role'] === 'admin'
                         && $arg['name'] === $data['name']
                         && $arg['email'] === $data['email'];
                 }))
                 ->andReturn(new User($data));

        $service = new UserService($mockRepo);
        $user = $service->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals('Admin User', $user->name);
    }


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
