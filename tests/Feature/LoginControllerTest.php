<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    const USER_PASSWORD = 'password';
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'test-user',
            'password' => bcrypt(self::USER_PASSWORD),
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function home(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function home_ログインフォーム表示(): void
    {
        $response = $this->get(route('home'));
        $response->assertSee('method="POST"', false)
            ->assertSee('action="' . route('login') . '"', false)
            ->assertSee('name="name"', false)
            ->assertSee('name="password"', false);
    }

    /**
     * @test
     * @return void
     */
    public function home_ログイン後にログアウトボタン表示(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('home'));
        $response->assertSee('ログアウト', false)
            ->assertSee('<strong>' . $this->user->name . '</strong>', false);
    }

    /**
     * @test
     * @return void
     */
    public function home_ログイン前はログアウトボタン非表示(): void
    {
        $response = $this->get(route('home'));
        $response->assertDontSee('ログアウト', false);
    }

    /**
     * @test
     * @return void
     */
    public function login(): void
    {
        $response = $this->post(route('login'), [
            'name' => $this->user->name,
            'password' => self::USER_PASSWORD,
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('memo.create'));
    }

    /**
     * @test
     * @return void
     */
    public function login_error_ログイン失敗(): void
    {
        $response = $this->post(route('login'), [
            'name' => $this->user->name,
            'password' => self::USER_PASSWORD . '-invalid',
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors([
                'name' => __('auth.failed'),
                'password' => __('auth.failed'),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function login_error_空欄(): void
    {
        $response = $this->post(route('login'), [
            'name' => '',
            'password' => '',
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors([
                'name' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
                'password' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function login_error_入力文字種(): void
    {
        $response = $this->post(route('login'), [
            'name' => '@',
            'password' => 'password',
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors([
                'name' => __('validation.alpha_dash', ['attribute' => __('validation.attributes.name')]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function login_error_入力文字数(): void
    {
        $max = 255;
        $response = $this->post(route('login'), [
            'name' => str_repeat('a', $max + 1),
            'password' => str_repeat('a', $max + 1),
        ]);
        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'name' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => $max]),
                'password' => __('validation.max.string', ['attribute' => __('validation.attributes.password'), 'max' => $max]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function logout(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('logout'));
        $response->assertStatus(302)
            ->assertRedirect(route('home'))
            ->assertSessionHas('success', __('auth.logout'));
    }

}
