<?php

declare(strict_types=1);

namespace MaxWebTech\AiAssistant;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use Exception;

/**
 * AI Assistant PHP SDK
 * 
 * 簡化 AI Assistant 整合的 PHP SDK，支援 JWT 認證和會員限制
 * 
 * @package MaxWebTech\AiAssistant
 * @version 1.0.0
 * @author MaxWebTech Team
 * @license MIT
 */
class AiAssistantSDK
{
    private ?string $widgetToken;
    private ?string $iframeToken;
    private string $jwtSecret;
    private ?string $issuer;
    private string $apiUrl;

    /**
     * SDK 設定
     */
    public function __construct(array $config)
    {
        $this->widgetToken = $config['widget_token'] ?? null;
        $this->iframeToken = $config['iframe_token'] ?? null;
        $this->jwtSecret = $config['jwt_secret'] ?? throw new InvalidArgumentException('JWT secret is required');
        $this->issuer = $config['issuer'] ?? null;
        $this->apiUrl = $config['api_url'] ?? 'https://ai-assistant.com';
    }

    /**
     * 為用戶生成 JWT Token
     * 
     * @param array $user 用戶資料 ['id', 'name', 'email']
     * @param array $membership 會員設定 ['level', 'daily_conversation_limit', 'daily_message_limit', 'features']
     * @return string JWT token
     * @throws Exception
     */
    public function generateJWT(array $user, array $membership = []): string
    {
        if (!class_exists('Firebase\JWT\JWT')) {
            throw new Exception('Firebase JWT library is required. Run: composer require firebase/php-jwt');
        }

        $this->validateUserData($user);

        $now = time();
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->apiUrl,
            'sub' => $user['id'],
            'iat' => $now,
            'exp' => $now + 3600, // 1小時過期
            'jti' => uniqid('jwt_', true) . '_' . $now,
            
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? ''
            ],
            
            'membership' => array_merge([
                'level' => 'free',
                'daily_conversation_limit' => 10,
                'daily_message_limit' => 100,
                'features' => []
            ], $membership)
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    /**
     * 生成 Widget HTML 代碼
     * 
     * @param array $user 用戶資料
     * @param array $options 選項 ['membership', 'title', 'placeholder', 'theme', 'position']
     * @return string HTML 代碼
     * @throws Exception
     */
    public function getWidgetHTML(array $user, array $options = []): string
    {
        if (!$this->widgetToken) {
            throw new Exception('Widget token is required');
        }

        $jwt = $this->generateJWT($user, $options['membership'] ?? []);
        
        $attributes = [
            'data-ai-chat-token' => $this->widgetToken,
            'data-jwt' => $jwt
        ];

        // 可選屬性
        foreach (['title', 'placeholder', 'theme', 'position'] as $attr) {
            if (isset($options[$attr])) {
                $attributes["data-{$attr}"] = $options[$attr];
            }
        }

        $attributeString = $this->buildAttributeString($attributes);

        return sprintf(
            '<script src="%s/widget/ai-chat-widget.js"%s></script>',
            $this->apiUrl,
            $attributeString
        );
    }

    /**
     * 生成 iframe HTML 代碼
     * 
     * @param array $user 用戶資料
     * @param array $options 選項 ['membership', 'width', 'height', 'style', 'title', 'placeholder']
     * @return string HTML 代碼
     * @throws Exception
     */
    public function getIframeHTML(array $user, array $options = []): string
    {
        if (!$this->iframeToken) {
            throw new Exception('iframe token is required');
        }

        $jwt = $this->generateJWT($user, $options['membership'] ?? []);
        
        $params = http_build_query([
            'token' => $this->iframeToken,
            'jwt' => $jwt,
            'title' => $options['title'] ?? 'AI 助手',
            'placeholder' => $options['placeholder'] ?? '輸入您的訊息...'
        ]);

        $width = $options['width'] ?? '400';
        $height = $options['height'] ?? '600';
        $style = $options['style'] ?? 'border: none; border-radius: 12px;';

        return sprintf(
            '<iframe src="%s/widget-iframe?%s" width="%s" height="%s" style="%s"></iframe>',
            $this->apiUrl,
            $params,
            $width,
            $height,
            $style
        );
    }

    /**
     * 動態生成 Widget JavaScript
     * 
     * @param array $user 用戶資料
     * @param array $options 選項
     * @return string JavaScript 代碼
     * @throws Exception
     */
    public function getWidgetJS(array $user, array $options = []): string
    {
        if (!$this->widgetToken) {
            throw new Exception('Widget token is required');
        }

        $jwt = $this->generateJWT($user, $options['membership'] ?? []);
        
        $jsAttributes = $this->generateJSAttributes($options);

        return sprintf(
            "
            (function() {
                const script = document.createElement('script');
                script.src = '%s/widget/ai-chat-widget.js';
                script.setAttribute('data-ai-chat-token', '%s');
                script.setAttribute('data-jwt', '%s');
                %s
                document.body.appendChild(script);
            })();
            ",
            $this->apiUrl,
            $this->widgetToken,
            $jwt,
            $jsAttributes
        );
    }

    /**
     * 驗證 JWT Token
     * 
     * @param string $jwt JWT token
     * @return array 解析後的資料
     * @throws Exception
     */
    public function validateJWT(string $jwt): array
    {
        if (!class_exists('Firebase\JWT\JWT')) {
            throw new Exception('Firebase JWT library is required');
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception('JWT validation failed: ' . $e->getMessage());
        }
    }

    /**
     * 獲取會員等級的預設限制
     * 
     * @param string $level 會員等級
     * @return array 限制設定
     */
    public static function getDefaultLimits(string $level): array
    {
        $limits = [
            'guest' => ['daily_conversations' => 3, 'daily_messages' => 20],
            'free' => ['daily_conversations' => 10, 'daily_messages' => 100],
            'basic' => ['daily_conversations' => 30, 'daily_messages' => 300],
            'premium' => ['daily_conversations' => 100, 'daily_messages' => 1000],
            'enterprise' => ['daily_conversations' => -1, 'daily_messages' => -1]
        ];

        return $limits[$level] ?? $limits['free'];
    }

    /**
     * 驗證用戶資料
     */
    private function validateUserData(array $user): void
    {
        if (empty($user['id'])) {
            throw new InvalidArgumentException('User ID is required');
        }
    }

    /**
     * 建立 HTML 屬性字串
     */
    private function buildAttributeString(array $attributes): string
    {
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= sprintf(' %s="%s"', $key, htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'));
        }
        return $attributeString;
    }

    /**
     * 生成 JavaScript 屬性設定代碼
     */
    private function generateJSAttributes(array $options): string
    {
        $jsLines = [];
        
        foreach (['title', 'placeholder', 'theme', 'position'] as $attr) {
            if (isset($options[$attr])) {
                $jsLines[] = sprintf(
                    "script.setAttribute('data-%s', '%s');",
                    $attr,
                    addslashes((string)$options[$attr])
                );
            }
        }

        return implode("\n                ", $jsLines);
    }
}