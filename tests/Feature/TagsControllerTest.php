<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Memo;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagsControllerTest extends TestCase
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
        $this->get(route('tags.index'))->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function index(): void
    {
        $this->get(route('tags.index'))->assertOk()->assertSeeText($this->tag->name);
    }
    
    /**
     * @test
     * @return void
     */
    public function index_not_include_oter_user_tags(): void
    {
        $otherUser = User::factory()->create();
        $otherMemo = Memo::factory(['user_id' => $otherUser->id])->create();
        $otherTag = Tag::factory()->create();
        $otherMemo->tags()->attach($otherTag->id);
        $this->get(route('tags.index'))->assertOk()->assertDontSeeText($otherTag->name);
    }

    /**
     * @test
     * @return void
     */
    public function index_not_include_deleted_memo_tags(): void
    {
        $this->memo->delete();
        $this->get(route('tags.index'))->assertOk()->assertDontSeeText($this->tag->name);
    }

}
