# AI Assistant SDK - Usage API 使用指南

## 概述

AI Assistant SDK 現在包含強大的使用量統計和分析功能，讓您輕鬆追蹤對話和訊息的使用情況。

## 功能特色

✅ **多種時間範圍查詢**: 今日、本週、本月、自定義日期範圍  
✅ **靈活過濾**: 支援全體用戶或特定用戶統計  
✅ **趨勢分析**: 30天使用趨勢與模式分析  
✅ **預測功能**: 基於歷史數據的使用量預測  
✅ **詳細報告**: 自動生成文字和數據報告  
✅ **便利方法**: 一鍵獲取常用統計數據  

## 快速開始

### 1. 基本設定

```php
use MaxWebTech\AiAssistant\AiAssistantSDK;

$sdk = new AiAssistantSDK([
    'widget_token' => 'your_widget_token',
    'iframe_token' => 'your_iframe_token',
    'api_url' => 'https://your-api-domain.com',
]);

$token = 'your_bearer_token'; // 支援 JWT 或 widget/iframe token（if_/wt_）
$userId = 'user_123';    // 可選: 特定用戶ID，null = 所有用戶
```

### 2. 基本使用量查詢

```php
// 今日使用量
$today = $sdk->getTodayUsage($token, $userId);
echo "今日對話: {$today['conversations']}, 訊息: {$today['messages']}";

// 本月使用量
$thisMonth = $sdk->getThisMonthUsage($token, $userId);
echo "本月對話: {$thisMonth['total_conversations']}";

// 本週使用量
$thisWeek = $sdk->getThisWeekUsage($token, $userId);
echo "本週對話: {$thisWeek['total_conversations']}";
```

### 3. 自定義日期範圍

```php
// 特定月份
$monthly = $sdk->getMonthlyUsage('2025-08', $token, $userId);

// 日期範圍 (最多90天)
$daily = $sdk->getDailyUsage('2025-09-01', '2025-09-07', $token, $userId);
foreach ($daily['daily_usage'] as $day) {
    echo "{$day['date']}: {$day['conversations']} 對話\n";
}
```

### 4. 使用趨勢分析

```php
// 最近30天趨勢
$trend = $sdk->getUsageTrend($jwt, $userId);
echo "最近7天平均對話: {$trend['averages']['last_7_days_conversations']}";
```

## 進階分析 - UsageAnalyzer

### 創建分析器

```php
$analyzer = $sdk->createUsageAnalyzer($token, $userId);
```

### 摘要分析

```php
// 今日摘要
$todaySummary = $analyzer->todaySummary();
echo "平均每對話訊息數: {$todaySummary['avg_messages_per_conversation']}";

// 本月摘要
$monthSummary = $analyzer->thisMonthSummary();
echo "日平均對話: {$monthSummary['daily_average_conversations']}";
```

### 對比分析

```php
// 本月 vs 上月比較
$comparison = $analyzer->getUsageComparison();
echo "對話數變化: {$comparison['changes']['conversations_change_percent']}%";
echo "趨勢: {$comparison['changes']['conversation_growth']}"; // increase/decrease/stable
```

### 週使用量分析

```php
$weekly = $analyzer->getWeeklyAnalysis();
echo "活躍天數: {$weekly['active_days_count']}";
echo "最活躍日: {$weekly['most_active_day']['date']}";
```

### 使用量預測

```php
$projection = $analyzer->getUsageProjection();
echo "目前本月對話數: {$projection['current_month_actual']['conversations']}";
echo "預計月底對話數: {$projection['projected_month_end']['conversations']}";
```

### 使用模式分析

```php
$patterns = $analyzer->getUsagePatterns();
echo "最活躍星期: {$patterns['insights']['most_active_weekday']}";
echo "最安靜星期: {$patterns['insights']['least_active_weekday']}";

// 每個星期的詳細統計
foreach ($patterns['weekday_patterns'] as $day => $stats) {
    echo "{$day}: 平均 {$stats['avg_conversations']} 對話\n";
}
```

## 報告生成

### 完整數據報告

```php
$report = $analyzer->generateReport();

// 基本統計
echo "總對話數: {$report['summary']['total_conversations']}";
echo "日平均: {$report['summary']['avg_conversations_per_day']}";

// 今日狀況
echo "今日對話: {$report['today_status']['conversations']}";

// 月度變化
echo "對話變化: {$report['month_comparison']['conversations_change_percent']}%";

// 峰值表現
if ($report['peak_performance']['highest_conversations_day']) {
    $peak = $report['peak_performance']['highest_conversations_day'];
    echo "最高對話日: {$peak['date']} ({$peak['conversations']} 對話)";
}
```

### 文字報告

```php
$textReport = $analyzer->getTextReport();
echo $textReport;
```

