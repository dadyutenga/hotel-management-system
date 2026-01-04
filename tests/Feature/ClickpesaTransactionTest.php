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

        // Mock URL generation for the callback route
        \Illuminate\Support\Facades\URL::shouldReceive('route')
            ->with('tenant.payments.clickpesa.callback', \Mockery::any(), \Mockery::any())
            ->andReturn('http://localhost/payments/clickpesa/callback');

        // Create Request
        $request = \Illuminate\Http\Request::create('/payments/clickpesa/initiate', 'POST', [
            'amount' => 5000,
            'msisdn' => '255712345678',
            'provider' => 'TIGO',
            'reference' => 'TEST-REF-001',
        ]);

        // Mock Service (already done above, but we need to pass it to controller)
        /** @var \App\Services\ClickpesaService $service */
        $service = $this->app->make(ClickpesaService::class);

        // Instantiate Controller
        $controller = new \App\Http\Controllers\Tenant\Payment\ClickpesaPaymentController($service);

        // Call method
        $response = $controller->initiate($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Test-ClickPesa-ID', $content['data']['clickpesa_id']);

        $this->assertDatabaseHas('clickpesa_transactions', [
            'reference' => 'TEST-REF-001',
            'amount' => 5000,
            'transaction_id' => 'Test-ClickPesa-ID',
        ]);
    }
}
