<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemoControllerTest extends TestCase
{
    use RefreshDatabase;
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     * @return void
     */
    public function auth_error(): void
    {
        $this->get(route('logout'));
        $this->assertGuest();
        $this->get(route('memo.create'))->assertRedirect(route('home'));
        $this->get(route('memo.store'))->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function create(): void
    {
        $response = $this->get(route('memo.create'));
        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function create_フォーム表示(): void
    {
        $response = $this->get(route('memo.create'));

        $response->assertSee('method="POST"', false)
            ->assertSee('action="' . route('memo.store') . '"', false)
            ->assertSee('name="memo[add][content]"', false)
            ->assertSee('name="memo[add][tags]"', false);
    }

    /**
     * @test
     * @return void
     */
    public function store(): void
    {
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'), [
            'memo' =>  ['add' => [
                'content' => 'メモの内容',
                'tags' => 'タグ1 タグ2'
            ]],
        ]);
        $this->assertDatabaseHas('memos', [
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'タグ1',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'タグ2',
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => 1,
            'tag_id' => 1,
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => 1,
            'tag_id' => 2,
        ]);

        $response->assertRedirect(route('memo.create'));
    }

    /**
     * @test
     * @return void
     */
    public function create_error_メモが空(): void
    {
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'), ['memo.add.content' => '']);

        $response->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors([
                'memo.add.content' => __('validation.required', [
                    'attribute' => __('validation.attributes.memo.content'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function create_error_タグの最大文字数(): void
    {
        $max = 20;
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo' =>  ['add' => [
                    'content' => 'メモの内容',
                    'tags' => str_repeat('あ', $max+1)
                ]],
            ]);
        $response->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.max.string', [
                    'attribute' => __('validation.attributes.tag'),
                    'max' => $max,
                ]),
            ]);
    }
    
    /**
     * @test
     * @return void
     */
    public function create_error_タグの最小文字数(): void
    {
        $min = 2;
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo' =>  ['add' => [
                    'content' => 'メモの内容',
                    'tags' => str_repeat('あ', $min -1)
                ]],
            ]);
        $response->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.min.string', [
                    'attribute' => __('validation.attributes.tag'),
                    'min' => $min,
                ]),
            ]);
    }

}
