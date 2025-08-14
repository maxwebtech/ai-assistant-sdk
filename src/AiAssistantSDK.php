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
    private ?string $jwtSecret;

    /**
     * SDK 設定
     */
    public function __construct(array $config)
    {
        $this->widgetToken = $config['widget_token'] ?? null;
        $this->iframeToken = $config['iframe_token'] ?? null;
        $this->apiUrl = $config['api_url'] ?? 'https://kitten-flowing-donkey.ngrok-free.app';
        $this->jwtSecret = $config['jwt_secret'] ?? null;
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
     * 獲取使用者的使用狀況和剩餘限制
     * 
     * @param string $userId 使用者 ID
     * @param string|null $authToken 驗證 token (widget_token 或 iframe_token)
     * @return array 包含使用狀況和剩餘限制的陣列
     * @throws Exception 當 API 請求失敗時
     */
    public function getUserUsageStatus(string $userId, ?string $authToken = null): array
    {
        if (empty($userId)) {
            throw new InvalidArgumentException('User ID is required');
        }

        // 使用提供的 token 或預設使用 widget_token
        $token = $authToken ?? $this->widgetToken ?? $this->iframeToken;
        
        if (!$token) {
            throw new Exception('Authentication token is required');
        }

        $apiEndpoint = $this->apiUrl . '/api/v1/usage-status';
        
        // 準備請求參數
        $params = [
            'user_id' => $userId,
            'token' => $token
        ];

        // 發送 API 請求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('API request failed: ' . $error);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = $errorData['message'] ?? 'Unknown error';
            throw new Exception('API error (HTTP ' . $httpCode . '): ' . $errorMessage);
        }

        $data = json_decode($response, true);
        
        if (!$data) {
            throw new Exception('Invalid API response');
        }

        return $this->formatUsageResponse($data);
    }

    /**
     * 格式化使用狀況回應
     * 
     * @param array $data API 回應資料
     * @return array 格式化的使用狀況資料
     */
    private function formatUsageResponse(array $data): array
    {
        return [
            'user_id' => $data['user_id'] ?? '',
            'membership_level' => $data['membership_level'] ?? 'free',
            'usage' => [
                'daily_conversations' => [
                    'used' => $data['usage']['daily_conversations']['used'] ?? 0,
                    'limit' => $data['usage']['daily_conversations']['limit'] ?? 0,
                    'remaining' => $data['usage']['daily_conversations']['remaining'] ?? 0,
                    'unlimited' => $data['usage']['daily_conversations']['unlimited'] ?? false
                ],
                'daily_messages' => [
                    'used' => $data['usage']['daily_messages']['used'] ?? 0,
                    'limit' => $data['usage']['daily_messages']['limit'] ?? 0,
                    'remaining' => $data['usage']['daily_messages']['remaining'] ?? 0,
                    'unlimited' => $data['usage']['daily_messages']['unlimited'] ?? false
                ]
            ],
            'reset_time' => $data['reset_time'] ?? null,
            'features' => $data['features'] ?? []
        ];
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

    /**
     * 獲取租戶的所有會員等級
     * 
     * @return array 會員等級清單
     * @throws Exception
     */
    public function getMembershipTiers(int $tenantId): array
    {
        if (!$this->jwtSecret) {
            throw new Exception('JWT secret is required for membership tier operations');
        }

        $jwt = $this->generateJWT([
            'tenant_id' => $tenantId,
            'action' => 'get_membership_tiers'
        ]);

        $response = $this->makeApiRequestWithJWT('GET', '/api/membership-tiers', [], $jwt);
        return $response;
    }

    /**
     * 獲取特定會員等級資訊
     * 
     * @param string $slug 等級標識
     * @return array 會員等級資訊
     * @throws Exception
     */
    public function getMembershipTier(string $slug): array
    {
        if (!$this->widgetToken) {
            throw new Exception('Widget token is required for membership tier operations');
        }

        $response = $this->makeApiRequest('GET', "/api/tenant/membership-tiers/{$slug}");
        return $response;
    }

    /**
     * 檢查用戶使用額度
     * 
     * @param string $userId 用戶ID
     * @param string|null $sessionId 會話ID（匿名用戶）
     * @return array 額度使用狀況
     * @throws Exception
     */
    public function checkUserQuota(string $userId, ?string $sessionId = null): array
    {
        if (!$this->widgetToken) {
            throw new Exception('Widget token is required for quota operations');
        }

        $params = ['user_id' => $userId];
        if ($sessionId) {
            $params['session_id'] = $sessionId;
        }

        $response = $this->makeApiRequest('GET', '/api/tenant/quota/check', $params);
        return $response;
    }

    /**
     * 分配會員等級給用戶
     * 
     * @param string $userId 用戶ID
     * @param string $tierSlug 等級標識
     * @return array 操作結果
     * @throws Exception
     */
    public function assignMembershipTier(string $userId, string $tierSlug): array
    {
        if (!$this->widgetToken) {
            throw new Exception('Widget token is required for membership operations');
        }

        $data = [
            'user_id' => $userId,
            'tier_slug' => $tierSlug
        ];

        $response = $this->makeApiRequest('POST', '/api/tenant/membership/assign', $data);
        return $response;
    }

    /**
     * 重置用戶每日使用量
     * 
     * @param string $userId 用戶ID
     * @param string|null $sessionId 會話ID（匿名用戶）
     * @return array 操作結果
     * @throws Exception
     */
    public function resetUserQuota(string $userId, ?string $sessionId = null): array
    {
        if (!$this->widgetToken) {
            throw new Exception('Widget token is required for quota operations');
        }

        $data = ['user_id' => $userId];
        if ($sessionId) {
            $data['session_id'] = $sessionId;
        }

        $response = $this->makeApiRequest('POST', '/api/tenant/quota/reset', $data);
        return $response;
    }

    /**
     * 執行 API 請求
     * 
     * @param string $method HTTP 方法
     * @param string $endpoint API 端點
     * @param array $data 請求資料
     * @return array API 回應
     * @throws Exception
     */
    private function makeApiRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = rtrim($this->apiUrl, '/') . $endpoint;
        
        // 使用 Widget Token 進行認證（和其他 Widget API 一樣）
        $options = [
            'http' => [
                'method' => $method,
                'header' => [
                    'Authorization: Bearer ' . $this->widgetToken,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'ignore_errors' => true
            ]
        ];

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        } elseif ($method !== 'GET' && !empty($data)) {
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to make API request');
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from API');
        }

        // 檢查 HTTP 狀態碼
        if (isset($http_response_header[0])) {
            $statusCode = (int) substr($http_response_header[0], 9, 3);
            if ($statusCode >= 400) {
                $errorMessage = $decodedResponse['message'] ?? 'API request failed';
                throw new Exception("API Error ({$statusCode}): {$errorMessage}");
            }
        }

        return $decodedResponse;
    }

    /**
     * 生成 JWT Token
     * 
     * @param array $payload JWT payload
     * @return string JWT token
     * @throws Exception
     */
    private function generateJWT(array $payload): string
    {
        if (!$this->jwtSecret) {
            throw new Exception('JWT secret is required');
        }

        // 檢查是否安裝 Firebase JWT
        if (!class_exists('Firebase\\JWT\\JWT')) {
            throw new Exception('Firebase JWT library is required. Run: composer require firebase/php-jwt');
        }

        $payload = array_merge([
            'iss' => 'ai-assistant-sdk',
            'iat' => time(),
            'exp' => time() + 3600, // 1小時過期
            'jti' => bin2hex(random_bytes(16)), // 唯一ID防止重放攻擊
        ], $payload);

        return \Firebase\JWT\JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    /**
     * 使用 JWT 執行 API 請求
     * 
     * @param string $method HTTP 方法
     * @param string $endpoint API 端點
     * @param array $data 請求資料
     * @param string $jwt JWT token
     * @return array API 回應
     * @throws Exception
     */
    private function makeApiRequestWithJWT(string $method, string $endpoint, array $data = [], string $jwt = ''): array
    {
        $url = rtrim($this->apiUrl, '/') . $endpoint;
        
        $options = [
            'http' => [
                'method' => $method,
                'header' => [
                    'Authorization: Bearer ' . $jwt,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'ignore_errors' => true
            ]
        ];

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        } elseif ($method !== 'GET' && !empty($data)) {
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to make API request');
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from API');
        }

        // 檢查 HTTP 狀態碼
        if (isset($http_response_header[0])) {
            $statusCode = (int) substr($http_response_header[0], 9, 3);
            if ($statusCode >= 400) {
                $errorMessage = $decodedResponse['message'] ?? 'API request failed';
                throw new Exception("API Error ({$statusCode}): {$errorMessage}");
            }
        }

        return $decodedResponse;
    }

}