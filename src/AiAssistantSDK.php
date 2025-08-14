<?php

declare(strict_types=1);

namespace MaxWebTech\AiAssistant;

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
    private string $apiUrl;

    /**
     * SDK 設定
     */
    public function __construct(array $config)
    {
        $this->widgetToken = $config['widget_token'] ?? null;
        $this->iframeToken = $config['iframe_token'] ?? null;
        $this->apiUrl = $config['api_url'] ?? 'http://localhost:8000';
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

        $this->validateUserData($user);
        
        $attributes = [
            'data-ai-chat-token' => $this->widgetToken,
            'data-user-id' => $user['id'],
            'data-user-name' => $user['name'] ?? '',
            'data-user-email' => $user['email'] ?? ''
        ];

        // 會員資料
        if (isset($options['membership'])) {
            foreach ($options['membership'] as $key => $value) {
                $attributes["data-membership-{$key}"] = is_array($value) ? json_encode($value) : $value;
            }
        }

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

        $this->validateUserData($user);
        
        $params = [
            'token' => $this->iframeToken,
            'user_id' => $user['id'],
            'user_name' => $user['name'] ?? '',
            'user_email' => $user['email'] ?? '',
            'title' => $options['title'] ?? 'AI 助手',
            'placeholder' => $options['placeholder'] ?? '輸入您的訊息...'
        ];

        // 添加會員資料到參數
        if (isset($options['membership'])) {
            foreach ($options['membership'] as $key => $value) {
                $params["membership_{$key}"] = is_array($value) ? json_encode($value) : $value;
            }
        }

        $queryString = http_build_query($params);
        $width = $options['width'] ?? '400';
        $height = $options['height'] ?? '600';
        $style = $options['style'] ?? 'border: none; border-radius: 12px;';

        return sprintf(
            '<iframe src="%s/widget-iframe?%s" width="%s" height="%s" style="%s"></iframe>',
            $this->apiUrl,
            $queryString,
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

        $this->validateUserData($user);
        
        $jsAttributes = $this->generateJSAttributes($user, $options);

        return sprintf(
            "
            (function() {
                const script = document.createElement('script');
                script.src = '%s/widget/ai-chat-widget.js';
                script.setAttribute('data-ai-chat-token', '%s');
                script.setAttribute('data-user-id', '%s');
                script.setAttribute('data-user-name', '%s');
                script.setAttribute('data-user-email', '%s');
                %s
                document.body.appendChild(script);
            })();
            ",
            $this->apiUrl,
            $this->widgetToken,
            addslashes($user['id']),
            addslashes($user['name'] ?? ''),
            addslashes($user['email'] ?? ''),
            $jsAttributes
        );
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
    private function generateJSAttributes(array $user, array $options): string
    {
        $jsLines = [];
        
        // 會員資料屬性
        if (isset($options['membership'])) {
            foreach ($options['membership'] as $key => $value) {
                $jsLines[] = sprintf(
                    "script.setAttribute('data-membership-%s', '%s');",
                    $key,
                    addslashes(is_array($value) ? json_encode($value) : (string)$value)
                );
            }
        }
        
        // 其他可選屬性
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