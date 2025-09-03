<?php

declare(strict_types=1);

namespace MaxWebTech\AiAssistant;

use DateTime;

/**
 * Usage Analyzer Helper Class
 *
 * 提供便利的使用量統計和分析功能
 *
 * @version 1.0.0
 *
 * @author MaxWebTech Team
 */
class UsageAnalyzer
{
    private AiAssistantSDK $sdk;

    private string $jwt;

    private ?string $userId;

    public function __construct(AiAssistantSDK $sdk, string $jwt, ?string $userId = null)
    {
        $this->sdk = $sdk;
        $this->jwt = $jwt;
        $this->userId = $userId;
    }

    /**
     * 快速獲取今日摘要
     */
    public function todaySummary(): array
    {
        $today = $this->sdk->getTodayUsage($this->jwt, $this->userId);

        return [
            'date' => $today['date'],
            'conversations' => $today['conversations'],
            'messages' => $today['messages'],
            'unique_users' => $today['unique_users'],
            'avg_messages_per_conversation' => $today['conversations'] > 0
                ? round($today['messages'] / $today['conversations'], 2)
                : 0,
        ];
    }

    /**
     * 快速獲取本月摘要
     */
    public function thisMonthSummary(): array
    {
        $month = $this->sdk->getThisMonthUsage($this->jwt, $this->userId);

        return [
            'month' => $month['month'],
            'total_conversations' => $month['total_conversations'],
            'total_messages' => $month['total_messages'],
            'unique_users' => $month['unique_users'],
            'avg_messages_per_conversation' => $month['total_conversations'] > 0
                ? round($month['total_messages'] / $month['total_conversations'], 2)
                : 0,
            'daily_average_conversations' => round($month['total_conversations'] / date('j'), 2),
            'daily_average_messages' => round($month['total_messages'] / date('j'), 2),
        ];
    }

    /**
     * 獲取使用量對比分析
     */
    public function getUsageComparison(): array
    {
        $thisMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        $currentMonth = $this->sdk->getMonthlyUsage($thisMonth, $this->jwt, $this->userId);
        $previousMonth = $this->sdk->getMonthlyUsage($lastMonth, $this->jwt, $this->userId);

        $conversationChange = $previousMonth['total_conversations'] > 0
            ? (($currentMonth['total_conversations'] - $previousMonth['total_conversations']) / $previousMonth['total_conversations']) * 100
            : ($currentMonth['total_conversations'] > 0 ? 100 : 0);

        $messageChange = $previousMonth['total_messages'] > 0
            ? (($currentMonth['total_messages'] - $previousMonth['total_messages']) / $previousMonth['total_messages']) * 100
            : ($currentMonth['total_messages'] > 0 ? 100 : 0);

        return [
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'changes' => [
                'conversations_change_percent' => round($conversationChange, 2),
                'messages_change_percent' => round($messageChange, 2),
                'conversation_growth' => $conversationChange > 0 ? 'increase' : ($conversationChange < 0 ? 'decrease' : 'stable'),
                'message_growth' => $messageChange > 0 ? 'increase' : ($messageChange < 0 ? 'decrease' : 'stable'),
            ],
        ];
    }

    /**
     * 獲取週使用量分析
     */
    public function getWeeklyAnalysis(): array
    {
        $thisWeek = $this->sdk->getThisWeekUsage($this->jwt, $this->userId);

        // 計算每日平均
        $activeDays = array_filter($thisWeek['daily_usage'], fn ($day) => $day['conversations'] > 0 || $day['messages'] > 0);
        $activeDaysCount = count($activeDays);

        // 找出最活躍的一天
        $mostActiveDay = null;
        $maxActivity = 0;
        foreach ($thisWeek['daily_usage'] as $day) {
            $activity = $day['conversations'] + $day['messages'];
            if ($activity > $maxActivity) {
                $maxActivity = $activity;
                $mostActiveDay = $day;
            }
        }

        return [
            'week_period' => [
                'start' => $thisWeek['week_start'],
                'end' => $thisWeek['week_end'],
            ],
            'totals' => [
                'conversations' => $thisWeek['total_conversations'],
                'messages' => $thisWeek['total_messages'],
                'days' => $thisWeek['total_days'],
            ],
            'averages' => [
                'daily_conversations' => $activeDaysCount > 0 ? round($thisWeek['total_conversations'] / $activeDaysCount, 2) : 0,
                'daily_messages' => $activeDaysCount > 0 ? round($thisWeek['total_messages'] / $activeDaysCount, 2) : 0,
            ],
            'most_active_day' => $mostActiveDay,
            'active_days_count' => $activeDaysCount,
            'daily_breakdown' => $thisWeek['daily_usage'],
        ];
    }

