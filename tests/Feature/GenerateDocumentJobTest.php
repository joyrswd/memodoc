<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Memo;
use App\Models\ApiJob;
use App\Repositories\ApiJobRepository;
use App\Jobs\GenerateDocumentJob;
use App\Interfaces\AiApiServiceInterface;
use App\Services\DocumentService;
use App\Services\ApiJobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerateDocumentJobTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private ApiJob $apiJob;
    private AiApiServiceInterface $apiService;
    private DocumentService $documentService;
    private ApiJobService $jobService;

    public const apiKeyName = 'expected key';

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->memo = Memo::factory(['user_id' => $this->user->id])->create();
        $this->apiJob = ApiJob::factory(['user_id' => $this->user->id, 'status' => ApiJobRepository::STATUS_STARTED])->create();
        $this->apiJob->memos()->attach($this->memo->id);
        $this->documentService = app(DocumentService::class);
        $this->jobService = app(ApiJobService::class);

        $this->apiService = $this->mock(AiApiServiceInterface::class);
        $this->apiService->shouldReceive('getKey')->andReturn(self::apiKeyName);
        Queue::fake();
    }

    private function handle()
    {
        $job = new GenerateDocumentJob($this->user->id, $this->apiJob->id);
        $job->handle($this->apiService, $this->documentService, $this->jobService);
    }

    /**
     * @test
     */
    public function job_pushed()
    {
        GenerateDocumentJob::dispatch($this->user->id, $this->apiJob->id);
        Queue::assertPushed(GenerateDocumentJob::class, function ($job) {
            return $job->userId === $this->user->id
                && $job->jobId === $this->apiJob->id;
        });
    }

    /**
     * @test
     */
    public function success()
    {
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(10);
        $this->apiService->shouldReceive('sendRequest')->once()->andReturnUsing(function () {
            $this->assertDatabaseHas('api_jobs', [
                'id' => $this->apiJob->id,
                'status' => ApiJobRepository::STATUS_PROCESSING,
                'api_name' => self::apiKeyName,
                'response' => null,
            ]);
            return ['result' => 'success'];
        });
        $this->apiService->shouldReceive('isError')->once()->andReturn(false);
        $this->apiService->shouldReceive('getTitle')->once()->andReturn('expected title');
        $this->apiService->shouldReceive('getContent')->once()->andReturn('expected content')->andReturnUsing(function () {
            $this->assertDatabaseHas('api_jobs', [
                'id' => $this->apiJob->id,
                'status' => ApiJobRepository::STATUS_PROCESSED,
                'api_name' => self::apiKeyName,
                'response' => '{"result":"success"}',
            ]);
            return 'expected content';
        });
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_SUCCESS,
            'api_name' => self::apiKeyName,
            'response' => '{"result":"success"}',
        ]);
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'api_job_id' => $this->apiJob->id,
            'title' => 'expected title',
            'content' => 'expected content',
        ]);
    }

    /**
     * @test
     */
    public function title_empty()
    {
        $title = '';
        $cotent = 'expected content';
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(10);
        $this->apiService->shouldReceive('sendRequest')->once()->andReturn(['result' => 'success']);
        $this->apiService->shouldReceive('isError')->once()->andReturn(false);
        $this->apiService->shouldReceive('getTitle')->once()->andReturn($title);
        $this->apiService->shouldReceive('getContent')->once()->andReturn($cotent);
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_SUCCESS,
            'api_name' => self::apiKeyName,
            'response' => '{"result":"success"}',
        ]);
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'api_job_id' => $this->apiJob->id,
            'title' => '',
            'content' => $cotent,
        ]);
    }


    /**
     * @test
     */
    public function title_too_long()
    {
        $title = str_repeat('a', 256);
        $cotent = 'expected content';
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(10);
        $this->apiService->shouldReceive('sendRequest')->once()->andReturn(['result' => 'success']);
        $this->apiService->shouldReceive('isError')->once()->andReturn(false);
        $this->apiService->shouldReceive('getTitle')->once()->andReturn($title);
        $this->apiService->shouldReceive('getContent')->once()->andReturn($cotent);
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_SUCCESS,
            'api_name' => self::apiKeyName,
            'response' => '{"result":"success"}',
        ]);
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'api_job_id' => $this->apiJob->id,
            'title' => '',
            'content' => $title . "\n" . $cotent,
        ]);
    }

    /**
     * @test
     */
    public function content_empty()
    {
        $title = 'expected title';
        $cotent = '';
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(10);
        $this->apiService->shouldReceive('sendRequest')->once()->andReturn(['result' => 'success']);
        $this->apiService->shouldReceive('isError')->once()->andReturn(false);
        $this->apiService->shouldReceive('getTitle')->once()->andReturn($title);
        $this->apiService->shouldReceive('getContent')->once()->andReturn($cotent);
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_SUCCESS,
            'api_name' => self::apiKeyName,
            'response' => '{"result":"success"}',
        ]);
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'api_job_id' => $this->apiJob->id,
            'title' => $title,
            'content' => $title,
        ]);
    }



    /**
     * @test
     */
    public function error_deleted()
    {
        $this->apiJob->delete();
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_ABORTED,
            'api_name' => null,
            'response' => '',
            'error_message' => 'ジョブが削除されているため、処理を中断しました。',
        ]);
    }

    /**
     * @test
     */
    public function error_over_limit()
    {
        ApiJob::factory(['user_id' => $this->user->id, 'status' => ApiJobRepository::STATUS_SUCCESS, 'started_at' => now()])->create();
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(1);
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_ABORTED,
            'api_name' => null,
            'response' => '',
            'error_message' => 'リクエスト回数上限に達したため、処理を中断しました。',
        ]);
    }

    /**
     * @test
     */
    public function error_api_exception()
    {
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(10);
        $this->apiService->shouldReceive('sendRequest')->once()->andThrow(new \Exception('An error occurred'));
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_ABORTED,
            'api_name' => self::apiKeyName,
            'response' => 'An error occurred',
            'error_message' => 'エラーが発生したため中断されました。',
        ]);
    }

    /**
     * @test
     */
    public function error_api_error()
    {
        $this->apiService->shouldReceive('getDailyLimit')->andReturn(10);
        $this->apiService->shouldReceive('sendRequest')->once()->andReturn(['result' => 'error']);
        $this->apiService->shouldReceive('isError')->once()->andReturn(true);
        $this->handle();
        $this->assertDatabaseHas('api_jobs', [
            'id' => $this->apiJob->id,
            'status' => ApiJobRepository::STATUS_ERROR,
            'api_name' => self::apiKeyName,
            'response' => '{"result":"error"}',
            'error_message' => 'APIのリクエストに失敗しました',
        ]);
    }

}
