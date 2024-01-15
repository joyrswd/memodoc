<?php

namespace Tests\Feature;
use App\Models\User;
use App\Models\Memo;
use App\Models\ApiJob;
use App\Repositories\ApiJobRepository;
use App\Jobs\GenerateDocumentJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Spatie\FlareClient\Api;
use Tests\TestCase;

class ApiJobControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private ApiJob $apiJob;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->memo = Memo::factory(['user_id' => $this->user->id])->create();
        $this->apiJob = ApiJob::factory(['user_id' => $this->user->id, 'status' => ApiJobRepository::STATUS_ABORTED])->create();
        $this->apiJob->memos()->attach($this->memo->id);
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
        $this->get(route('job.index'))->assertRedirect(route('home'));
        $this->post(route('job.store'))->assertRedirect(route('home'));
        $this->delete(route('job.destroy', ['job' => $this->apiJob->id]))->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function auth_error_jobId(): void
    {
        $apiJob = ApiJob::factory()->create();
        $this->delete(route('job.destroy', ['job' => $apiJob->id]))->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function index(): void
    {
        $this->get(route('job.index'))->assertOk()->assertViewIs('job.index');
    }

    /**
     * @test
     * @return void
     */
    public function index_from(): void
    {
        $search = $this->apiJob->created_at->format('Y-m-d');
        $this->get(route('job.index', ['job_from' => $search]))->assertOk()->assertViewIs('job.index')
            ->assertSee($this->apiJob->error_message);
    }

    /**
     * @test
     * @return void
     */
    public function index_to(): void
    {
        $search = $this->apiJob->created_at->format('Y-m-d');
        $this->get(route('job.index', ['job_to' => $search]))->assertOk()->assertViewIs('job.index')
            ->assertSee($this->apiJob->error_message);
    }

    /**
     * @test
     * @return void
     */
    public function index_status(): void
    {
        $this->get(route('job.index', ['job_status' => [$this->apiJob->status]]))->assertOk()->assertViewIs('job.index')
            ->assertSee($this->apiJob->error_message);
    }

    /**
     * @test
     * @return void
     */
    public function store(): void
    {
        // ジョブディスパッチを迂回する
        Bus::fake();
        //メモを作成
        $memo = Memo::factory(['user_id' => $this->user->id])->create();
        //メモをpartsセッションに登録
        $this->put(route('parts.add', ['memo' => $memo->id]))->assertOk();
        //ジョブを登録
        $this->post(route('job.store'), ['generate' => 'memo'])->assertRedirect(route('job.index'));
        //ジョブが登録されていることを確認
        Bus::assertDispatched(GenerateDocumentJob::class);
    }

    /**
     * @test
     * @return void
     */
    public function regenerate(): void
    {
        // ジョブディスパッチを迂回する
        Bus::fake();
        // 登録済みのジョブから書類を再作成する
        $this->from('job.index')->post(route('job.store', ['regenerate' => $this->apiJob->id]))->assertRedirect(route('job.index'));
        //ジョブが登録されていることを確認
        Bus::assertDispatched(GenerateDocumentJob::class);
    }

    /**
     * @test
     * @return void
     */
    public function destroy(): void
    {
        $this->from(route('job.index'))->delete(route('job.destroy', ['job' => $this->apiJob->id]))->assertRedirect(route('job.index'));
        $this->assertSoftDeleted(ApiJob::class, ['id' => $this->apiJob->id]);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_tomorrow(): void
    {
        $this->get(route('job.index', ['job_from' => date('Y-m-d', strtotime('+1 day'))]))->assertSessionHasErrors(['job_from']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_to_tomorrow(): void
    {
        $this->get(route('job.index', ['job_to' => date('Y-m-d', strtotime('+1 day'))]))->assertSessionHasErrors(['job_to']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_from_to(): void
    {
        $this->get(route('job.index', ['job_from' => date('Y-m-d'), 'job_to' => date('Y-m-d', strtotime('-1 day'))]))->assertSessionHasErrors(['job_from']);
    }

    /**
     * @test
     * @return void
     */
    public function index_error_status(): void
    {
        $this->get(route('job.index', ['job_status' => ['test']]))->assertSessionHasErrors(['job_status.*']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_empty(): void
    {
        $this->post(route('job.store'), ['generate' => 'memo'])->assertSessionHasErrors(['memos']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_not_your_memo(): void
    {
        $memo = Memo::factory()->create();
        $this->post(route('job.store'), ['generate' => 'memo', 'memos' => [$memo->id]])->assertSessionHasErrors(['memos.*']);
    }

    /**
     * @test
     * @return void
     */
    public function store_error_not_exists(): void
    {
        $this->post(route('job.store'), ['generate' => 'memo', 'memos' => [999999]])->assertSessionHasErrors(['memos.*']);
    }

    /**
     * @test
     * @return void
     */
    public function regenerate_error_not_your_job(): void
    {
        $apiJob = ApiJob::factory()->create();
        $memo = Memo::factory(['user_id' => $this->user->id])->create();
        $apiJob->memos()->attach($memo->id);
        $this->post(route('job.store'), ['regenerate' => $apiJob->id])->assertSessionHasErrors(['regenerate']);
    }

    /**
     * @test
     * @return void
     */
    public function regenerate_error_not_renegetable_status(): void
    {
        $apiJob = ApiJob::factory(['user_id' => $this->user->id, 'status' => ApiJobRepository::STATUS_SUCCESS])->create();
        $memo = Memo::factory(['user_id' => $this->user->id])->create();
        $apiJob->memos()->attach($memo->id);
        $this->post(route('job.store'), ['regenerate' => $apiJob->id])->assertSessionHasErrors(['regenerate']);
    }
}
