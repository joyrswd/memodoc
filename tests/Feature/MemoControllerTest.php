<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Memo;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemoControllerTest extends TestCase
{
    use RefreshDatabase;
    private $user;
    private $memo;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create();
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
        $this->post(route('memo.store'))->assertRedirect(route('home'));
        $this->get(route('memo.index'))->assertRedirect(route('home'));
        $this->get(route('memo.edit', ['memo' => $this->memo->id]))->assertRedirect(route('home'));
        $this->put(route('memo.update', ['memo' => $this->memo->id]))->assertRedirect(route('home'));
        $this->delete(route('memo.destroy', ['memo' => $this->memo->id]))->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function auth_error_memoId(): void
    {
        $this->get(route('memo.edit', ['memo' => $this->memo->id]))->assertStatus(404);
        $this->put(route('memo.update', ['memo' => $this->memo->id]))->assertStatus(404);
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
            ->assertSee('name="memo_content"', false)
            ->assertSee('name="memo_tags"', false);
    }

    /**
     * @test
     * @return void
     */
    public function create_error_メモが空(): void
    {
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'));

        $response->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors([
                'memo_content' => __('validation.required', [
                    'attribute' => __('validation.attributes.memo_content'),
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
                'memo_content' =>  'メモの内容',
                'memo_tags' =>  str_repeat('あ', $max + 1),
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
                'memo_content' =>  'メモの内容',
                'memo_tags' =>  str_repeat('あ', $min - 1),
            ]);
        $response->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.min.string', [
                    'attribute' => __('validation.attributes.tag'),
                    'min' => $min,
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function create_error_タグの不正文字(): void
    {
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  'メモの内容',
                'memo_tags' =>  '#!#$%',
            ]);
        $response->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.regex', [
                    'attribute' => __('validation.attributes.tag'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function store(): void
    {
        $memoId = Memo::factory()->create()->id + 1;
        $tagId = Tag::factory()->create()->id + 1;
        $response = $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  'メモの内容',
                'memo_tags' =>  'tag1 タグ2',
            ]);
        $this->assertDatabaseHas('memos', [
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'tag1',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'タグ2',
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $memoId,
            'tag_id' => $tagId,
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $memoId,
            'tag_id' => $tagId + 1,
        ]);

        $response->assertRedirect(route('memo.create'));
    }

    /**
     * @test
     * @return void
     */
    public function index(): void
    {
        $response = $this->get(route('memo.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function index_検索フォーム表示(): void
    {
        $response = $this->get(route('memo.index'));
        $response->assertSee('method="GET"', false)
            ->assertSee('action="' . route('memo.index') . '"', false)
            ->assertSee('name="memo_content"', false)
            ->assertSee('name="memo_tags"', false)
            ->assertSee('name="memo_from"', false)
            ->assertSee('name="memo_to"', false);
    }

    /**
     * @test
     * @return void
     */
    public function index_検索_メモの内容(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->get(route('memo.index', [
            'memo_content' => 'メモの内容',
        ]));
        $response->assertSee('value="メモの内容"', false)
            ->assertSee('href="' . route('memo.edit', ['memo' => $memo->id]) . '"', false);
    }

    /**
     * @test
     * @return void
     */
    public function index_検索_タグ(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        Tag::factory()->create([
            'name' => 'tag1',
        ])->memos()->attach($memo->id);

        $response = $this->get(route('memo.index', [
            'memo_tags' => 'tag1',
        ]));
        $response->assertSee('value="tag1"', false)
            ->assertSee('href="' . route('memo.index', ['memo_tags' => 'tag1']) . '"', false);
    }

    /**
     * @test
     * @return void
     */
    public function index_検索_日付(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->get(route('memo.index', [
            'memo_from' => $memo->created_at->format('Y-m-d'),
            'memo_to' => $memo->created_at->format('Y-m-d'),
        ]));
        $response->assertSee('value="' . $memo->created_at->format('Y-m-d') . '"', false)
            ->assertSee('href="' . route('memo.edit', ['memo' => $memo->id]) . '"', false);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_メモの最大文字数(): void
    {
        $max = 100;
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_content' => str_repeat('あ', $max + 1),
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'memo_content' => __('validation.max.string', [
                    'attribute' => __('validation.attributes.memo_content'),
                    'max' => $max,
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_メモの最小文字数(): void
    {
        $min = 2;
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_content' => str_repeat('あ', $min - 1),
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'memo_content' => __('validation.min.string', [
                    'attribute' => __('validation.attributes.memo_content'),
                    'min' => $min,
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_タグの最大文字数(): void
    {
        $max = 20;
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_tags' => str_repeat('あ', $max + 1),
            ]));
        $response->assertRedirect(route('memo.index'))
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
    public function index_error_タグの最小文字数(): void
    {
        $min = 2;
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_tags' => str_repeat('あ', $min - 1),
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.min.string', [
                    'attribute' => __('validation.attributes.tag'),
                    'min' => $min,
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_タグの不正文字(): void
    {
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_tags' => '#!#$%',
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.regex', [
                    'attribute' => __('validation.attributes.tag'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_日付の不正文字(): void
    {
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_from' => 'あ',
                'memo_to' => 'あ',
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'memo_from' => __('validation.date', [
                    'attribute' => __('validation.attributes.memo_from'),
                ]),
                'memo_to' => __('validation.date', [
                    'attribute' => __('validation.attributes.memo_to'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_日付の不正範囲(): void
    {
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_from' => '2021-01-02',
                'memo_to' => '2021-01-01',
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'memo_from' => __('validation.before_or_equal', [
                    'attribute' => __('validation.attributes.memo_from'),
                    'date' => __('validation.attributes.memo_to'),
                ]),
                'memo_to' => __('validation.after_or_equal', [
                    'attribute' => __('validation.attributes.memo_to'),
                    'date' => __('validation.attributes.memo_from'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_日付の最大日付(): void
    {
        $response = $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_from' => '9999-12-31',
                'memo_to' => '9999-12-31',
            ]));
        $response->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors([
                'memo_from' => __('validation.before_or_equal', [
                    'attribute' => __('validation.attributes.memo_from'),
                    'date' => __('validation.values.memo_from.today'),
                ]),
                'memo_to' => __('validation.before_or_equal', [
                    'attribute' => __('validation.attributes.memo_to'),
                    'date' => __('validation.values.memo_to.today'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function edit(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->get(route('memo.edit', ['memo' => $memo->id]));
        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function edit_フォーム表示(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->get(route('memo.edit', ['memo' => $memo->id]));
        $response->assertSee('method="POST"', false)
            ->assertSee('action="' . route('memo.update', ['memo' => $memo->id]) . '"', false)
            ->assertSee('name="memo_tags"', false);
    }

    /**
     * @test
     * @return void
     */
    public function edit_error_タグの最大文字数(): void
    {
        $max = 20;
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->from(route('memo.edit', ['memo' => $memo->id]))
            ->put(route('memo.update', ['memo' => $memo->id]), [
                'memo_tags' => str_repeat('あ', $max + 1),
            ]);
        $response->assertRedirect(route('memo.edit', ['memo' => $memo->id]))
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
    public function edit_error_タグの最小文字数(): void
    {
        $min = 2;
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->from(route('memo.edit', ['memo' => $memo->id]))
            ->put(route('memo.update', ['memo' => $memo->id]), [
                'memo_tags' => str_repeat('あ', $min - 1),
            ]);
        $response->assertRedirect(route('memo.edit', ['memo' => $memo->id]))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.min.string', [
                    'attribute' => __('validation.attributes.tag'),
                    'min' => $min,
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function edit_error_タグの不正文字(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $response = $this->from(route('memo.edit', ['memo' => $memo->id]))
            ->put(route('memo.update', ['memo' => $memo->id]), [
                'memo_tags' => '#!#$%',
            ]);
        $response->assertRedirect(route('memo.edit', ['memo' => $memo->id]))
            ->assertSessionHasErrors([
                'tags.0' => __('validation.regex', [
                    'attribute' => __('validation.attributes.tag'),
                ]),
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function update(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $tag1 = Tag::factory()->create([
            'name' => 'tag1',
        ]);
        $tag2 = Tag::factory()->create([
            'name' => 'タグ2',
        ]);
        $memo->tags()->attach($tag1->id);
        $memo->tags()->attach($tag2->id);

        $response = $this->from(route('memo.edit', ['memo' => $memo->id]))
            ->put(route('memo.update', ['memo' => $memo->id]), [
                'memo_tags' => 'tag3 タグ4',
            ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'tag3',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'タグ4',
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $memo->id,
            'tag_id' => $tag2->id + 1,
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $memo->id,
            'tag_id' => $tag2->id + 2,
        ]);
        $this->assertDatabaseMissing('memo_tag', [
            'memo_id' => $memo->id,
            'tag_id' => $tag1->id,
        ]);
        $this->assertDatabaseMissing('memo_tag', [
            'memo_id' => $memo->id,
            'tag_id' => $tag2->id,
        ]);

        $response->assertRedirect(route('memo.edit', ['memo' => $memo->id]));
    }

    /**
     * @test
     * @return void
     */
    public function destroy(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->from(route('memo.index'))
            ->delete(route('memo.destroy', ['memo' => $memo->id]));
        $this->assertSoftDeleted('memos', [
            'id' => $memo->id,
        ]);
        $response->assertRedirect(route('memo.index'));
    }

    /**
     * @test
     * @return void
     */
    public function destroy_error_メモが存在しない(): void
    {
        $response = $this->from(route('memo.index'))
            ->delete(route('memo.destroy', ['memo' => 0]));
        $response->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function destroy_error_メモが他のユーザーのもの(): void
    {
        $memo = Memo::factory()->create();
        $response = $this->from(route('memo.index'))
            ->delete(route('memo.destroy', ['memo' => $memo->id]));
        $response->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function destroy_error_メモが既に削除済み(): void
    {
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $memo->delete();
        $response = $this->from(route('memo.index'))
            ->delete(route('memo.destroy', ['memo' => $memo->id]));
        $response->assertStatus(404);
    }


}
