# AI Assistant PHP SDK

超快整合 AI 小幫手的 PHP 套件。內建 JWT 支援、會員等級、對話與訊息額度，3 分鐘上線。

## 安裝

```bash
composer require maxwebtech/ai-assistant-sdk firebase/php-jwt
```

## 快速開始（3 步驟）

1) 初始化

```php
use MaxWebTech\AiAssistant\AiAssistantSDK;

$sdk = new AiAssistantSDK([
    'widget_token' => getenv('AI_ASSISTANT_WIDGET_TOKEN'),
    'iframe_token' => getenv('AI_ASSISTANT_IFRAME_TOKEN'), // 可選
    'api_url'      => getenv('AI_ASSISTANT_API_URL') ?: 'http://localhost:8000',
]);
```

2) 前端嵌入（Widget）

```php
$user = ['id' => $userId, 'name' => $name, 'email' => $email];

// 如果你的服務端已經會發 JWT，直接傳入即可（最推薦）；SDK 不會自產
// $jwt = issueJwtForUser($user); // 你的系統生成
echo $sdk->getWidgetHTML($user, [
    'jwt' => $jwt ?? null,               // 若提供，SDK 直接使用，不會自己產生
    'membership' => ['level' => 'free'], // 可選；若無 jwt，SDK 會退回 member-id 模式
    'title' => 'AI 助手',
]);
```

3) 查詢用量（自動避免匿名誤判）

```php
// 會優先使用 SDK config 的 tenant_id
// 若提供 membershipLevel，SDK 會先行同步（assign）會員等級再查詢
// 直接使用你服務端簽的 JWT（必要）
$quota = $sdk->checkUserQuota($userId, (int) getenv('AI_ASSISTANT_TENANT_ID'), null, null, $jwt);

$daily = $quota['daily_conversations'];
printf('今日對話：已用 %d，剩餘 %s', $daily['used'], $daily['unlimited'] ? '無限制' : (string) $daily['remaining']);

// 如果有設定每月限制，也會在回應中包含
if (isset($quota['monthly_conversations'])) {
    $monthly = $quota['monthly_conversations'];
    printf('本月對話：已用 %d，剩餘 %s', $monthly['used'], $monthly['unlimited'] ? '無限制' : (string) $monthly['remaining']);
}
```

## Iframe 嵌入（可選）

```php
// 同理：若你已有 JWT，直接帶上
echo $sdk->getIframeHTML($user, [
    'jwt' => $jwt ?? null,
    'membership' => ['level' => 'basic'],
    'width' => '100%',
    'height' => '600px',
    'style' => 'border: 1px solid #e5e7eb; border-radius: 8px;',
    'title' => 'AI Assistant',
]);
```

## 會員與用量 API（可選）

```php
// 取得等級清單（需管理權限 JWT）
$tiers = $sdk->getMembershipTiers((int) getenv('AI_ASSISTANT_TENANT_ID'), $jwt);

// 指定用戶等級（直接提供管理 JWT）
$sdk->assignMembershipTier('user-123', 'premium', (int) getenv('AI_ASSISTANT_TENANT_ID'), $jwt);

// 重置用量快取（同上）
$sdk->resetUserQuota('user-123', (int) getenv('AI_ASSISTANT_TENANT_ID'), $jwt);
```

## 即時用量更新（NEW）

使用 Iframe 模式時，可監聽用量更新事件來即時顯示剩餘次數：

```javascript
window.addEventListener('message', function(event) {
    if (event.data.type === 'ai-chat-usage-updated') {
        const usage = event.data.usage;
        
        console.log('訊息使用量:', {
            used: usage.messages.used,
            limit: usage.messages.limit,
            remaining: usage.messages.remaining
        });
        
        console.log('對話使用量:', {
            used: usage.conversations.used,
            limit: usage.conversations.limit,
            remaining: usage.conversations.remaining
        });
        
        console.log('會員等級:', usage.membershipLevel);
        
        // 更新你的 UI 顯示
        updateUsageDisplay(usage);
    }
});

function updateUsageDisplay(usage) {
    // 範例：更新頁面上的使用量顯示
    if (usage.messages.limit) {
        document.getElementById('messages-count').textContent = 
            `${usage.messages.used}/${usage.messages.limit}`;
        document.getElementById('messages-remaining').textContent = 
            `剩餘 ${usage.messages.remaining} 則訊息`;
    } else {
        document.getElementById('messages-remaining').textContent = '無限制';
    }
}
```

## 每月限制支援（NEW）

SDK 現在支援彈性的每日和每月限制設定：

### 1. 只設定每日限制
```php
$membership = [
    'level' => 'daily_plan',
    'daily_conversation_limit' => 20,
    'daily_message_limit' => 200,
    'monthly_conversation_limit' => null, // 不限制每月
    'monthly_message_limit' => null
];
```

### 2. 只設定每月限制
```php
$membership = [
    'level' => 'monthly_plan',
    'daily_conversation_limit' => null,   // 不限制每日
    'daily_message_limit' => null,
    'monthly_conversation_limit' => 500,
    'monthly_message_limit' => 5000
];
```

### 3. 同時設定兩種限制
```php
$membership = [
    'level' => 'strict_plan',
    'daily_conversation_limit' => 10,     // 每日最多10個對話
    'daily_message_limit' => 100,         // 每日最多100條訊息
    'monthly_conversation_limit' => 200,  // 每月最多200個對話
    'monthly_message_limit' => 2000       // 每月最多2000條訊息
];
```

當同時設定每日和每月限制時，用戶需要同時滿足兩種限制才能使用。

### 檢查每月用量
```php
$quota = $sdk->checkUserQuota($userId, $tenantId, null, null, $jwt);

// 檢查每日用量
if (isset($quota['daily_messages'])) {
    $daily = $quota['daily_messages'];
    echo "每日訊息：{$daily['used']}/{$daily['limit']} (剩餘 {$daily['remaining']})";
}

// 檢查每月用量（如果有設定）
if (isset($quota['monthly_messages'])) {
    $monthly = $quota['monthly_messages'];
    echo "每月訊息：{$monthly['used']}/{$monthly['limit']} (剩餘 {$monthly['remaining']})";
    echo "每月重置時間：{$quota['monthly_reset_time']}";
}
```

## 設定說明

- `widget_token`：後台建立助手後取得
- `jwt_secret`：後台租戶設定的 JWT secret（建議設，SDK 才能自動帶 JWT）
- `tenant_id`：你的租戶 ID（查用量與管理 API 需要）
- `api_url`：後端 API 位址（本機多為 `http://localhost:8000`）
- `issuer`：你的站點 URL（可選）

## 常見問題

- 顯示「無限制」但我有設定上限？
  - 你的用戶可能被視為匿名。使用 `checkUserQuota($userId, null, null, 'free')` 會先自動同步會員等級再查詢。
- Widget 沒有出現？
  - 檢查 `widget_token` 是否正確，且助手允許你的網域（allowed domains）。
- 出現 JWT 錯誤？
  - 安裝 `firebase/php-jwt`，並確認 `jwt_secret`、`issuer`、`api_url` 設定無誤。

## 授權

MIT