輸出範例:
```
=== 使用量分析報告 ===

📊 基本統計 (2025-08-03 至 2025-09-02)
- 總對話數: 450
- 總訊息數: 1800
- 日平均對話: 15.0
- 日平均訊息: 60.0

📅 今日狀況 (2025-09-02)
- 對話數: 18
- 訊息數: 72
- 每對話平均訊息: 4.0

📈 月度趨勢
- 對話數變化: +25.5% (↗️)
- 訊息數變化: +20.8% (↗️)

🔄 使用模式
- 最活躍日: Tuesday
- 最安靜日: Sunday

🏆 高峰表現
- 最高對話日: 2025-08-15 (35 對話)
- 最高訊息日: 2025-08-20 (140 訊息)
```

## 常用場景範例

### Dashboard 即時統計

```php
$today = $sdk->getTodayUsage($jwt);
$month = $sdk->getThisMonthUsage($jwt);

$html = "
<div class='stats'>
    <div class='stat-card'>
        <h3>今日統計</h3>
        <p>對話: {$today['conversations']}</p>
        <p>訊息: {$today['messages']}</p>
    </div>
    <div class='stat-card'>
        <h3>本月統計</h3>
        <p>對話: {$month['total_conversations']}</p>
        <p>訊息: {$month['total_messages']}</p>
    </div>
</div>
";
```

### 用戶個人使用量

```php
function getUserUsageReport($sdk, $jwt, $userId) {
    $analyzer = $sdk->createUsageAnalyzer($jwt, $userId);
    
    return [
        'today' => $analyzer->todaySummary(),
        'month' => $analyzer->thisMonthSummary(),
        'projection' => $analyzer->getUsageProjection(),
        'comparison' => $analyzer->getUsageComparison(),
    ];
}
```

### 管理後台月度報表

```php
function generateMonthlyReport($sdk, $token, $month) {
    $analyzer = $sdk->createUsageAnalyzer($token);
    $monthly = $sdk->getMonthlyUsage($month, $token);
    $patterns = $analyzer->getUsagePatterns();
    
    return [
        'summary' => $monthly,
        'patterns' => $patterns,
        'text_report' => $analyzer->getTextReport(),
    ];
}
```

### 容量預警系統

```php
function checkCapacityWarning($sdk, $token, $threshold = 10000) {
    $analyzer = $sdk->createUsageAnalyzer($token);
    $projection = $analyzer->getUsageProjection();
    
    if ($projection['projected_month_end']['conversations'] > $threshold) {
        // 發送預警通知
        sendAlert("預計本月對話數將超過 {$threshold}");
    }
}
```

## API 回應格式

### 月使用量回應
```json
{
  "month": "2025-09",
  "total_conversations": 450,
  "total_messages": 1800,
  "unique_users": 25
}
```

### 日使用量回應
```json
{
  "start_date": "2025-09-01",
  "end_date": "2025-09-07", 
  "daily_usage": [
    {
      "date": "2025-09-01",
      "conversations": 15,
      "messages": 60,
      "unique_users": 5
    }
  ],
  "summary": {
    "total_conversations": 105,
    "total_messages": 420,
    "total_days": 7
  }
}
```

## 錯誤處理

```php
try {
    $usage = $sdk->getMonthlyUsage('2025-13', $token); // 無效月份
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage();
    // 錯誤: API Error (422): The month field must be a valid date format.
}

try {
    // 超過90天限制
    $usage = $sdk->getDailyUsage('2025-01-01', '2025-06-01', $token);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage();
    // 錯誤: API Error (400): Date range cannot exceed 90 days
}
```

## 認證與限制

- **日期範圍限制**: 每日使用量查詢最多支援90天範圍
- **月份格式**: 必須使用 `YYYY-MM` 格式
- **日期格式**: 必須使用 `YYYY-MM-DD` 格式
- **認證**: Usage API 需要有效 Bearer token。
  - 支援 JWT 或 widget/iframe token（`if_`/`wt_`）。
  - 為便於後端查詢，Usage API 對 widget/iframe token 放寬來源檢查（無需 `Origin`）。其他路由仍依原白名單策略。
- **租戶隔離**: 依據 token 解析到的租戶自動隔離（JWT: tenant 由 payload 辨識；widget/iframe: 由 token 綁定）

## 測試

運行SDK測試:
```bash
cd packages/ai-assistant-sdk
composer test
```

運行特定的使用量測試:
```bash
composer test -- --filter Usage
```

## 更新日誌

### v1.1.0 (2025-09-02)
- ✅ 新增完整的Usage API支援
- ✅ 新增UsageAnalyzer進階分析類
- ✅ 新增趨勢分析和預測功能
- ✅ 新增使用模式分析
- ✅ 新增自動報告生成
- ✅ 完整的測試覆蓋

## 支援

如有問題或需要協助，請聯繫 MaxWebTech 技術支援團隊。
