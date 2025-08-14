<?php

declare(strict_types=1);

namespace MaxWebTech\AiAssistant\Tests;

use PHPUnit\Framework\TestCase;
use MaxWebTech\AiAssistant\AiAssistantSDK;
use InvalidArgumentException;
use Exception;

class AiAssistantSDKTest extends TestCase
{
    private AiAssistantSDK $sdk;
    private array $testUser;

    protected function setUp(): void
    {
        $this->sdk = new AiAssistantSDK([
            'widget_token' => 'wt_test_token',
            'iframe_token' => 'if_test_token',
            'jwt_secret' => 'test_secret_key',
            'issuer' => 'https://test-website.com'
        ]);

        $this->testUser = [
            'id' => 'test_user_123',
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];
    }

    public function testConstructorRequiresJwtSecret(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('JWT secret is required');

        new AiAssistantSDK([
            'widget_token' => 'wt_test'
        ]);
    }

    public function testGenerateJWT(): void
    {
        $jwt = $this->sdk->generateJWT($this->testUser);

        $this->assertIsString($jwt);
        $this->assertNotEmpty($jwt);
        
        // JWT 應該有三個部分（header.payload.signature）
        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts);
    }

    public function testGenerateJWTWithMembership(): void
    {
        $membership = [
            'level' => 'premium',
            'daily_conversation_limit' => 100,
            'features' => ['file_upload']
        ];

        $jwt = $this->sdk->generateJWT($this->testUser, $membership);
        
        $this->assertIsString($jwt);
        $this->assertNotEmpty($jwt);
    }

    public function testGenerateJWTRequiresUserId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User ID is required');

        $invalidUser = ['name' => 'Test User'];
        $this->sdk->generateJWT($invalidUser);
    }

    public function testGetWidgetHTML(): void
    {
        $html = $this->sdk->getWidgetHTML($this->testUser);

        $this->assertStringContains('<script', $html);
        $this->assertStringContains('ai-chat-widget.js', $html);
        $this->assertStringContains('data-ai-chat-token="wt_test_token"', $html);
        $this->assertStringContains('data-jwt=', $html);
    }

    public function testGetWidgetHTMLWithOptions(): void
    {
        $options = [
            'title' => 'Custom Title',
            'theme' => 'dark',
            'membership' => ['level' => 'premium']
        ];

        $html = $this->sdk->getWidgetHTML($this->testUser, $options);

        $this->assertStringContains('data-title="Custom Title"', $html);
        $this->assertStringContains('data-theme="dark"', $html);
    }

    public function testGetWidgetHTMLRequiresWidgetToken(): void
    {
        $sdk = new AiAssistantSDK([
            'jwt_secret' => 'test_secret'
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Widget token is required');

        $sdk->getWidgetHTML($this->testUser);
    }

    public function testGetIframeHTML(): void
    {
        $html = $this->sdk->getIframeHTML($this->testUser);

        $this->assertStringContains('<iframe', $html);
        $this->assertStringContains('widget-iframe', $html);
        $this->assertStringContains('token=if_test_token', $html);
        $this->assertStringContains('jwt=', $html);
    }

    public function testGetIframeHTMLWithOptions(): void
    {
        $options = [
            'width' => '500',
            'height' => '700',
            'title' => 'Custom Chat'
        ];

        $html = $this->sdk->getIframeHTML($this->testUser, $options);

        $this->assertStringContains('width="500"', $html);
        $this->assertStringContains('height="700"', $html);
        $this->assertStringContains('title=Custom%20Chat', $html);
    }

    public function testGetIframeHTMLRequiresIframeToken(): void
    {
        $sdk = new AiAssistantSDK([
            'widget_token' => 'wt_test',
            'jwt_secret' => 'test_secret'
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('iframe token is required');

        $sdk->getIframeHTML($this->testUser);
    }

    public function testGetWidgetJS(): void
    {
        $js = $this->sdk->getWidgetJS($this->testUser);

        $this->assertStringContains('document.createElement(\'script\')', $js);
        $this->assertStringContains('ai-chat-widget.js', $js);
        $this->assertStringContains('data-ai-chat-token', $js);
        $this->assertStringContains('document.body.appendChild', $js);
    }

    public function testGetDefaultLimits(): void
    {
        $limits = AiAssistantSDK::getDefaultLimits('premium');
        
        $this->assertIsArray($limits);
        $this->assertArrayHasKey('daily_conversations', $limits);
        $this->assertArrayHasKey('daily_messages', $limits);
        $this->assertEquals(100, $limits['daily_conversations']);
        $this->assertEquals(1000, $limits['daily_messages']);
    }

    public function testGetDefaultLimitsForUnknownLevel(): void
    {
        $limits = AiAssistantSDK::getDefaultLimits('unknown');
        
        // 應該返回 free 等級的限制
        $this->assertEquals(10, $limits['daily_conversations']);
        $this->assertEquals(100, $limits['daily_messages']);
    }

    public function testGetDefaultLimitsForAllLevels(): void
    {
        $levels = ['guest', 'free', 'basic', 'premium', 'enterprise'];
        
        foreach ($levels as $level) {
            $limits = AiAssistantSDK::getDefaultLimits($level);
            $this->assertIsArray($limits);
            $this->assertArrayHasKey('daily_conversations', $limits);
            $this->assertArrayHasKey('daily_messages', $limits);
        }
    }

    public function testHTMLEscaping(): void
    {
        $userWithSpecialChars = [
            'id' => 'test_123',
            'name' => 'Test "User" & Co',
            'email' => 'test@example.com'
        ];

        $options = [
            'title' => 'Chat "Title" & More'
        ];

        $html = $this->sdk->getWidgetHTML($userWithSpecialChars, $options);

        // 檢查特殊字符是否被正確轉義
        $this->assertStringContains('Chat &quot;Title&quot; &amp; More', $html);
        $this->assertStringNotContains('Chat "Title" & More', $html);
    }

    public function testGetUserUsageStatusRequiresUserId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User ID is required');

        $this->sdk->getUserUsageStatus('');
    }

    public function testGetUserUsageStatusRequiresToken(): void
    {
        $sdk = new AiAssistantSDK([
            'api_url' => 'http://localhost:8000'
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Authentication token is required');

        $sdk->getUserUsageStatus('test_user_123');
    }

    public function testGetUserUsageStatusFormatsResponse(): void
    {
        // 這個測試需要 mock API 回應
        // 在實際環境中，你可能需要使用 PHPUnit 的 mock 功能或測試 HTTP client
        
        $sdk = new AiAssistantSDK([
            'widget_token' => 'wt_test_token',
            'api_url' => 'http://localhost:8000'
        ]);

        // 測試方法的格式化功能
        $reflection = new \ReflectionClass($sdk);
        $method = $reflection->getMethod('formatUsageResponse');
        $method->setAccessible(true);

        $mockApiResponse = [
            'user_id' => 'test_123',
            'membership_level' => 'premium',
            'usage' => [
                'daily_conversations' => [
                    'used' => 5,
                    'limit' => 100,
                    'remaining' => 95,
                    'unlimited' => false
                ],
                'daily_messages' => [
                    'used' => 50,
                    'limit' => 1000,
                    'remaining' => 950,
                    'unlimited' => false
                ]
            ],
            'reset_time' => '2024-01-01T00:00:00Z',
            'features' => ['file_upload', 'voice_chat']
        ];

        $formattedResponse = $method->invoke($sdk, $mockApiResponse);

        $this->assertEquals('test_123', $formattedResponse['user_id']);
        $this->assertEquals('premium', $formattedResponse['membership_level']);
        $this->assertEquals(5, $formattedResponse['usage']['daily_conversations']['used']);
        $this->assertEquals(95, $formattedResponse['usage']['daily_conversations']['remaining']);
        $this->assertEquals(950, $formattedResponse['usage']['daily_messages']['remaining']);
        $this->assertContains('file_upload', $formattedResponse['features']);
    }

    public function testGetUserUsageStatusHandlesUnlimitedPlan(): void
    {
        $sdk = new AiAssistantSDK([
            'widget_token' => 'wt_test_token',
            'api_url' => 'http://localhost:8000'
        ]);

        $reflection = new \ReflectionClass($sdk);
        $method = $reflection->getMethod('formatUsageResponse');
        $method->setAccessible(true);

        $mockApiResponse = [
            'user_id' => 'enterprise_user',
            'membership_level' => 'enterprise',
            'usage' => [
                'daily_conversations' => [
                    'used' => 500,
                    'limit' => -1,
                    'remaining' => -1,
                    'unlimited' => true
                ],
                'daily_messages' => [
                    'used' => 5000,
                    'limit' => -1,
                    'remaining' => -1,
                    'unlimited' => true
                ]
            ]
        ];

        $formattedResponse = $method->invoke($sdk, $mockApiResponse);

        $this->assertTrue($formattedResponse['usage']['daily_conversations']['unlimited']);
        $this->assertTrue($formattedResponse['usage']['daily_messages']['unlimited']);
        $this->assertEquals(-1, $formattedResponse['usage']['daily_conversations']['limit']);
    }
}