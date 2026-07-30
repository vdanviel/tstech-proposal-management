<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaginationFilterSearchTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function testItCoversAllPaginationFilterAndSearchScenarios(): void
    {

        $user = User::factory()->create();

        //quando não há page ou perpage...
        $responseValidation = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/propostas');

        $responseValidation->assertStatus(422)
        ->assertJsonFragment(
            [
                "error" =>
                [
                    'page' => ['O campo página é obrigatório.'],
                    'perPage' => ['O campo itens por página é obrigatório.']
                ]
            ]
        );

        $proposals = Proposal::factory()->count(15)->create();

        //criar algumas propostas com valores especificos...
        Proposal::factory()->create([
            'monthly_value' => 1000
        ]);

        //quando ha filtro pagino page e perpage...
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=10')
            ->assertStatus(200)
            ->assertJsonCount(10, 'data');


        //testa o filtro por produto
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=1&product=' . $proposals->random()->product)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        //testa filtro de status
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=15&status=DRAFT')
            ->assertStatus(200)
            ->assertJsonPath('data.0.status', 'DRAFT');

        //testa filtro de origem
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=15&origin=SITE')
            ->assertStatus(200)
            ->assertJsonPath('data.0.origin', 'SITE');

        //testa filtro de id do cliente
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=1&clientId=' . $proposals->random()->client_id)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        //testa filtro de grandeza > de valor ao mes
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=10&monthlyOp=%3E&monthlyValue=1000')
            ->assertStatus(200)
            ->assertJson(function ($json) {

                $json->where('data.0.monthly_value', function ($value) {
                    return is_numeric($value) && $value > 1000;
                }

            )->etc();
        });

        //testa filtro de grandeza < de valor ao mes
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=10&monthlyOp=%3C&monthlyValue=1000')
            ->assertStatus(200)
            ->assertJson(function ($json) {

                $json->where('data.0.monthly_value', function ($value) {
                    return is_numeric($value) && $value < 1000;
                }

            )->etc();
        });

        //testa filtro de grandeza = de valor ao mes
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=10&monthlyOp=%3D&monthlyValue=1000')
            ->assertStatus(200)
            ->assertJson(function ($json) {

                $json->where('data.0.monthly_value', function ($value) {
                    return is_numeric($value) && $value == 1000;
                }

            )->etc();
        });

        //testa filtro de grandeza >= de valor ao mes
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=10&monthlyOp=%3E%3D&monthlyValue=1000')
            ->assertStatus(200)
            ->assertJson(function ($json) {

                $json->where('data.0.monthly_value', function ($value) {
                    return is_numeric($value) && $value >= 1000;
                }

            )->etc();
        });

        //testa filtro de grandeza <= de valor ao mes
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/propostas?page=1&perPage=10&monthlyOp=%3C%3D&monthlyValue=1000')
            ->assertStatus(200)
            ->assertJson(function ($json) {

                $json->where('data.0.monthly_value', function ($value) {
                    return is_numeric($value) && $value <= 1000;
                }

            )->etc();
        });
    }

}
