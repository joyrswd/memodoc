<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ApiJob;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ApiJob $apiJob;
    private Document $document;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->apiJob = ApiJob::factory(['user_id' => $this->user->id])->create();
        $this->document = Document::factory(['user_id' => $this->user->id, 'api_job_id' => $this->apiJob->id])->create();
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
        $this->get(route('doc.index'))->assertRedirect(route('home'));
        $this->get(route('doc.edit', ['doc' => $this->document->id]))->assertRedirect(route('home'));
        $this->put(route('doc.update', ['doc' => $this->document->id]))->assertRedirect(route('home'));
        $this->delete(route('doc.destroy', ['doc' => $this->document->id]))->assertRedirect(route('home'));
    }
    
    /**
     * @test
     * @return void
     */
    public function auth_error_docId(): void
    {
        $document = Document::factory()->create();
        $this->get(route('doc.edit', ['doc' => $document->id]))->assertStatus(404);
        $this->put(route('doc.update', ['doc' => $document->id]))->assertStatus(404);
        $this->delete(route('doc.destroy', ['doc' => $document->id]))->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function index(): void
    {
        $this->get(route('doc.index'))->assertOk()->assertViewIs('document.index');
    }
    /**
     * @test
     * @return void
     */
    public function index_title(): void
    {
        $search = substr($this->document->title, 0, 50);
        $this->get(route('doc.index', ['doc_title' => $search]))->assertOk()->assertViewIs('document.index')
            ->assertSee($this->document->title);
    }

    /**
     * @test
     * @return void
     */
    public function index_content(): void
    {
        $search = substr($this->document->content, 0, 50);
        $this->get(route('doc.index', ['doc_content' => $search]))->assertOk()->assertViewIs('document.index')
            ->assertSee($this->document->title);
    }

    /**
     * @test
     * @return void
     */
    public function index_from(): void
    {
        $search = $this->document->created_at->format('Y-m-d');
        $this->get(route('doc.index', ['doc_from' => $search]))->assertOk()->assertViewIs('document.index')
            ->assertSee($this->document->title);
    }

    /**
     * @test
     * @return void
     */
    public function index_to(): void
    {
        $search = $this->document->created_at->format('Y-m-d');
        $this->get(route('doc.index', ['doc_to' => $search]))->assertOk()->assertViewIs('document.index')
            ->assertSee($this->document->title);
    }

    /**
     * @test
     * @return void
     */
    public function edit(): void
    {
        $this->get(route('doc.edit', ['doc' => $this->document->id]))->assertOk()->assertViewIs('document.edit');
    }

    /**
     * @test
     * @return void
     */
    public function update(): void
    {
        $this->from(route('doc.edit', ['doc' => $this->document->id]))
            ->put(route('doc.update', ['doc' => $this->document->id]), [
            'doc_title' => 'test title',
            'doc_content' => 'test content',
        ])->assertRedirect(route('doc.edit', ['doc' => $this->document->id]))
        ->assertSessionHas('success', __('updated'));
        $this->assertDatabaseHas(Document::class, [
            'id' => $this->document->id,
            'title' => 'test title',
            'content' => 'test content',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function destroy(): void
    {
        $document = Document::factory(['user_id' => $this->user->id])->create();
        $this->from(route('doc.index'))
            ->delete(route('doc.destroy', ['doc' => $document->id]))
            ->assertRedirect(route('doc.index'))
            ->assertSessionHas('success', __('deleted'));
        $this->assertSoftDeleted(Document::class, [
            'id' => $document->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_title_overflow(): void
    {
        $max = 100;
        $this->get(route('doc.index', ['doc_title' => str_repeat('a', $max+1)]))->assertSessionHasErrors(['doc_title']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_content_overflow(): void
    {
        $max = 100;
        $this->get(route('doc.index', ['doc_content' => str_repeat('a', $max+1)]))->assertSessionHasErrors(['doc_content']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_tomorrow(): void
    {
        $this->get(route('doc.index', ['doc_from' => date('Y-m-d', strtotime('+1 day'))]))->assertSessionHasErrors(['doc_from']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_format(): void
    {
        $this->get(route('doc.index', ['doc_from' => 'aaaaaaaa']))->assertSessionHasErrors(['doc_from']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_to_tomorrow(): void
    {
        $this->get(route('doc.index', ['doc_to' => date('Y-m-d', strtotime('+1 day'))]))->assertSessionHasErrors(['doc_to']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_to_format(): void
    {
        $this->get(route('doc.index', ['doc_to' => 'aaaaaaaa']))->assertSessionHasErrors(['doc_to']);
    }
    
    /**
     * @test
     * @return void
     */
    public function index_error_from_to(): void
    {
        $this->get(route('doc.index', ['doc_from' => date('Y-m-d'), 'doc_to' => date('Y-m-d', strtotime('-1 day'))]))->assertSessionHasErrors(['doc_from']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_title_overflow(): void
    {
        $max = 255;
        $this->from(route('doc.edit', ['doc' => $this->document->id]))
            ->put(route('doc.update', ['doc' => $this->document->id]), [
            'doc_title' => str_repeat('a', $max+1),
            'doc_content' => 'test content',
        ])->assertRedirect(route('doc.edit', ['doc' => $this->document->id]))
        ->assertSessionHasErrors(['doc_title']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_title_empty(): void
    {
        $this->from(route('doc.edit', ['doc' => $this->document->id]))
            ->put(route('doc.update', ['doc' => $this->document->id]), [
            'doc_title' => '',
            'doc_content' => 'test content',
        ])->assertRedirect(route('doc.edit', ['doc' => $this->document->id]))
        ->assertSessionHasErrors(['doc_title']);
    }

    /**
     * @test
     * @return void
     */
    public function update_error_content_empty(): void
    {
        $this->from(route('doc.edit', ['doc' => $this->document->id]))
            ->put(route('doc.update', ['doc' => $this->document->id]), [
            'doc_title' => 'test title',
            'doc_content' => '',
        ])->assertRedirect(route('doc.edit', ['doc' => $this->document->id]))
        ->assertSessionHasErrors(['doc_content']);
    }

}
