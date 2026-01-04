<?php

namespace Tests\Feature;

use App\Models\ClickpesaTransaction;
use App\Models\User;
use App\Models\Tenant;
use App\Services\ClickpesaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class ClickpesaTransactionTest extends TestCase
{
    // use RefreshDatabase; // Commented out to avoid wiping user's dev db if not configured for testing

    public function test_initiate_payment_successfully()
    {
        // Mock the service
        $this->mock(ClickpesaService::class, function (MockInterface $mock) {
            $mock->shouldReceive('initiatePayment')
                ->once()
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'transaction_id' => 'Test-ClickPesa-ID',
                        'status' => 'pending'
                    ]
                ]);
        });

        // Create a dummy user/tenant if needed, or bypass auth for this test depending on middleware
        // Assuming we mock the auth middleware or user logic for simplicity in this specific test
        // actually the route is protected by 'auth' from tenant.php group. 
        // We will mock actingAs a user.

        $user = User::factory()->create(); // Ensure factories exist and work, or just make a simple user

        $response = $this->actingAs($user)
            ->postJson(route('tenant.payments.clickpesa.initiate'), [
                'amount' => 5000,
                'msisdn' => '255712345678',
                'provider' => 'TIGO',
                'reference' => 'TEST-REF-001',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'clickpesa_id' => 'Test-ClickPesa-ID',
                    'status' => 'pending',
                ]
            ]);

        $this->assertDatabaseHas('clickpesa_transactions', [
            'reference' => 'TEST-REF-001',
            'amount' => 5000,
            'transaction_id' => 'Test-ClickPesa-ID',
        ]);
    }
}
