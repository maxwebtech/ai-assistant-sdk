# AI Assistant PHP SDK

超快整合 AI 小幫手的 PHP 套件。內建 JWT 支援、會員等級、對話與訊息額度、**使用量統計分析**，3 分鐘上線。

## ✨ 新功能

- 🔥 **Usage API**: 完整使用量統計與分析
- 📊 **進階分析器**: 趨勢分析、使用預測、模式識別
- 📈 **智能報告**: 自動生成文字和數據報告
- 🎯 **多維查詢**: 今日、本週、本月、自定義日期範圍
- 👥 **靈活過濾**: 全體用戶或個人統計

## 安裝

```bash
composer require maxwebtech/ai-assistant-sdk firebase/php-jwt
```

## 目錄

- [快速開始](#快速開始4-步驟)
- [Iframe 嵌入](#iframe-嵌入可選)
- [會員與用量 API](#會員與用量-api可選)
- [Usage API - 使用量統計](#usage-api---使用量統計new)
- [會員對話歷史 API](#會員對話歷史-apinew)
- [即時用量更新](#即時用量更新new)
- [每月限制支援](#每月限制支援new)

## 快速開始（4 步驟）

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
// 租戶信息從JWT自動解析
// 若提供 membershipLevel，SDK 會先行同步（assign）會員等級再查詢
// 直接使用你服務端簽的 JWT（必要）
$quota = $sdk->checkUserQuota($userId, $jwt, $sessionId, $membershipLevel);

$daily = $quota['daily_conversations'];
printf('今日對話：已用 %d，剩餘 %s', $daily['used'], $daily['unlimited'] ? '無限制' : (string) $daily['remaining']);

// 如果有設定每月限制，也會在回應中包含
if (isset($quota['monthly_conversations'])) {
    $monthly = $quota['monthly_conversations'];
    printf('本月對話：已用 %d，剩餘 %s', $monthly['used'], $monthly['unlimited'] ? '無限制' : (string) $monthly['remaining']);
}
```

4) 使用量統計（可選）

```php
// 快速獲取今日使用量統計
$today = $sdk->getTodayUsage($jwt, $userId);
echo "今日: {$today['conversations']} 對話, {$today['messages']} 訊息";

// 創建分析器進行進階分析
$analyzer = $sdk->createUsageAnalyzer($jwt, $userId);
$summary = $analyzer->todaySummary();
echo "平均每對話訊息數: {$summary['avg_messages_per_conversation']}";

// 生成完整使用量報告
$textReport = $analyzer->getTextReport();
echo $textReport; // 包含趨勢、對比、預測等完整分析
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
$tiers = $sdk->getMembershipTiers($jwt);

// 指定用戶等級（直接提供管理 JWT）
$sdk->assignMembershipTier('user-123', 'premium', $jwt);

// 重置用量快取（同上）
$sdk->resetUserQuota('user-123', $jwt);
```

## Usage API - 使用量統計（NEW）

透過 Usage API 輕鬆追蹤和分析使用量統計，支援多種時間範圍和進階分析功能。

### 基本使用量查詢

```php
// 今日使用量
$today = $sdk->getTodayUsage($jwt, $userId); // $userId 可選，null = 全體用戶
echo "今日對話: {$today['conversations']}, 訊息: {$today['messages']}";

// 本月使用量
$thisMonth = $sdk->getThisMonthUsage($jwt, $userId);
echo "本月總對話: {$thisMonth['total_conversations']}";

// 本週使用量
$thisWeek = $sdk->getThisWeekUsage($jwt, $userId);
echo "本週對話: {$thisWeek['total_conversations']}";

// 自定義月份
$august = $sdk->getMonthlyUsage('2025-08', $jwt, $userId);

// 自定義日期範圍 (最多90天)
$weekly = $sdk->getDailyUsage('2025-09-01', '2025-09-07', $jwt, $userId);
foreach ($weekly['daily_usage'] as $day) {
    echo "{$day['date']}: {$day['conversations']} 對話\n";
}
```

### 進階分析 - UsageAnalyzer

```php
// 創建分析器
$analyzer = $sdk->createUsageAnalyzer($jwt, $userId);

// 今日摘要
$todaySummary = $analyzer->todaySummary();
echo "平均每對話訊息: {$todaySummary['avg_messages_per_conversation']}";

// 本月摘要
$monthSummary = $analyzer->thisMonthSummary();
echo "日平均對話: {$monthSummary['daily_average_conversations']}";

// 使用量對比 (本月 vs 上月)
$comparison = $analyzer->getUsageComparison();
echo "對話變化: {$comparison['changes']['conversations_change_percent']}%";
echo "趨勢: {$comparison['changes']['conversation_growth']}"; // increase/decrease/stable

// 週使用量分析
$weekly = $analyzer->getWeeklyAnalysis();
echo "活躍天數: {$weekly['active_days_count']}";
echo "最活躍日: {$weekly['most_active_day']['date']}";

// 使用量預測
$projection = $analyzer->getUsageProjection();
echo "預計月底對話數: {$projection['projected_month_end']['conversations']}";

// 使用模式分析
$patterns = $analyzer->getUsagePatterns();
echo "最活躍星期: {$patterns['insights']['most_active_weekday']}";
```

### 報告生成

```php
// 生成完整數據報告
$report = $analyzer->generateReport();
echo "總對話數: {$report['summary']['total_conversations']}";
echo "對話變化: {$report['month_comparison']['conversations_change_percent']}%";

// 生成文字報告
$textReport = $analyzer->getTextReport();
echo $textReport;
```

文字報告範例輸出：
```
=== 使用量分析報告 ===

📊 基本統計 (2025-08-03 至 2025-09-02)
- 總對話數: 450
- 總訊息數: 1800
- 日平均對話: 15.0

📅 今日狀況 (2025-09-02)
- 對話數: 18
- 訊息數: 72

📈 月度趨勢
- 對話數變化: +25.5% (↗️)
- 訊息數變化: +20.8% (↗️)

🔄 使用模式
- 最活躍日: Tuesday
- 最安靜日: Sunday
```

### 使用場景範例

```php
// Dashboard 即時統計
$stats = $sdk->getTodayUsage($jwt);

// 個人使用量查詢
$userStats = $sdk->getThisMonthUsage($jwt, $userId);

// 容量預警系統
$projection = $analyzer->getUsageProjection();
if ($projection['projected_month_end']['conversations'] > 10000) {
    sendAlert("使用量即將超標");
}

// 生成月度報表
$monthlyReport = $analyzer->generateReport();
$textReport = $analyzer->getTextReport();
```

詳細說明請參考：[Usage API 完整文檔](USAGE_API.md)

## 會員對話歷史 API（NEW）

```php
// 取得會員的對話列表
$conversations = $sdk->getMemberConversations('user-123', $jwt, $page = 1, $perPage = 20);

foreach ($conversations['data'] as $conversation) {
    echo "對話: {$conversation['title']} (ID: {$conversation['id']})";
    echo "助手: {$conversation['assistant']['name']}";
    echo "訊息數: {$conversation['message_count']}";
    echo "最後更新: {$conversation['last_message_at']}";
}

// 取得特定對話的完整內容（包含所有訊息）
$conversationDetails = $sdk->getMemberConversation('user-123', $conversationId, $jwt);

echo "對話標題: {$conversationDetails['conversation']['title']}";
foreach ($conversationDetails['messages'] as $message) {
    echo "{$message['role']}: {$message['content']}";
}

// 取得對話訊息（分頁）
$messages = $sdk->getMemberConversationMessages('user-123', $conversationId, $jwt, $page = 1, $perPage = 50);

echo "對話: {$messages['conversation']['title']}";
echo "總訊息數: {$messages['pagination']['total']}";
foreach ($messages['messages'] as $message) {
    echo "{$message['role']}: {$message['content']} ({$message['created_at']})";
}
```

### 回傳格式說明

**對話列表：**
```php
[
    'data' => [
        [
            'id' => 1,
            'title' => '關於產品問題',
            'created_at' => '2025-01-01T10:00:00Z',
            'last_message_at' => '2025-01-01T10:30:00Z',
            'message_count' => 5,
            'assistant' => [
                'id' => 1,
                'name' => '客服助手'
            ]
        ]
    ],
    'pagination' => [
        'current_page' => 1,
        'per_page' => 20,
        'total' => 100,
        'last_page' => 5
    ]
]
```

**對話詳情：**
```php
[
    'conversation' => [
        'id' => 1,
        'title' => '關於產品問題',
        'created_at' => '2025-01-01T10:00:00Z',
        'last_message_at' => '2025-01-01T10:30:00Z',
        'message_count' => 5,
        'status' => 'active',
        'assistant' => [
            'id' => 1,
            'name' => '客服助手'
        ]
    ],
    'messages' => [
        [
            'id' => 1,
            'role' => 'user',
            'content' => '你好，我想詢問產品相關問題',
            'created_at' => '2025-01-01T10:00:00Z',
            'metadata' => []
        ],
        [
            'id' => 2,
            'role' => 'assistant', 
            'content' => '您好！我很樂意為您解答產品相關問題',
            'created_at' => '2025-01-01T10:01:00Z',
            'metadata' => []
        ]
    ]
]
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
$quota = $sdk->checkUserQuota($userId, $jwt);

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
- `iframe_token`：後台建立iframe token（可選）
- `api_url`：後端 API 位址（本機多為 `http://localhost:8000`）

**重要**：租戶信息完全從 JWT 中解析，無需在 SDK 設定中提供 `tenant_id`

## 常見問題

- 顯示「無限制」但我有設定上限？
  - 你的用戶可能被視為匿名。使用 `checkUserQuota($userId, null, null, 'free')` 會先自動同步會員等級再查詢。
- Widget 沒有出現？
  - 檢查 `widget_token` 是否正確，且助手允許你的網域（allowed domains）。
- 出現 JWT 錯誤？
  - 安裝 `firebase/php-jwt`，並確認 `jwt_secret`、`issuer`、`api_url` 設定無誤。

## 授權

MIT
