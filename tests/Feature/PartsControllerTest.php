<?php

namespace Tests\Feature;

use App\Models\Memo;
use App\Models\User;
use App\Repositories\PartsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PartsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Memo
     */
    private $memo;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->memo = Memo::factory(['user_id' => $this->user->id])->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function auth_error()
    {
        $this->get(route('logout'));
        $this->assertGuest();
        $this->get(route('parts.index'))->assertRedirect(route('home'));
        $this->put(route('parts.add', $this->memo))->assertRedirect(route('home'));
        $this->delete(route('parts.remove', $this->memo))->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function auth_error_memoId(): void
    {
        $memo = Memo::factory()->create();
        $this->put(route('parts.add', $memo))->assertStatus(404);
        $this->delete(route('parts.remove', $memo))->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function index(): void
    {
        $this->get(route('parts.index'))->assertOk()->assertViewIs('parts.index');
    }

    /**
     * @test
     * @return void
     */
    public function add(): void
    {
        $memo = Memo::factory(['user_id' => $this->user->id])->create();
        $this->put(route('parts.add', $memo))->assertOk()
            ->assertSessionHas(PartsRepository::KEY, [$memo->id])
            ->assertJson([
                'status' => 'success',
                'message' => '追加しました。',
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function add_error_overlimit(): void
    {
        for ($i = 0; $i < PartsRepository::LIMIT; $i++) {
            $memo = Memo::factory(['user_id' => $this->user->id])->create();
            $this->put(route('parts.add', $memo))->assertOk();
        }
        $memo = Memo::factory(['user_id' => $this->user->id])->create();
        $this->put(route('parts.add', $memo))->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'これ以上追加できません。',
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function add_error_overlapped(): void
    {
        $this->put(route('parts.add', $this->memo))->assertOk();
        $this->put(route('parts.add', $this->memo))->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'すでに存在しています。',
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function remove(): void
    {
        $this->put(route('parts.add', $this->memo))->assertOk();
        $this->delete(route('parts.remove', $this->memo))->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => '削除しました。',
            ])->assertSessionHas(PartsRepository::KEY, function ($parts)  {
                return !in_array($this->memo->id, $parts);
            });
    }

    /**
     * @test
     * @return void
     */
    public function remove_all(): void
    {
        for ($i = 0; $i < PartsRepository::LIMIT; $i++) {
            $memo = Memo::factory(['user_id' => $this->user->id])->create();
            $this->put(route('parts.add', $memo))->assertOk();
        }
        $this->delete(route('parts.remove'))->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => '削除しました。',
                'count' => '0',
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function remove_error_not_in_session(): void
    {
        $this->delete(route('parts.remove', $this->memo))->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => '存在しません。',
            ]);
    }
}
