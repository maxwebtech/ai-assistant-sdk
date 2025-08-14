# AI Assistant PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/maxwebtech/ai-assistant-sdk.svg?style=flat-square)](https://packagist.org/packages/maxwebtech/ai-assistant-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/maxwebtech/ai-assistant-sdk.svg?style=flat-square)](https://packagist.org/packages/maxwebtech/ai-assistant-sdk)
[![License](https://img.shields.io/packagist/l/maxwebtech/ai-assistant-sdk.svg?style=flat-square)](https://packagist.org/packages/maxwebtech/ai-assistant-sdk)

簡化 AI Assistant 整合的 PHP SDK，支援 JWT 認證和會員限制功能。

## 功能特色

- 🔐 **JWT 認證**：安全的用戶身份驗證
- 👥 **會員等級**：支援多層級會員限制
- 🎨 **多種整合方式**：Widget、iframe、JavaScript SDK
- 🛡️ **安全性**：防重放攻擊、時間戳驗證
- 📱 **響應式**：支援桌面和行動裝置
- 🎯 **易於使用**：簡潔的 API 設計

## 安裝

```bash
composer require maxwebtech/ai-assistant-sdk
```

## 快速開始

### 1. 初始化 SDK

```php
<?php

require_once 'vendor/autoload.php';

use MaxWebTech\AiAssistant\AiAssistantSDK;

$sdk = new AiAssistantSDK([
    'widget_token' => 'wt_your_widget_token',
    'iframe_token' => 'if_your_iframe_token',  // 可選
    'jwt_secret' => 'your_shared_secret',
    'issuer' => 'https://your-website.com',   // 可選
    'api_url' => 'https://ai-assistant.com'   // 可選
]);
```

### 2. 基本使用

```php
// 用戶資料
$user = [
    'id' => '123',
    'name' => '張三',
    'email' => 'user@example.com'
];

// 會員設定
$membership = [
    'level' => 'premium',
    'daily_conversation_limit' => 100,
    'daily_message_limit' => 1000,
    'features' => ['file_upload', 'priority_support']
];

// 生成 Widget HTML
echo $sdk->getWidgetHTML($user, [
    'membership' => $membership,
    'title' => '客服助手',
    'theme' => 'light',
    'position' => 'bottom-right'
]);
```

## 詳細使用說明

### Widget 整合

```php
// 基本 Widget
echo $sdk->getWidgetHTML($user);

// 自訂 Widget
echo $sdk->getWidgetHTML($user, [
    'membership' => ['level' => 'premium'],
    'title' => '智能客服',
    'placeholder' => '請輸入您的問題...',
    'theme' => 'dark',
    'position' => 'bottom-left'
]);
```

### iframe 整合

```php
// 基本 iframe
echo $sdk->getIframeHTML($user);

// 自訂 iframe
echo $sdk->getIframeHTML($user, [
    'membership' => ['level' => 'basic'],
    'width' => '500',
    'height' => '700',
    'style' => 'border: 1px solid #ccc; border-radius: 8px;',
    'title' => '線上客服',
    'placeholder' => '有什麼可以幫您的嗎？'
]);
```

### 動態載入

```php
// 生成 JavaScript 代碼
$jsCode = $sdk->getWidgetJS($user, [
    'membership' => ['level' => 'enterprise'],
    'title' => '專屬客服'
]);

echo "<script>{$jsCode}</script>";
```

### JWT 操作

```php
// 生成 JWT
$jwt = $sdk->generateJWT($user, [
    'level' => 'premium',
    'daily_conversation_limit' => 50
]);

// 驗證 JWT
try {
    $payload = $sdk->validateJWT($jwt);
    echo "用戶 ID: " . $payload['sub'];
} catch (Exception $e) {
    echo "JWT 無效: " . $e->getMessage();
}
```

## 會員等級

### 預設等級

```php
use MaxWebTech\AiAssistant\AiAssistantSDK;

// 獲取預設限制
$limits = AiAssistantSDK::getDefaultLimits('premium');
// 返回: ['daily_conversations' => 100, 'daily_messages' => 1000]
```

| 等級 | 每日對話 | 每日訊息 | 說明 |
|------|----------|----------|------|
| guest | 3 | 20 | 訪客 |
| free | 10 | 100 | 免費會員 |
| basic | 30 | 300 | 基礎會員 |
| premium | 100 | 1000 | 付費會員 |
| enterprise | -1 | -1 | 企業會員（無限制） |

### 查詢使用者剩餘限制

```php
// 獲取使用者的使用狀況和剩餘限制
try {
    $usageStatus = $sdk->getUserUsageStatus($user['id']);
    
    echo "會員等級: " . $usageStatus['membership_level'] . "\n";
    echo "對話使用狀況:\n";
    echo "  已使用: " . $usageStatus['usage']['daily_conversations']['used'] . "\n";
    echo "  限制: " . $usageStatus['usage']['daily_conversations']['limit'] . "\n";
    echo "  剩餘: " . $usageStatus['usage']['daily_conversations']['remaining'] . "\n";
    
    echo "訊息使用狀況:\n";
    echo "  已使用: " . $usageStatus['usage']['daily_messages']['used'] . "\n";
    echo "  限制: " . $usageStatus['usage']['daily_messages']['limit'] . "\n";
    echo "  剩餘: " . $usageStatus['usage']['daily_messages']['remaining'] . "\n";
    
    if ($usageStatus['reset_time']) {
        echo "重置時間: " . $usageStatus['reset_time'] . "\n";
    }
    
    // 檢查是否為無限制方案
    if ($usageStatus['usage']['daily_conversations']['unlimited']) {
        echo "對話: 無限制\n";
    }
    
} catch (Exception $e) {
    echo "查詢失敗: " . $e->getMessage();
}
```

### 使用自訂 Token 查詢

```php
// 使用特定的認證 token 查詢
$usageStatus = $sdk->getUserUsageStatus($user['id'], 'custom_auth_token');
```

### 自訂限制

```php
$customMembership = [
    'level' => 'custom',
    'daily_conversation_limit' => 75,
    'daily_message_limit' => 750,
    'features' => [
        'file_upload',
        'priority_support',
        'custom_branding',
        'api_access'
    ]
];

echo $sdk->getWidgetHTML($user, ['membership' => $customMembership]);
```

## 錯誤處理

```php
try {
    echo $sdk->getWidgetHTML($user);
} catch (InvalidArgumentException $e) {
    // 參數錯誤
    echo "設定錯誤: " . $e->getMessage();
} catch (Exception $e) {
    // 其他錯誤
    echo "系統錯誤: " . $e->getMessage();
}
```

## 安全性注意事項

### 1. 保護 JWT 密鑰

```php
// ❌ 錯誤：寫死在程式碼中
$sdk = new AiAssistantSDK([
    'jwt_secret' => 'my-secret-key'
]);

// ✅ 正確：使用環境變數
$sdk = new AiAssistantSDK([
    'jwt_secret' => $_ENV['AI_ASSISTANT_JWT_SECRET']
]);
```

### 2. 驗證用戶身份

```php
// 確保用戶已登入
if (!auth()->check()) {
    throw new Exception('User must be authenticated');
}

$user = [
    'id' => auth()->id(),
    'name' => auth()->user()->name,
    'email' => auth()->user()->email
];
```

### 3. 適當的過期時間

JWT Token 預設 1 小時過期，這是安全的設定。如需調整，請修改 SDK 代碼中的 `exp` 設定。

## Laravel 整合範例

### Service Provider

```php
// app/Providers/AiAssistantServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MaxWebTech\AiAssistant\AiAssistantSDK;

class AiAssistantServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AiAssistantSDK::class, function ($app) {
            return new AiAssistantSDK([
                'widget_token' => config('ai-assistant.widget_token'),
                'iframe_token' => config('ai-assistant.iframe_token'),
                'jwt_secret' => config('ai-assistant.jwt_secret'),
                'issuer' => config('app.url'),
            ]);
        });
    }
}
```

### Controller

```php
// app/Http/Controllers/ChatController.php
<?php

namespace App\Http\Controllers;

use MaxWebTech\AiAssistant\AiAssistantSDK;

class ChatController extends Controller
{
    public function widget(AiAssistantSDK $sdk)
    {
        $user = [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'email' => auth()->user()->email
        ];

        $membership = [
            'level' => auth()->user()->subscription_level,
            'daily_conversation_limit' => auth()->user()->daily_limit
        ];

        $widgetHtml = $sdk->getWidgetHTML($user, [
            'membership' => $membership,
            'title' => '智能客服'
        ]);

        return view('chat.widget', compact('widgetHtml'));
    }
}
```

### Blade 範例

```php
{{-- resources/views/chat/widget.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>客服支援</h1>
    <p>如有任何問題，請使用右下角的智能客服功能。</p>
</div>

{{-- 載入 AI 客服 Widget --}}
{!! $widgetHtml !!}
@endsection
```

## API 參考

### AiAssistantSDK

#### 建構函數

```php
public function __construct(array $config)
```

**參數：**
- `widget_token` (string, 可選): Widget Token
- `iframe_token` (string, 可選): iframe Token  
- `jwt_secret` (string, 必須): JWT 共享密鑰
- `issuer` (string, 可選): JWT 發行者
- `api_url` (string, 可選): API 基礎 URL

#### 方法

##### generateJWT()

```php
public function generateJWT(array $user, array $membership = []): string
```

生成 JWT Token。

##### getWidgetHTML()

```php
public function getWidgetHTML(array $user, array $options = []): string
```

生成 Widget HTML 代碼。

##### getIframeHTML()

```php
public function getIframeHTML(array $user, array $options = []): string
```

生成 iframe HTML 代碼。

##### getWidgetJS()

```php
public function getWidgetJS(array $user, array $options = []): string
```

生成動態載入 Widget 的 JavaScript 代碼。

##### validateJWT()

```php
public function validateJWT(string $jwt): array
```

驗證並解析 JWT Token。

##### getDefaultLimits()

```php
public static function getDefaultLimits(string $level): array
```

獲取指定會員等級的預設限制。

## 疑難排解

### 常見問題

**Q: 出現「Firebase JWT library is required」錯誤**
```bash
# A: 安裝 JWT 庫
composer require firebase/php-jwt
```

**Q: Widget 沒有出現**
```php
// A: 檢查 Token 是否正確
$sdk = new AiAssistantSDK([
    'widget_token' => 'wt_correct_token_here', // 確保 Token 正確
    'jwt_secret' => 'correct_secret_here'
]);
```

**Q: 出現 429 錯誤（達到限制）**
```php
// A: 檢查會員等級設定
$membership = [
    'level' => 'premium', // 提高會員等級
    'daily_conversation_limit' => 100 // 或增加限制
];
```

## 更新日誌

### v1.0.0 (2024-01-XX)
- 🎉 首次發布
- ✅ JWT 認證支援
- ✅ 多種整合方式
- ✅ 會員等級管理
- ✅ 完整文檔

## 授權

MIT License. 詳見 [LICENSE](LICENSE) 文件。


## 貢獻

歡迎提交 Pull Request 或回報問題！請先閱讀 [CONTRIBUTING.md](CONTRIBUTING.md)。