    /**
     * 生成使用量報告
     */
    public function generateReport(int $days = 30): array
    {
        $trend = $this->sdk->getUsageTrend($this->jwt, $this->userId);
        $comparison = $this->getUsageComparison();
        $todaySummary = $this->todaySummary();

        // 計算峰值和低谷
        $conversationData = $trend['trends']['conversations'];
        $messageData = $trend['trends']['messages'];

        $peakConversationDay = array_keys($conversationData, max($conversationData))[0] ?? null;
        $peakMessageDay = array_keys($messageData, max($messageData))[0] ?? null;

        return [
            'report_period' => $trend['period'],
            'summary' => [
                'total_conversations' => $trend['summary']['total_conversations'],
                'total_messages' => $trend['summary']['total_messages'],
                'total_days' => $trend['summary']['total_days'],
                'avg_conversations_per_day' => round($trend['summary']['total_conversations'] / $trend['summary']['total_days'], 2),
                'avg_messages_per_day' => round($trend['summary']['total_messages'] / $trend['summary']['total_days'], 2),
            ],
            'today_status' => $todaySummary,
            'month_comparison' => $comparison['changes'],
            'peak_performance' => [
                'highest_conversations_day' => $peakConversationDay !== null ? [
                    'date' => $trend['daily_usage'][$peakConversationDay]['date'],
                    'conversations' => $conversationData[$peakConversationDay],
                ] : null,
                'highest_messages_day' => $peakMessageDay !== null ? [
                    'date' => $trend['daily_usage'][$peakMessageDay]['date'],
                    'messages' => $messageData[$peakMessageDay],
                ] : null,
            ],
            'recent_averages' => $trend['averages'],
            'trend_data' => $trend['trends'],
        ];
    }

    /**
     * 獲取使用量預測 (基於最近7天平均)
     */
    public function getUsageProjection(): array
    {
        $trend = $this->sdk->getUsageTrend($this->jwt, $this->userId);

        $recent7DaysConversations = $trend['averages']['last_7_days_conversations'];
        $recent7DaysMessages = $trend['averages']['last_7_days_messages'];

        $currentMonth = date('Y-m');
        $daysInMonth = date('t');
        $currentDay = date('j');
        $remainingDays = $daysInMonth - $currentDay;

        // 獲取本月已有數據
        $thisMonth = $this->sdk->getThisMonthUsage($this->jwt, $this->userId);

        return [
            'current_month_actual' => [
                'conversations' => $thisMonth['total_conversations'],
                'messages' => $thisMonth['total_messages'],
                'days_elapsed' => $currentDay,
            ],
            'recent_daily_average' => [
                'conversations' => $recent7DaysConversations,
                'messages' => $recent7DaysMessages,
            ],
            'projected_month_end' => [
                'conversations' => $thisMonth['total_conversations'] + ($recent7DaysConversations * $remainingDays),
                'messages' => $thisMonth['total_messages'] + ($recent7DaysMessages * $remainingDays),
                'projection_based_on_days' => 7,
                'remaining_days' => $remainingDays,
            ],
            'growth_indicators' => [
                'on_track_to_exceed_last_month' => null, // 需要有上月數據才能計算
            ],
        ];
    }

