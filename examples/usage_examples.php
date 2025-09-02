<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxWebTech\AiAssistant\AiAssistantSDK;
use MaxWebTech\AiAssistant\UsageAnalyzer;

/**
 * AI Assistant SDK - Usage API 使用範例
 * 
 * 這個檔案展示了如何使用 SDK 的使用量統計功能
 */

// 初始化 SDK
$sdk = new AiAssistantSDK([
    'widget_token' => 'your_widget_token',
    'iframe_token' => 'your_iframe_token', 
    'api_url' => 'https://your-api-domain.com',
]);

// 您的 JWT token (需要從您的系統生成)
$jwt = 'your_jwt_token_here';

// 可選：指定特定用戶ID
$userId = 'user_123'; // 或者 null 代表所有用戶

echo "=== AI Assistant SDK 使用量統計範例 ===\n\n";

try {
    // =====================================
    // 1. 基本使用量查詢
    // =====================================
    echo "📊 1. 基本使用量查詢\n";
    echo "----------------------------------------\n";
    
    // 獲取今日使用量
    $todayUsage = $sdk->getTodayUsage($jwt, $userId);
    echo "今日使用量:\n";
    echo "- 日期: {$todayUsage['date']}\n";
    echo "- 對話數: {$todayUsage['conversations']}\n";
    echo "- 訊息數: {$todayUsage['messages']}\n";
    echo "- 使用者數: {$todayUsage['unique_users']}\n\n";
    
    // 獲取本月使用量
    $thisMonthUsage = $sdk->getThisMonthUsage($jwt, $userId);
    echo "本月使用量:\n";
    echo "- 月份: {$thisMonthUsage['month']}\n";
    echo "- 總對話數: {$thisMonthUsage['total_conversations']}\n";
    echo "- 總訊息數: {$thisMonthUsage['total_messages']}\n";
    echo "- 總使用者數: {$thisMonthUsage['unique_users']}\n\n";
    
    // =====================================
    // 2. 自定義日期範圍查詢
    // =====================================
    echo "📅 2. 自定義日期範圍查詢\n";
    echo "----------------------------------------\n";
    
    // 獲取特定月份使用量
    $monthlyUsage = $sdk->getMonthlyUsage('2025-08', $jwt, $userId);
    echo "2025年8月使用量:\n";
    echo "- 總對話數: {$monthlyUsage['total_conversations']}\n";
    echo "- 總訊息數: {$monthlyUsage['total_messages']}\n\n";
    
    // 獲取最近7天的每日使用量
    $startDate = date('Y-m-d', strtotime('-7 days'));
    $endDate = date('Y-m-d');
    $weeklyUsage = $sdk->getDailyUsage($startDate, $endDate, $jwt, $userId);
    echo "最近7天使用量:\n";
    foreach ($weeklyUsage['daily_usage'] as $day) {
        echo "- {$day['date']}: {$day['conversations']} 對話, {$day['messages']} 訊息\n";
    }
    echo "總計: {$weeklyUsage['summary']['total_conversations']} 對話, {$weeklyUsage['summary']['total_messages']} 訊息\n\n";
    
    // =====================================
    // 3. 便利方法使用
    // =====================================
    echo "⚡ 3. 便利方法使用\n";
    echo "----------------------------------------\n";
    
    // 獲取本週使用量
    $thisWeekUsage = $sdk->getThisWeekUsage($jwt, $userId);
    echo "本週使用量 ({$thisWeekUsage['week_start']} 至 {$thisWeekUsage['week_end']}):\n";
    echo "- 總對話數: {$thisWeekUsage['total_conversations']}\n";
    echo "- 總訊息數: {$thisWeekUsage['total_messages']}\n";
    echo "- 天數: {$thisWeekUsage['total_days']}\n\n";
    
    // 獲取使用量趨勢 (最近30天)
    $trend = $sdk->getUsageTrend($jwt, $userId);
    echo "使用量趨勢 (最近30天):\n";
    echo "- 期間: {$trend['period']['start']} 至 {$trend['period']['end']}\n";
    echo "- 總對話數: {$trend['summary']['total_conversations']}\n";
    echo "- 總訊息數: {$trend['summary']['total_messages']}\n";
    echo "- 最近7天平均對話: {$trend['averages']['last_7_days_conversations']}\n";
    echo "- 最近7天平均訊息: {$trend['averages']['last_7_days_messages']}\n\n";
    
    // =====================================
    // 4. 使用 UsageAnalyzer 進階分析
    // =====================================
    echo "🔍 4. UsageAnalyzer 進階分析\n";
    echo "----------------------------------------\n";
    
    // 創建分析器實例
    $analyzer = $sdk->createUsageAnalyzer($jwt, $userId);
    
    // 獲取今日摘要
    $todaySummary = $analyzer->todaySummary();
    echo "今日摘要:\n";
    echo "- 對話數: {$todaySummary['conversations']}\n";
    echo "- 訊息數: {$todaySummary['messages']}\n";
    echo "- 平均每對話訊息數: {$todaySummary['avg_messages_per_conversation']}\n\n";
    
    // 獲取本月摘要
    $monthSummary = $analyzer->thisMonthSummary();
    echo "本月摘要:\n";
    echo "- 月份: {$monthSummary['month']}\n";
    echo "- 總對話數: {$monthSummary['total_conversations']}\n";
    echo "- 日平均對話: {$monthSummary['daily_average_conversations']}\n";
    echo "- 日平均訊息: {$monthSummary['daily_average_messages']}\n\n";
    
    // 獲取使用量對比分析 (本月 vs 上月)
    $comparison = $analyzer->getUsageComparison();
    echo "使用量對比 (本月 vs 上月):\n";
    echo "- 對話數變化: {$comparison['changes']['conversations_change_percent']}% ({$comparison['changes']['conversation_growth']})\n";
    echo "- 訊息數變化: {$comparison['changes']['messages_change_percent']}% ({$comparison['changes']['message_growth']})\n\n";
    
    // 獲取週使用量分析
    $weeklyAnalysis = $analyzer->getWeeklyAnalysis();
    echo "週使用量分析:\n";
    echo "- 本週總對話: {$weeklyAnalysis['totals']['conversations']}\n";
    echo "- 活躍天數: {$weeklyAnalysis['active_days_count']}\n";
    echo "- 日平均對話: {$weeklyAnalysis['averages']['daily_conversations']}\n";
    if ($weeklyAnalysis['most_active_day']) {
        $mostActive = $weeklyAnalysis['most_active_day'];
        echo "- 最活躍日: {$mostActive['date']} ({$mostActive['conversations']} 對話)\n";
    }
    echo "\n";
    
    // 獲取使用量預測
    $projection = $analyzer->getUsageProjection();
    echo "使用量預測:\n";
    echo "- 目前本月對話數: {$projection['current_month_actual']['conversations']}\n";
    echo "- 預計月底對話數: {$projection['projected_month_end']['conversations']}\n";
    echo "- 剩餘天數: {$projection['projected_month_end']['remaining_days']}\n";
    echo "- 預測基於最近 {$projection['projected_month_end']['projection_based_on_days']} 天平均值\n\n";
    
    // 獲取使用模式分析
    $patterns = $analyzer->getUsagePatterns();
    echo "使用模式分析:\n";
    if (isset($patterns['insights']['most_active_weekday'])) {
        echo "- 最活躍的星期: {$patterns['insights']['most_active_weekday']}\n";
        echo "- 最安靜的星期: {$patterns['insights']['least_active_weekday']}\n";
    }
    echo "- 分析期間: {$patterns['analysis_period']['start']} 至 {$patterns['analysis_period']['end']}\n\n";
    
    // =====================================
    // 5. 生成完整報告
    // =====================================
    echo "📋 5. 完整分析報告\n";
    echo "----------------------------------------\n";
    
    // 生成數據報告
    $report = $analyzer->generateReport();
    echo "完整報告摘要:\n";
    echo "- 報告期間: {$report['report_period']['start']} 至 {$report['report_period']['end']}\n";
    echo "- 總對話數: {$report['summary']['total_conversations']}\n";
    echo "- 日平均對話: {$report['summary']['avg_conversations_per_day']}\n";
    echo "- 對話數月比變化: {$report['month_comparison']['conversations_change_percent']}%\n";
    
    if ($report['peak_performance']['highest_conversations_day']) {
        $peak = $report['peak_performance']['highest_conversations_day'];
        echo "- 最高對話日: {$peak['date']} ({$peak['conversations']} 對話)\n";
    }
    echo "\n";
    
    // 生成文字報告
    echo "📄 完整文字報告:\n";
    echo str_repeat("-", 50) . "\n";
    $textReport = $analyzer->getTextReport();
    echo $textReport;
    echo str_repeat("-", 50) . "\n\n";
    
    // =====================================
    // 6. 錯誤處理範例
    // =====================================
    echo "⚠️  6. 錯誤處理範例\n";
    echo "----------------------------------------\n";
    
    try {
        // 嘗試使用無效的日期格式
        $invalidUsage = $sdk->getMonthlyUsage('invalid-month', $jwt);
    } catch (Exception $e) {
        echo "捕獲到錯誤: " . $e->getMessage() . "\n";
    }
    
    try {
        // 嘗試使用超過90天的日期範圍
        $invalidRange = $sdk->getDailyUsage('2025-01-01', '2025-06-01', $jwt);
    } catch (Exception $e) {
        echo "捕獲到錯誤: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    echo "✅ 範例執行完成！\n";
    
} catch (Exception $e) {
    echo "❌ 發生錯誤: " . $e->getMessage() . "\n";
    echo "請檢查您的 JWT token 和 API 設定。\n";
}

// =====================================
// 7. 實際使用場景範例
// =====================================
echo "\n💡 實際使用場景範例\n";
echo "========================================\n";

echo "
// 場景1: Dashboard 顯示每日統計
\$todayStats = \$sdk->getTodayUsage(\$jwt);
echo \"今日對話: {\$todayStats['conversations']}，訊息: {\$todayStats['messages']}\";

// 場景2: 管理後台月度報表
\$monthlyReport = \$sdk->getThisMonthUsage(\$jwt);
\$lastMonthReport = \$sdk->getMonthlyUsage(date('Y-m', strtotime('-1 month')), \$jwt);

// 場景3: 用戶個人使用量查詢
\$userUsage = \$sdk->getTodayUsage(\$jwt, \$userId);
\$userMonthly = \$sdk->getThisMonthUsage(\$jwt, \$userId);

// 場景4: 使用趨勢分析
\$analyzer = \$sdk->createUsageAnalyzer(\$jwt);
\$trend = \$analyzer->getUsageTrend();
\$patterns = \$analyzer->getUsagePatterns();

// 場景5: 自動化報告生成
\$report = \$analyzer->generateReport();
\$textReport = \$analyzer->getTextReport();

// 場景6: 使用量預測與容量規劃
\$projection = \$analyzer->getUsageProjection();
if (\$projection['projected_month_end']['conversations'] > 10000) {
    // 發送容量預警
}
";

echo "\n📚 更多功能:\n";
echo "- 所有方法都支援可選的用戶ID參數，可以查詢特定用戶或全體用戶統計\n";
echo "- UsageAnalyzer 提供豐富的分析功能，包含趨勢、模式、預測等\n";
echo "- 完整的錯誤處理和驗證\n";
echo "- 支援自定義日期範圍查詢 (每日查詢最多90天)\n";
echo "- 自動計算各種統計指標和比率\n";
echo "- 文字報告生成，方便直接展示或發送\n";