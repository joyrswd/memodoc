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
    private User $user;
    private Memo $memo;
    private Tag $tag;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->memo = Memo::factory(['user_id' => $this->user->id])->create();
        $this->tag = Tag::factory()->create();
        $this->memo->tags()->attach($this->tag->id);
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
        $memo = Memo::factory()->create();
        $this->get(route('memo.edit', ['memo' => $memo->id]))->assertStatus(404);
        $this->put(route('memo.update', ['memo' => $memo->id]))->assertStatus(404);
        $this->delete(route('memo.destroy', ['memo' => $memo->id]))->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function create(): void
    {
        $this->get(route('memo.create'))->assertOk()->assertViewIs('memo.create');
    }


    /**
     * @test
     * @return void
     */
    public function store(): void
    {
        $memoId = Memo::factory()->create()->id + 1;
        $tagId = Tag::factory()->create()->id + 1;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  'メモの内容',
                'tags' =>  ['tag1', 'aＡ1１あ亜'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.index'));
        $this->assertDatabaseHas('memos', [
            'user_id' => $this->user->id,
            'content' => 'メモの内容',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'tag1',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'aＡ1１あ亜',
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $memoId,
            'tag_id' => $tagId,
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $memoId,
            'tag_id' => $tagId + 1,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function store_with_add_next_redirect(): void
    {
        $tags = ['tag1', 'タグ2'];
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  'メモの内容',
                'tags' =>  $tags,
                'add_next' => 1,
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasInput('add_next', 1)
            ->assertSessionHasInput('tags', $tags);
    }

    /**
     * @test
     * @return void
     */
    public function store_with_zenkaku_lmit(): void
    {
        $max = 140;
        $content = str_repeat('あ', $max);
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  $content,
            ])->assertRedirect(route('memo.index'));
        $this->assertDatabaseHas('memos', [
            'user_id' => $this->user->id,
            'content' => $content,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function store_with_url_limit(): void
    {
        $max = 280;
        $url = 'http://test.com/' . str_repeat('a', 23) . ' ';
        $content = $url . str_repeat('a', $max - 24);
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  $content,
            ])->assertRedirect(route('memo.index'));
        $this->assertDatabaseHas('memos', [
            'user_id' => $this->user->id,
            'content' => $content,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_empty(): void
    {
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => '',
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_overflow(): void
    {
        $max = 280;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => str_repeat('a', $max + 1),
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_zenkaku_overflow(): void
    {
        $max = 140;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => str_repeat('あ', $max + 1),
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_with_tags_overflow(): void
    {
        $max = 280;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => str_repeat('a', $max - 1),
                'tags' => ['aa'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_underflow(): void
    {
        $min = 10;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => str_repeat('a', $min - 1),
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_zenkaku_underflow(): void
    {
        $min = 5;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => str_repeat('あ', $min - 1),
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_content_with_tags_underflow(): void
    {
        $min = 10;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => str_repeat('a', $min - 1),
                'tags' => ['aa'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['memo_content']);
    }


    /**
     * @test
     * @return void
     */
    public function store_error_tags_not_sent(): void
    {
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => 'メモの内容',
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['tags']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_tags_empty(): void
    {
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' => 'メモの内容',
                'tags' => [],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['tags']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_tags_overflow(): void
    {
        $max = 20;
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  'メモの内容',
                'tags' =>  [str_repeat('あ', $max + 1)],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['tags.0']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_tags_invalid(): void
    {
        $this->from(route('memo.create'))
            ->post(route('memo.store'), [
                'memo_content' =>  'メモの内容',
                'tags' =>  ['#!#$%'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.create'))
            ->assertSessionHasErrors(['tags.0']);
    }

    /**
     * @test
     * @return void
     */
    public function index(): void
    {
        $this->get(route('memo.index'))->assertOk()->assertViewIs('memo.index');
    }

    /**
     * @test
     * @return void
     */
    public function index_content(): void
    {
        $this->get(route('memo.index', [
            'memo_content' => mb_substr($this->memo->content, 0, 5),
        ]))->assertOk()->assertViewIs('memo.index')
        ->assertSee(route('memo.edit', ['memo' => $this->memo->id]));
    }

    /**
     * @test
     * @return void
     */
    public function index_tags(): void
    {
        $this->get(route('memo.index', [
            'tags' => [$this->tag->name],
        ]))->assertOk()->assertViewIs('memo.index')
        ->assertSee(route('memo.edit', ['memo' => $this->memo->id]));
    }

    /**
     * @test
     * @return void
     */
    public function index_from(): void
    {
        $this->get(route('memo.index', [
            'memo_from' => $this->memo->created_at->format('Y-m-d'),
        ]))->assertOk()->assertViewIs('memo.index')
        ->assertSee(route('memo.edit', ['memo' => $this->memo->id]));
    }

    /**
     * @test
     * @return void
     */
    public function index_to(): void
    {
        $this->get(route('memo.index', [
            'memo_to' => $this->memo->created_at->format('Y-m-d'),
        ]))->assertOk()->assertViewIs('memo.index')
        ->assertSee(route('memo.edit', ['memo' => $this->memo->id]));
    }

    /**
     * @test
     * @return void
     */
    public function index_error_content_overflow(): void
    {
        $max = 100;
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_content' => str_repeat('あ', $max + 1),
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_content_underflow(): void
    {
        $min = 2;
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_content' => str_repeat('あ', $min - 1),
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_tags_overflow(): void
    {
        $max = 20;
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_tags' => str_repeat('あ', $max + 1),
        ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['tags.*']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_tags_underflow(): void
    {
        $min = 2;
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_tags' => str_repeat('あ', $min - 1),
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['tags.*']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_tags_invalid(): void
    {
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_tags' => '#!#$%',
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['tags.*']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_invalid(): void
    {
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_from' => 'あ',
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_from']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_to_invalid(): void
    {
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_to' => 'あ',
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_to']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_tomorrow(): void
    {
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_from' => date('Y-m-d', strtotime('+1 day')),
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_from']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_to_tomorrow(): void
    {
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_to' => date('Y-m-d', strtotime('+1 day')),
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_to']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_to(): void
    {
        $this->from(route('memo.index'))
            ->get(route('memo.index', [
                'memo_from' => date('Y-m-d'),
                'memo_to' => date('Y-m-d', strtotime('-1 day')),
            ]))->assertRedirect(route('memo.index'))
            ->assertSessionHasErrors(['memo_from']);
    }

    /**
     * @test
     * @return void
     */
    public function edit(): void
    {
        $this->get(route('memo.edit', ['memo' => $this->memo->id]))->assertOk()->assertViewIs('memo.edit');
    }

    /**
     * @test
     * @return void
     */
    public function update(): void
    {
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'tags' => ['tag1', 'タグ2'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tags', [
            'name' => 'tag1',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'タグ2',
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $this->memo->id,
            'tag_id' => Tag::where('name', 'tag1')->first()->id,
        ]);
        $this->assertDatabaseHas('memo_tag', [
            'memo_id' => $this->memo->id,
            'tag_id' => Tag::where('name', 'タグ2')->first()->id,
        ]);
        $this->assertDatabaseMissing('memo_tag', [
            'memo_id' => $this->memo->id,
            'tag_id' => $this->tag->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function update_with_url_limit(): void
    {
        $max = 280;
        $url = 'https://test.com/' . str_repeat('a', $max);
        $this->memo->content = $url;
        $this->memo->save();
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'tags' => ['tag1', 'タグ2'],
                'has_tag' => 1,
            ])
            ->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasNoErrors();
    }

    /**
     * @test
     * @return void
     */
    public function update_error_tags_not_sent(): void
    {
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'has_tag' => 1,
            ])->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasErrors(['tags']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_tags_empty(): void
    {
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'tags' => [],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasErrors(['tags']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_tags_overflow(): void
    {
        $max = 20;
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'tags' => [str_repeat('あ', $max + 1)],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasErrors(['tags.*']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_tags_invalid(): void
    {
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'tags' => ['#!#$%'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasErrors(['tags.*']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_tags_with_content_overflow(): void
    {
        $this->memo->content = str_repeat('a', 280 - 1);
        $this->memo->save();
        $this->from(route('memo.edit', ['memo' => $this->memo->id]))
            ->put(route('memo.update', ['memo' => $this->memo->id]), [
                'tags' => ['bb'],
                'has_tag' => 1,
            ])->assertRedirect(route('memo.edit', ['memo' => $this->memo->id]))
            ->assertSessionHasErrors(['memo_content']);
    }

    /**
     * @test
     * @return void
     */
    public function destroy(): void
    {
        $this->from(route('memo.index'))
            ->delete(route('memo.destroy', ['memo' => $this->memo->id]))
            ->assertRedirect(route('memo.index'));
        $this->assertSoftDeleted('memos', [
            'id' => $this->memo->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function destroy_error_deleted(): void
    {
        $this->memo->delete();
        $this->from(route('memo.index'))
            ->delete(route('memo.destroy', ['memo' => $this->memo->id]))
            ->assertStatus(404);
    }
}