    /**
     * 獲取使用模式分析
     */
    public function getUsagePatterns(): array
    {
        $trend = $this->sdk->getUsageTrend($this->jwt, $this->userId);
        $dailyUsage = $trend['daily_usage'];

        // 計算星期幾的模式
        $weekdayStats = [
            1 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Monday
            2 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Tuesday
            3 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Wednesday
            4 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Thursday
            5 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Friday
            6 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Saturday
            0 => ['conversations' => 0, 'messages' => 0, 'count' => 0], // Sunday
        ];

        foreach ($dailyUsage as $day) {
            $dayOfWeek = (new DateTime($day['date']))->format('w');
            $weekdayStats[$dayOfWeek]['conversations'] += $day['conversations'];
            $weekdayStats[$dayOfWeek]['messages'] += $day['messages'];
            $weekdayStats[$dayOfWeek]['count']++;
        }

        // 計算每個星期幾的平均值
        $weekdayAverages = [];
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($weekdayStats as $dayNum => $stats) {
            if ($stats['count'] > 0) {
                $weekdayAverages[$dayNames[$dayNum]] = [
                    'avg_conversations' => round($stats['conversations'] / $stats['count'], 2),
                    'avg_messages' => round($stats['messages'] / $stats['count'], 2),
                    'sample_days' => $stats['count'],
                ];
            }
        }

        // 找出最活躍和最安靜的日子
        $mostActiveDay = null;
        $leastActiveDay = null;
        $maxActivity = -1;
        $minActivity = PHP_INT_MAX;

        foreach ($weekdayAverages as $day => $stats) {
            $activity = $stats['avg_conversations'] + $stats['avg_messages'];
            if ($activity > $maxActivity) {
                $maxActivity = $activity;
                $mostActiveDay = $day;
            }
            if ($activity < $minActivity) {
                $minActivity = $activity;
                $leastActiveDay = $day;
            }
        }

        return [
            'weekday_patterns' => $weekdayAverages,
            'insights' => [
                'most_active_weekday' => $mostActiveDay,
                'least_active_weekday' => $leastActiveDay,
                'weekend_vs_weekday' => [
                    'weekend_avg_conversations' => round(($weekdayAverages['Saturday']['avg_conversations'] ?? 0) + ($weekdayAverages['Sunday']['avg_conversations'] ?? 0) / 2, 2),
                    'weekday_avg_conversations' => round(array_sum(array_column(array_slice($weekdayAverages, 1, 5, true), 'avg_conversations')) / 5, 2),
                ],
            ],
            'analysis_period' => $trend['period'],
        ];
    }

    /**
     * 創建簡單的文字報告
     */
    public function getTextReport(): string
    {
        $report = $this->generateReport();
        $patterns = $this->getUsagePatterns();

        $text = "=== 使用量分析報告 ===\n\n";

        // 基本統計
        $text .= "📊 基本統計 ({$report['report_period']['start']} 至 {$report['report_period']['end']})\n";
        $text .= "- 總對話數: {$report['summary']['total_conversations']}\n";
        $text .= "- 總訊息數: {$report['summary']['total_messages']}\n";
        $text .= "- 日平均對話: {$report['summary']['avg_conversations_per_day']}\n";
        $text .= "- 日平均訊息: {$report['summary']['avg_messages_per_day']}\n\n";

        // 今日狀況
        $text .= "📅 今日狀況 ({$report['today_status']['date']})\n";
        $text .= "- 對話數: {$report['today_status']['conversations']}\n";
        $text .= "- 訊息數: {$report['today_status']['messages']}\n";
        $text .= "- 每對話平均訊息: {$report['today_status']['avg_messages_per_conversation']}\n\n";

        // 趨勢分析
        $text .= "📈 月度趨勢\n";
        $conversationChange = $report['month_comparison']['conversations_change_percent'];
        $messageChange = $report['month_comparison']['messages_change_percent'];
        $text .= "- 對話數變化: {$conversationChange}% (".($conversationChange > 0 ? '↗️' : ($conversationChange < 0 ? '↘️' : '➡️')).")\n";
        $text .= "- 訊息數變化: {$messageChange}% (".($messageChange > 0 ? '↗️' : ($messageChange < 0 ? '↘️' : '➡️')).")\n\n";

        // 使用模式
        if ($patterns['insights']['most_active_weekday']) {
            $text .= "🔄 使用模式\n";
            $text .= "- 最活躍日: {$patterns['insights']['most_active_weekday']}\n";
            $text .= "- 最安靜日: {$patterns['insights']['least_active_weekday']}\n\n";
        }

        // 高峰表現
        if ($report['peak_performance']['highest_conversations_day']) {
            $text .= "🏆 高峰表現\n";
            $peak = $report['peak_performance']['highest_conversations_day'];
            $text .= "- 最高對話日: {$peak['date']} ({$peak['conversations']} 對話)\n";

            $peakMsg = $report['peak_performance']['highest_messages_day'];
            if ($peakMsg) {
                $text .= "- 最高訊息日: {$peakMsg['date']} ({$peakMsg['messages']} 訊息)\n";
            }
        }

        return $text;
    }
}
