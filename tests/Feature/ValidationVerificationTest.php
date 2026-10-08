<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registration with invalid values to ensure full-stack Arabic validation executes correctly.
     */
    public function test_registration_validation_fails_with_strict_rules()
    {
        // 1. Sending bad inputs that should violate our Regex and Rules
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John 123', // Contains numbers (Should fail)
            'phone' => '055123456', // Not a Yemeni key (Should fail)
            'email' => 'notanemail', // Invalid email (Should fail)
            'password' => '12345', // Weak password (Should fail)
            'password_confirmation' => '54321', // Mismatch
        ]);

        // 2. Expecting 422 Unprocessable Entity
        $response->assertStatus(422);

        // 3. Ensuring response structure matches exact requirements
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'name',
                'phone',
                'email',
                'password',
            ]
        ]);

        ob_start();
        var_dump($response->json());
        $output = ob_get_clean();
        
        $this->assertStringContainsString('يرجى التحقق من المدخلات', $response->json('message'));
        $this->assertNotEmpty($response->json('errors'));
    }
}
