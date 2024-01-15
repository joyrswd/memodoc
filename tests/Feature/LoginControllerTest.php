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
        $this->get(route('home'))->assertOk()->assertViewIs('login.index');
    }

    /**
     * @test
     * @return void
     */
    public function home_show_logout(): void
    {
        $this->actingAs($this->user);
        $this->get(route('home'))->assertSee(route('logout'), false)
            ->assertSee('<strong>' . $this->user->name . '</strong>', false);
    }

    /**
     * @test
     * @return void
     */
    public function home_ログイン前はログアウトボタン非表示(): void
    {
        $this->get(route('home'))->assertDontSee(route('logout'), false);
    }

    /**
     * @test
     * @return void
     */
    public function login(): void
    {
        $this->post(route('login'), [
            'name' => $this->user->name,
            'password' => self::USER_PASSWORD,
        ])->assertRedirect(route('memo.create'));
    }

    /**
     * @test
     * @return void
     */
    public function login_error_invalid(): void
    {
        $this->post(route('login'), [
            'name' => $this->user->name,
            'password' => self::USER_PASSWORD . '-invalid',
        ])->assertRedirect(route('home'))
        ->assertSessionHasErrors(['name','password']);
    }

    /**
     * @test
     * @return void
     */
    public function login_error_empty(): void
    {
        $this->post(route('login'), [
            'name' => '',
            'password' => '',
        ])->assertRedirect(route('home'))
        ->assertSessionHasErrors(['name','password']);
    }

    /**
     * @test
     * @return void
     */
    public function login_error_invalid_input(): void
    {
        $this->post(route('login'), [
            'name' => '@',
            'password' => 'password',
        ])->assertRedirect(route('home'))
        ->assertSessionHasErrors(['name']);
    }

    /**
     * @test
     * @return void
     */
    public function login_error_overflow(): void
    {
        $max = 255;
        $this->post(route('login'), [
            'name' => str_repeat('a', $max + 1),
            'password' => str_repeat('a', $max + 1),
        ])->assertRedirect(route('home'))
        ->assertSessionHasErrors(['name','password']);
    }

    /**
     * @test
     * @return void
     */
    public function logout(): void
    {
        $this->actingAs($this->user);
        $this->get(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('success', __('auth.logout'));
    }

}
