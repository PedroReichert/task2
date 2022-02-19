<?php

    namespace Tests\Feature\Posts;

    use App\Models\Job;
    use App\Models\User;
    use App\Notifications\JobCreated;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Notification;
    use Laravel\Sanctum\Sanctum;
    use Tests\TestCase;

    class JobManagementTest extends TestCase
    {
        use RefreshDatabase;

        const MANAGER = 'user@manager.com';
        const USER1 = 'user1@regular.com';
        const USER2 = 'user2@regular.com';
        
        protected function setUp(): void
        {
            parent::setUp();

            $this->seed([
                'UserTableSeeder',
            ]);
        }

        /**
         * @test
         */
        public function api_is_accessible()
        {
            Sanctum::actingAs(User::where('email', self::USER1)->first());
            $this->json('get', 'api/jobs')
                ->assertStatus(200);
        }

        /**
         * @test
         */
        public function user_can_create_job()
        {
            Notification::fake();

            Sanctum::actingAs(User::where('email', self::USER1)->first());

            $data = [
                'title'   => 'Job Title',
                'description' => 'Alguma descrição sobre algum job',
            ];

            
            $this->json('post', 'api/jobs', $data)
                ->assertStatus(201)
                ->getContent();

            $this->assertDatabaseHas('jobs', $data);
            Notification::assertSentTo(
                [User::where('is_manager', 1)->get()],
                \App\Notifications\JobCreated::class
            );
        }

        /**
         * @test
         */
        public function user_can_update_a_job()
        {
            $user = User::where('email', self::USER1)->first();
            Sanctum::actingAs($user);

            $data = [
                'title' => 'Job Title Updated',
            ];
            
            $job = Job::factory()->for($user)->create();

            $content = $this->json('put', 'api/jobs/' . $job->id, $data)
                ->assertStatus(200)
                ->getContent();
        
            $this->assertDatabaseHas('jobs', $data);
        }

                
        /**
         * @test
         */
        public function user_can_read_a_job()
        {
            $user = User::where('email', self::USER1)->first();
            Sanctum::actingAs($user);
            
            $job = Job::factory()->for($user)->make();

            $this->json('get', 'api/jobs/' . $job->id)
                ->assertStatus(200);
        }


                /**
         * @test
         */
        public function user_can_not_read_another_user_job()
        {
            $user2 = User::where('email', self::USER2)->first();
            $job = Job::factory()->for($user2)->make();

            $user = User::where('email', self::USER1)->first();
            Sanctum::actingAs($user);
            
            $this->json('get', 'api/jobs/' . $job->id)
                ->assertJson(['data'=>[]]);
        }

        /**
         * @test
        */
        public function user_can_not_update_another_user_job()
        {
            $user2 = User::where('email', self::USER2)->first();
            $job = Job::factory()->for($user2)->create();

            $user = User::where('email', self::USER1)->first();
            Sanctum::actingAs($user);
            
            $data = [
                'title' => 'Job Title Updated',
            ];
            
            $content = $this->json('put', 'api/jobs/' . $job->id, $data)
                    ->assertStatus(404)
                    ->getContent();
        }


        /**
         * @test
         */
        public function manager_can_read_all_users_jobs()
        {
            $manager = User::where('email', self::MANAGER)->first();
            Sanctum::actingAs($manager);

            Job::factory()->count(5)->create();
            
            $this->json('get', 'api/jobs')
                ->assertJsonCount(Job::count());
        }
      
    }