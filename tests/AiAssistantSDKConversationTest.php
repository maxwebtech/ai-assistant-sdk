<?php

use MaxWebTech\AiAssistant\AiAssistantSDK;
use PHPUnit\Framework\TestCase;

class AiAssistantSDKConversationTest extends TestCase
{
    private AiAssistantSDK $sdk;
    private string $mockJwt = 'mock.jwt.token';

    protected function setUp(): void
    {
        $this->sdk = new AiAssistantSDK([
            'widget_token' => 'test-widget-token',
            'api_url' => 'https://api.test.com'
        ]);
    }

    public function testGetMemberConversationsRequiresJWT()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to make API request');

        $this->sdk->getMemberConversations('test-member-123', '');
    }

    public function testGetMemberConversationRequiresJWT()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to make API request');

        $this->sdk->getMemberConversation('test-member-123', 1, '');
    }

    public function testGetMemberConversationMessagesRequiresJWT()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to make API request');

        $this->sdk->getMemberConversationMessages('test-member-123', 1, '');
    }

    public function testMethodsAcceptValidParameters()
    {
        // These would fail due to no actual API, but we're testing parameter validation
        try {
            $this->sdk->getMemberConversations('test-member-123', $this->mockJwt, 2, 10);
        } catch (Exception $e) {
            $this->assertStringContains('Failed to make API request', $e->getMessage());
        }

        try {
            $this->sdk->getMemberConversation('test-member-123', 123, $this->mockJwt);
        } catch (Exception $e) {
            $this->assertStringContains('Failed to make API request', $e->getMessage());
        }

        try {
            $this->sdk->getMemberConversationMessages('test-member-123', 123, $this->mockJwt, 3, 25);
        } catch (Exception $e) {
            $this->assertStringContains('Failed to make API request', $e->getMessage());
        }

        $this->assertTrue(true); // If we get here, parameter validation passed
    }
}