# AI Assistant PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/maxwebtech/ai-assistant-sdk.svg?style=flat-square)](https://packagist.org/packages/maxwebtech/ai-assistant-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/maxwebtech/ai-assistant-sdk.svg?style=flat-square)](https://packagist.org/packages/maxwebtech/ai-assistant-sdk)
[![License](https://img.shields.io/packagist/l/maxwebtech/ai-assistant-sdk.svg?style=flat-square)](https://packagist.org/packages/maxwebtech/ai-assistant-sdk)

簡化 AI Assistant 整合的 PHP SDK，支援 JWT 認證和動態會員等級管理。

## 功能特色

- 🔐 **JWT 認證**：安全的用戶身份驗證
- 👥 **動態會員等級**：支援資料庫驅動的多層級會員限制
- 🎨 **多種整合方式**：Widget、iframe、JavaScript SDK
- 🛡️ **安全性**：防重放攻擊、時間戳驗證
- 📱 **響應式**：支援桌面和行動裝置
- 🎯 **易於使用**：簡潔的 API 設計
- ⚡ **彈性部署**：支援純匿名、純會員或混合模式

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
    'jwt_secret' => 'your_shared_secret',      // 可選，JWT 使用
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

### 會員等級 API 認證

SDK 使用 JWT 認證來調用會員等級管理相關的 API：

```php
$sdk = new AiAssistantSDK([
    'widget_token' => 'wt_your_widget_token',  // 用於 Widget 嵌入
    'jwt_secret' => 'your_jwt_secret',         // 用於 API 調用
]);
```

## 會員等級管理

### 獲取所有等級

```php
// 需要 JWT secret 來調用管理 API
$sdk = new AiAssistantSDK([
    'widget_token' => 'wt_xxx',
    'jwt_secret' => 'your_jwt_secret'
]);

// 獲取租戶配置的所有等級（需要租戶 ID）
$tiers = $sdk->getMembershipTiers(123); // 123 是租戶 ID

foreach ($tiers['data'] as $tier) {
    echo "等級: {$tier['name']} ({$tier['slug']})\n";
    echo "每日訊息: " . ($tier['daily_message_limit'] ?? '無限制') . "\n";
    echo "每日對話: " . ($tier['daily_conversation_limit'] ?? '無限制') . "\n";
    echo "---\n";
}
```

### 獲取特定等級

```php
$tier = $sdk->getMembershipTier('premium');
if ($tier['success']) {
    echo "等級名稱: " . $tier['data']['name'] . "\n";
    echo "每日訊息限制: " . ($tier['data']['daily_message_limit'] ?? '無限制') . "\n";
}
```

### 檢查用戶額度

```php
// 會員用戶額度檢查
$quota = $sdk->checkUserQuota('user123');

// 匿名用戶額度檢查
$quota = $sdk->checkUserQuota('', 'session_abc123');

if ($quota['success']) {
    $data = $quota['data'];
    echo "當前等級: " . $data['tier']['name'] . "\n";
    echo "訊息使用: {$data['usage']['messages']} / " . ($data['tier']['daily_message_limit'] ?? '無限制') . "\n";
    echo "對話使用: {$data['usage']['conversations']} / " . ($data['tier']['daily_conversation_limit'] ?? '無限制') . "\n";
    echo "可發送訊息: " . ($data['can_send_message'] ? '是' : '否') . "\n";
    echo "可創建對話: " . ($data['can_create_conversation'] ? '是' : '否') . "\n";
    echo "重置時間: " . $data['reset_time'] . "\n";
}
```

### 分配會員等級

```php
// 將用戶設定為特定等級
$result = $sdk->assignMembershipTier('user123', 'premium');

if ($result['success']) {
    echo "成功將用戶升級為 premium 等級\n";
}
```

### 重置用戶額度

```php
// 清除用戶額度快取（管理員功能）
$result = $sdk->resetUserQuota('user123');

if ($result['success']) {
    echo "用戶額度已重置\n";
}
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

## 使用情境

### 情境 1：完全匿名網站（無會員制度）

```php
$sdk = new AiAssistantSDK(['widget_token' => 'wt_xxx']);

// 所有用戶都使用相同額度（由管理員在後台設定匿名預設等級）
echo $sdk->getWidgetHTML(['id' => session_id()]);
```

### 情境 2：有會員制度的網站

```php
$sdk = new AiAssistantSDK([
    'widget_token' => 'wt_xxx'
]);

// 根據用戶等級使用不同額度
$userTier = getUserMembershipLevel($userId); // 你的邏輯

echo $sdk->getWidgetHTML([
    'id' => $userId,
    'name' => $userName,
    'email' => $userEmail
], [
    'membership' => ['level' => $userTier] // 'free', 'premium', etc.
]);

// 檢查用戶額度狀況
$quota = $sdk->checkUserQuota($userId);
if (!$quota['data']['can_send_message']) {
    echo "額度已用完，請升級會員或等待明日重置";
}
```

### 情境 3：混合模式（支援登入用戶和訪客）

```php
if (auth()->check()) {
    // 已登入用戶
    echo $sdk->getWidgetHTML([
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'email' => auth()->user()->email
    ], [
        'membership' => ['level' => auth()->user()->tier]
    ]);
} else {
    // 訪客用戶
    echo $sdk->getWidgetHTML(['id' => session_id()]);
}
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
- `jwt_secret` (string, 可選): JWT 共享密鑰
- `issuer` (string, 可選): JWT 發行者
- `api_url` (string, 可選): API 基礎 URL

#### 方法


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


##### getMembershipTiers()

```php
public function getMembershipTiers(int $tenantId): array
```

獲取租戶的所有會員等級（使用 JWT 認證）。

##### getMembershipTier()

```php
public function getMembershipTier(string $slug): array
```

獲取特定會員等級資訊（使用 widget_token）。

##### checkUserQuota()

```php
public function checkUserQuota(string $userId, ?string $sessionId = null): array
```

檢查用戶額度狀況（使用 widget_token）。

##### assignMembershipTier()

```php
public function assignMembershipTier(string $userId, string $tierSlug): array
```

分配會員等級給用戶（使用 widget_token）。

##### resetUserQuota()

```php
public function resetUserQuota(string $userId, ?string $sessionId = null): array
```

重置用戶額度（使用 widget_token）。

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