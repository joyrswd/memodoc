<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function create() :void
    {
        $this->get(route('user.create'))->assertStatus(200)->assertViewIs('user.create');
    }

    /**
     * @test
     */
    public function create_redirect() :void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get(route('user.create'))->assertRedirect(route('home'));
    }


    /**
     * @test
     */
    public function store() :void
    {
        $name = 'aA_-1';
        $this->post(route('user.store'), [
            'user_name' => $name,
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertRedirect(route('memo.create'));
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => 'test@localhost',
        ]);
    }

    /**
     * @test
     */
    public function store_redirect(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertRedirect(route('home'));
    }

    /**
     * @test
     */
    public function store_error_name_required(): void
    {
        $this->post(route('user.store'), [
            'user_name' => '',
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_name']);
    }

    /**
     * @test
     */
    public function store_error_name_alpha_dash(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'あいうえお',
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_name']);
    }

    /**
     * @test
     */
    public function store_error_name_min(): void
    {
        $min = 3;
        $this->post(route('user.store'), [
            'user_name' => str_repeat('a', $min - 1),
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_name']);
    }

    /**
     * @test
     */
    public function store_error_name_max(): void
    {
        $max = 255;
        $this->post(route('user.store'), [
            'user_name' => str_repeat('a', $max + 1),
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_name']);
    }

    /**
     * @test
     */
    public function store_error_name_unique(): void
    {
        $user = User::factory()->create();
        $this->post(route('user.store'), [
            'user_name' => $user->name,
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_name']);
    }

    /**
     * @test
     */
    public function store_error_email_required(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => '',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_email']);
    }

    /**
     * @test
     */
    public function store_error_email_invalid(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test',
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_email']);
    }

    /**
     * @test
     */
    public function store_error_email_max(): void
    {
        $max = 255;
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => str_repeat('a', $max + 1),
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_email']);
    }

    /**
     * @test
     */
    public function store_error_email_unique(): void
    {
        $user = User::factory()->create();
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => $user->email,
            'user_password' => 'password',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_email']);
    }

    /**
     * @test
     */
    public function store_error_password_required(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => '',
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_password']);
    }

    /**
     * @test
     */
    public function store_error_password_invalid(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => 'あいうえお',
            'user_password_confirmation' => 'あいうえお',
        ])->assertSessionHasErrors(['user_password']);
    }

    /**
     * @test
     */
    public function store_error_password_min(): void
    {
        $min = 8;
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => str_repeat('a', $min - 1),
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_password']);
    }

    /**
     * @test
     */
    public function store_error_password_max(): void
    {
        $max = 255;
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => str_repeat('a', $max + 1),
            'user_password_confirmation' => 'password',
        ])->assertSessionHasErrors(['user_password']);
    }

    /**
     * @test
     */
    public function store_error_password_confirmed(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => 'password_confirmation',
        ])->assertSessionHasErrors(['user_password']);
    }

    /**
     * @test
     */
    public function store_error_password_confirmation_required(): void
    {
        $this->post(route('user.store'), [
            'user_name' => 'test',
            'user_email' => 'test@localhost',
            'user_password' => 'password',
            'user_password_confirmation' => '',
        ])->assertSessionHasErrors(['user_password']);
    }

}
