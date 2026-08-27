<?php

namespace Tests\Feature;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LandingMessageStatisticsCacheTest extends TestCase
{
    private const CACHE_KEY = 'landing.message-statistics.v1';

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget(self::CACHE_KEY);
    }

    protected function tearDown(): void
    {
        Cache::forget(self::CACHE_KEY);

        parent::tearDown();
    }

    public function test_repeated_public_requests_reuse_cached_message_statistics(): void
    {
        $messageQueryCount = 0;

        DB::listen(
            static function (QueryExecuted $query) use (&$messageQueryCount): void {
                if (str_contains(strtolower($query->sql), 'messages')) {
                    $messageQueryCount++;
                }
            }
        );

        $this->get('/')->assertOk();

        $this->assertSame(3, $messageQueryCount);

        $messageQueryCount = 0;

        $this->get('/')->assertOk();

        $this->assertSame(
            0,
            $messageQueryCount,
            'The second landing-page request queried the messages table instead of using the cache.'
        );
    }
}
