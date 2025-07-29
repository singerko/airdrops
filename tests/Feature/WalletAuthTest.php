<?php
// tests/Feature/WalletAuthTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Blockchain;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Blockchain::factory()->create(['slug' => 'ethereum']);
    }

    /** @test */
    public function can_get_nonce_for_wallet_authentication()
    {
        $response = $this->post('/auth/wallet/nonce', [
            'address' => '0x1234567890123456789012345678901234567890',
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure(['nonce', 'message']);
    }

    /** @test */
    public function can_connect_wallet_with_valid_signature()
    {
        // Get nonce first
        $nonceResponse = $this->post('/auth/wallet/nonce', [
            'address' => '0x1234567890123456789012345678901234567890',
        ]);
        
        $nonce = $nonceResponse->json('nonce');
        $message = $nonceResponse->json('message');

        // Mock a valid signature (in real test, you'd generate proper signature)
        $response = $this->post('/auth/wallet/connect', [
            'address' => '0x1234567890123456789012345678901234567890',
            'blockchain_slug' => 'ethereum',
            'wallet_type' => 'metamask',
            'signature' => 'valid_signature_mock_' . str_repeat('a', 100),
            'message' => $message,
        ]);

        $response->assertSuccessful();
        $this->assertAuthenticated();
    }

    /** @test */
    public function cannot_connect_wallet_with_invalid_signature()
    {
        $response = $this->post('/auth/wallet/connect', [
            'address' => '0x1234567890123456789012345678901234567890',
            'blockchain_slug' => 'ethereum',
            'wallet_type' => 'metamask',
            'signature' => 'invalid_signature',
            'message' => 'invalid_message',
        ]);

        $response->assertStatus(400);
        $this->assertGuest();
    }
}
