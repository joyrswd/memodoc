<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Passwords\PasswordBroker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public const USER_PASSWORD = 'password';
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

    private function createToken(): string
    {
        $broker = app(PasswordBroker::class);
        return $broker->createToken($this->user);
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
    public function home_hide_logout(): void
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

    /**
     * @test
     * @return void
     */
    public function verification_redirect(): void
    {
        //　未認証状態にする
        $this->user->email_verified_at = null;
        $this->actingAs($this->user);
        // メモ作成画面にアクセスしてメール認証ページにリダイレクトされることを確認
        $this->get(route('memo.create'))->assertRedirect(route('verification.notice'));
    }

    /**
     * @test
     * @return void
     */
    public function verification_notice(): void
    {
        $this->user->email_verified_at = null;
        $this->actingAs($this->user);
        $this->get(route('verification.notice'))->assertOk()->assertViewIs('login.email_notice');
    }

    /**
     * @test
     * @return void
     */
    public function verification_resend(): void
    {
        $this->user->email_verified_at = null;
        $this->actingAs($this->user);
        $this->from(route('verification.notice'))->post(route('verification.resend'))
        ->assertRedirect(route('verification.notice'))
        ->assertSessionHas('success');
    }

    /**
     * @test
     * @return void
     */
    public function verification_verify(): void
    {
        $this->user->email_verified_at = null;
        $this->actingAs($this->user);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );
        $this->get($verificationUrl)->assertRedirect(route('memo.create'))
        ->assertSessionHas('success');
    }

    /**
     * @test
     * @return void
     */
    public function password_request(): void
    {
        $this->get(route('password.request'))->assertOk()->assertViewIs('login.password_request');
    }

    /**
     * @test
     * @return void
     */
    public function password_email(): void
    {
        $this->from(route('password.request'))->post(route('password.email'), [
            'email' => $this->user->email,
        ])->assertRedirect(route('password.request'))
        ->assertSessionHas('success');
    }

    /**
     * @test
     * @return void
     */
    public function password_reset(): void
    {
        $token = $this->createToken();
        $email = $this->user->email;
        $this->get(route('password.reset', ['token' => $token, 'email' => $email]))
        ->assertOk()
        ->assertViewIs('login.password_reset');
    }

    /**
     * @test
     * @return void
     */
    public function password_update(): void
    {
        $token = $this->createToken();
        $newPassword = self::USER_PASSWORD . '-new';
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertRedirect(route('home'))
        ->assertSessionHas('success');
        $this->assertTrue(\Hash::check($newPassword, $this->user->fresh()->password));
    }

    /**
     * @test
     * @return void
     */
    public function password_update_error_invalid(): void
    {
        $token = $this->createToken();
        $newPassword = self::USER_PASSWORD . '-new';
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword . '-invalid',
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['password']);
    }

    /**
     * @test
     * @return void
     */
    public function password_update_error_empty(): void
    {
        $token = $this->createToken();
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['email','password']);
    }

    /**
     * @test
     * @return void
     */
    public function password_update_error_invalid_input(): void
    {
        $token = $this->createToken();
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => '@',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['email']);
    }


    /**
     * @test
     * @return void
     */
    public function password_update_error_overflow(): void
    {
        $token = $this->createToken();
        $max = 255;
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => str_repeat('a', $max + 1),
            'password' => str_repeat('a', $max + 1),
            'password_confirmation' => str_repeat('a', $max + 1),
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['email','password']);
    }

    /**
     * @test
     * @return void
     */
    public function password_update_error_invalid_token(): void
    {
        $token = $this->createToken();
        $newPassword = self::USER_PASSWORD . '-new';
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token . '-invalid',
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['email']);
    }

    /**
     * @test
     * @return void
     */
    public function password_update_error_invalid_email(): void
    {
        $token = $this->createToken();
        $newPassword = self::USER_PASSWORD . '-new';
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => $this->user->email . '-invalid',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['email']);
    }

    /**
     * @test
     * @return void
     */
    public function password_update_error_invalid_password_confirmation(): void
    {
        $token = $this->createToken();
        $newPassword = self::USER_PASSWORD . '-new';
        $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword . '-invalid',
        ])->assertRedirect(route('password.reset', ['token' => $token]))
        ->assertSessionHasErrors(['password']);
    }

    /**
     * @test
     * @return void
     */
    public function about(): void
    {
        $this->get(route('about'))->assertOk()->assertViewIs('login.about');
        $this->get(route('home'))->assertSee(route('about'), false);
    }
}
