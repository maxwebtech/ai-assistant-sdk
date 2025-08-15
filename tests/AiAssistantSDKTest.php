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

    public function testConstructorWorksWithoutJwtSecret(): void
    {
        $sdk = new AiAssistantSDK([
            'widget_token' => 'wt_test'
        ]);

        $this->assertInstanceOf(AiAssistantSDK::class, $sdk);
    }


    public function testGetWidgetHTML(): void
    {
        $html = $this->sdk->getWidgetHTML($this->testUser);

        $this->assertStringContainsString('<script', $html);
        $this->assertStringContainsString('ai-chat-widget.js', $html);
        $this->assertStringContainsString('data-ai-chat-token="wt_test_token"', $html);
        $this->assertStringContainsString('data-user-id="test_user_123"', $html);
    }

    public function testGetWidgetHTMLWithOptions(): void
    {
        $options = [
            'title' => 'Custom Title',
            'theme' => 'dark',
            'membership' => ['level' => 'premium']
        ];

        $html = $this->sdk->getWidgetHTML($this->testUser, $options);

        $this->assertStringContainsString('data-title="Custom Title"', $html);
        $this->assertStringContainsString('data-theme="dark"', $html);
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

        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringContainsString('widget-iframe', $html);
        $this->assertStringContainsString('token=if_test_token', $html);
        $this->assertStringContainsString('user_id=test_user_123', $html);
    }

    public function testGetIframeHTMLWithOptions(): void
    {
        $options = [
            'width' => '500',
            'height' => '700',
            'title' => 'Custom Chat'
        ];

        $html = $this->sdk->getIframeHTML($this->testUser, $options);

        $this->assertStringContainsString('width="500"', $html);
        $this->assertStringContainsString('height="700"', $html);
        $this->assertStringContainsString('title=Custom', $html);
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

        $this->assertStringContainsString('document.createElement(\'script\')', $js);
        $this->assertStringContainsString('ai-chat-widget.js', $js);
        $this->assertStringContainsString('data-ai-chat-token', $js);
        $this->assertStringContainsString('document.body.appendChild', $js);
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
        $this->assertStringContainsString('Chat &quot;Title&quot; &amp; More', $html);
        $this->assertStringNotContainsString('Chat "Title" & More', $html);
    }

}