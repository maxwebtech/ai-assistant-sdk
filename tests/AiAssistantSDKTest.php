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
}