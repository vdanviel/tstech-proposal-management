<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;

class IdempotencySavingTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function testIdempotencyKey(): void
    {
        $uniqueKey = Str::uuid();

        $payload = [
            "name" => "temp",
            "email" => "temp@temp.com",
            "document" => "111.111.111-11"
        ];

        $headers = [
            "Idempotency-Key" => $uniqueKey,
        ];

        $firstResponse = $this->postJson('/api/v1/clientes', $payload, $headers);
        $this->postJson('/api/v1/clientes', $payload, $headers);

        $count = Client::where('email', $firstResponse->json('email'))->count();

        $this->assertEquals(1, $count);

        Client::find($firstResponse->json('id'))->delete();

}



}
