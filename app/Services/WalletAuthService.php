<?php
// app/Services/WalletAuthService.php (upravený bez Redis)

namespace App\Services;

use App\Models\WalletNonce;
use Illuminate\Support\Str;

class WalletAuthService
{
    public function generateNonce(string $address): string
    {
        $nonce = Str::random(32);
        
        // Clean up expired nonces
        WalletNonce::where('expires_at', '<', now())->delete();
        
        // Store nonce in database for 10 minutes
        WalletNonce::create([
            'address' => $address,
            'nonce' => $nonce,
            'expires_at' => now()->addMinutes(10),
        ]);
        
        return $nonce;
    }

    public function generateMessage(string $address, string $nonce): string
    {
        $domain = config('app.url');
        $timestamp = now()->toISOString();
        
        return "Welcome to AirdropPortal!\n\n" .
               "Please sign this message to authenticate your wallet.\n\n" .
               "Domain: {$domain}\n" .
               "Address: {$address}\n" .
               "Nonce: {$nonce}\n" .
               "Timestamp: {$timestamp}";
    }

    public function verifySignature(string $address, string $message, string $signature, string $blockchain): bool
    {
        // Extract nonce from message
        preg_match('/Nonce: ([a-zA-Z0-9]+)/', $message, $matches);
        if (!isset($matches[1])) {
            return false;
        }
        
        $nonce = $matches[1];
        
        // Check if nonce is valid and not expired
        $walletNonce = WalletNonce::where('address', $address)
            ->where('nonce', $nonce)
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$walletNonce) {
            return false;
        }
        
        // Clean up used nonce
        $walletNonce->delete();
        
        // Verify signature based on blockchain
        switch ($blockchain) {
            case 'ethereum':
            case 'polygon':
            case 'bsc':
            case 'arbitrum':
                return $this->verifyEthereumSignature($address, $message, $signature);
            
            case 'solana':
                return $this->verifySolanaSignature($address, $message, $signature);
            
            case 'cosmos':
                return $this->verifyCosmosSignature($address, $message, $signature);
            
            default:
                return false;
        }
    }

    private function verifyEthereumSignature(string $address, string $message, string $signature): bool
    {
        try {
            // This is a simplified verification
            // In production, you would use a proper library like web3.php
            $messageHash = "\x19Ethereum Signed Message:\n" . strlen($message) . $message;
            $hash = hash('sha3-256', $messageHash, true);
            
            // For demo purposes, we'll assume verification passes
            // In real implementation, use proper ECDSA verification
            return strlen($signature) > 100; // Basic validation
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verifySolanaSignature(string $address, string $message, string $signature): bool
    {
        try {
            // Solana signature verification
            // In production, use @solana/web3.js or equivalent PHP library
            return strlen($signature) > 80; // Basic validation
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verifyCosmosSignature(string $address, string $message, string $signature): bool
    {
        try {
            // Cosmos signature verification
            // In production, use CosmJS or equivalent PHP library
            return strlen($signature) > 80; // Basic validation
        } catch (\Exception $e) {
            return false;
        }
    }

    public function cleanupExpiredNonces(): void
    {
        WalletNonce::where('expires_at', '<', now())->delete();
    }
}
