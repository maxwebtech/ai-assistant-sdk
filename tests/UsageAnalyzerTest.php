<?php

declare(strict_types=1);

namespace MaxWebTech\AiAssistant\Tests;

use Exception;
use MaxWebTech\AiAssistant\AiAssistantSDK;
use MaxWebTech\AiAssistant\UsageAnalyzer;
use PHPUnit\Framework\TestCase;

class UsageAnalyzerTest extends TestCase
{
    private AiAssistantSDK $sdk;
    private UsageAnalyzer $analyzer;
    private string $testJwt;
    private string $testUserId;

    protected function setUp(): void
    {
        $this->sdk = $this->createMock(AiAssistantSDK::class);
        $this->testJwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.test';
        $this->testUserId = 'test_user_123';
        
        $this->analyzer = new UsageAnalyzer($this->sdk, $this->testJwt, $this->testUserId);
    }

    public function test_today_summary_calculation()
    {
        // Mock today's usage data
        $todayData = [
            'date' => '2025-09-02',
            'conversations' => 10,
            'messages' => 50,
            'unique_users' => 1,
        ];

        $this->sdk->expects($this->once())
            ->method('getTodayUsage')
            ->with($this->testJwt, $this->testUserId)
            ->willReturn($todayData);

        $result = $this->analyzer->todaySummary();

        $this->assertEquals('2025-09-02', $result['date']);
        $this->assertEquals(10, $result['conversations']);
        $this->assertEquals(50, $result['messages']);
        $this->assertEquals(5.0, $result['avg_messages_per_conversation']);
    }

    public function test_today_summary_with_zero_conversations()
    {
        $todayData = [
            'date' => '2025-09-02',
            'conversations' => 0,
            'messages' => 0,
            'unique_users' => 0,
        ];

        $this->sdk->expects($this->once())
            ->method('getTodayUsage')
            ->with($this->testJwt, $this->testUserId)
            ->willReturn($todayData);

        $result = $this->analyzer->todaySummary();

        $this->assertEquals(0, $result['avg_messages_per_conversation']);
    }

    public function test_this_month_summary_calculation()
    {
        $monthData = [
            'month' => '2025-09',
            'total_conversations' => 150,
            'total_messages' => 600,
            'unique_users' => 10,
        ];

        $this->sdk->expects($this->once())
            ->method('getThisMonthUsage')
            ->with($this->testJwt, $this->testUserId)
            ->willReturn($monthData);

        $result = $this->analyzer->thisMonthSummary();

        $this->assertEquals('2025-09', $result['month']);
        $this->assertEquals(150, $result['total_conversations']);
        $this->assertEquals(4.0, $result['avg_messages_per_conversation']);
        $this->assertGreaterThan(0, $result['daily_average_conversations']);
        $this->assertGreaterThan(0, $result['daily_average_messages']);
    }

    public function test_usage_comparison_with_growth()
    {
        $currentMonthData = [
            'month' => '2025-09',
            'total_conversations' => 150,
            'total_messages' => 600,
            'unique_users' => 10,
        ];

        $previousMonthData = [
            'month' => '2025-08',
            'total_conversations' => 100,
            'total_messages' => 400,
            'unique_users' => 8,
        ];

        $this->sdk->expects($this->exactly(2))
            ->method('getMonthlyUsage')
            ->withConsecutive(
                [date('Y-m'), $this->testJwt, $this->testUserId],
                [date('Y-m', strtotime('-1 month')), $this->testJwt, $this->testUserId]
            )
            ->willReturnOnConsecutiveCalls($currentMonthData, $previousMonthData);

        $result = $this->analyzer->getUsageComparison();

        $this->assertEquals($currentMonthData, $result['current_month']);
        $this->assertEquals($previousMonthData, $result['previous_month']);
        $this->assertEquals(50.0, $result['changes']['conversations_change_percent']); // 50% increase
        $this->assertEquals(50.0, $result['changes']['messages_change_percent']); // 50% increase
        $this->assertEquals('increase', $result['changes']['conversation_growth']);
        $this->assertEquals('increase', $result['changes']['message_growth']);
    }

    public function test_usage_comparison_with_decline()
    {
        $currentMonthData = [
            'month' => '2025-09',
            'total_conversations' => 80,
            'total_messages' => 320,
            'unique_users' => 6,
        ];

        $previousMonthData = [
            'month' => '2025-08',
            'total_conversations' => 100,
            'total_messages' => 400,
            'unique_users' => 8,
        ];

        $this->sdk->expects($this->exactly(2))
            ->method('getMonthlyUsage')
            ->withConsecutive(
                [date('Y-m'), $this->testJwt, $this->testUserId],
                [date('Y-m', strtotime('-1 month')), $this->testJwt, $this->testUserId]
            )
            ->willReturnOnConsecutiveCalls($currentMonthData, $previousMonthData);

        $result = $this->analyzer->getUsageComparison();

        $this->assertEquals(-20.0, $result['changes']['conversations_change_percent']); // 20% decrease
        $this->assertEquals(-20.0, $result['changes']['messages_change_percent']); // 20% decrease
        $this->assertEquals('decrease', $result['changes']['conversation_growth']);
        $this->assertEquals('decrease', $result['changes']['message_growth']);
    }

    public function test_weekly_analysis()
    {
        $weeklyData = [
            'week_start' => '2025-09-01',
            'week_end' => '2025-09-07',
            'total_conversations' => 70,
            'total_messages' => 280,
            'total_days' => 7,
            'daily_usage' => [
                ['date' => '2025-09-01', 'conversations' => 10, 'messages' => 40],
                ['date' => '2025-09-02', 'conversations' => 15, 'messages' => 60],
                ['date' => '2025-09-03', 'conversations' => 12, 'messages' => 48],
                ['date' => '2025-09-04', 'conversations' => 8, 'messages' => 32],
                ['date' => '2025-09-05', 'conversations' => 20, 'messages' => 80], // Most active
                ['date' => '2025-09-06', 'conversations' => 5, 'messages' => 20],
                ['date' => '2025-09-07', 'conversations' => 0, 'messages' => 0], // Inactive day
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('getThisWeekUsage')
            ->with($this->testJwt, $this->testUserId)
            ->willReturn($weeklyData);

        $result = $this->analyzer->getWeeklyAnalysis();

        $this->assertEquals(70, $result['totals']['conversations']);
        $this->assertEquals(280, $result['totals']['messages']);
        $this->assertEquals(6, $result['active_days_count']); // 6 active days (excluding the 0 day)
        
        // Most active day should be 2025-09-05 (20 conversations + 80 messages = 100 total activity)
        $this->assertEquals('2025-09-05', $result['most_active_day']['date']);
        $this->assertEquals(20, $result['most_active_day']['conversations']);
        $this->assertEquals(80, $result['most_active_day']['messages']);
        
        // Check averages are calculated correctly (70 conversations / 6 active days = 11.67)
        $this->assertEquals(11.67, $result['averages']['daily_conversations']);
        $this->assertEquals(46.67, $result['averages']['daily_messages']);
    }

    public function test_usage_projection()
    {
        // Mock trend data
        $trendData = [
            'period' => ['start' => '2025-08-03', 'end' => '2025-09-02'],
            'averages' => [
                'last_7_days_conversations' => 12.5,
                'last_7_days_messages' => 50.0,
            ],
        ];

        // Mock this month data
        $thisMonthData = [
            'month' => '2025-09',
            'total_conversations' => 25, // 2 days worth so far
            'total_messages' => 100,
            'unique_users' => 5,
        ];

        $this->sdk->expects($this->once())
            ->method('getUsageTrend')
            ->with($this->testJwt, $this->testUserId)
            ->willReturn($trendData);

        $this->sdk->expects($this->once())
            ->method('getThisMonthUsage')
            ->with($this->testJwt, $this->testUserId)
            ->willReturn($thisMonthData);

        $result = $this->analyzer->getUsageProjection();

        $this->assertEquals(25, $result['current_month_actual']['conversations']);
        $this->assertEquals(100, $result['current_month_actual']['messages']);
        $this->assertEquals(12.5, $result['recent_daily_average']['conversations']);
        $this->assertEquals(50.0, $result['recent_daily_average']['messages']);
        
        // Check projected totals
        $currentDay = (int)date('j');
        $daysInMonth = (int)date('t');
        $remainingDays = $daysInMonth - $currentDay;
        
        $expectedConversations = 25 + (12.5 * $remainingDays);
        $expectedMessages = 100 + (50.0 * $remainingDays);
        
        $this->assertEquals($expectedConversations, $result['projected_month_end']['conversations']);
        $this->assertEquals($expectedMessages, $result['projected_month_end']['messages']);
        $this->assertEquals($remainingDays, $result['projected_month_end']['remaining_days']);
    }

    public function test_text_report_generation()
    {
        // Mock multiple method calls for report generation
        $reportData = [
            'report_period' => ['start' => '2025-08-03', 'end' => '2025-09-02'],
            'summary' => [
                'total_conversations' => 300,
                'total_messages' => 1200,
                'avg_conversations_per_day' => 10.0,
                'avg_messages_per_day' => 40.0,
            ],
            'today_status' => [
                'date' => '2025-09-02',
                'conversations' => 15,
                'messages' => 60,
                'avg_messages_per_conversation' => 4.0,
            ],
            'month_comparison' => [
                'conversations_change_percent' => 20.5,
                'messages_change_percent' => 15.3,
            ],
            'peak_performance' => [
                'highest_conversations_day' => [
                    'date' => '2025-08-15',
                    'conversations' => 25,
                ],
                'highest_messages_day' => [
                    'date' => '2025-08-20',
                    'messages' => 100,
                ],
            ],
        ];

        $patternsData = [
            'insights' => [
                'most_active_weekday' => 'Tuesday',
                'least_active_weekday' => 'Sunday',
            ],
        ];

        $this->sdk->method('getUsageTrend')->willReturn([]);
        $this->sdk->method('getThisWeekUsage')->willReturn([]);
        $this->sdk->method('getTodayUsage')->willReturn($reportData['today_status']);
        $this->sdk->method('getThisMonthUsage')->willReturn([]);
        $this->sdk->method('getMonthlyUsage')->willReturn([]);

        // Create a partial mock of the analyzer to mock internal method calls
        $analyzer = $this->getMockBuilder(UsageAnalyzer::class)
            ->setConstructorArgs([$this->sdk, $this->testJwt, $this->testUserId])
            ->onlyMethods(['generateReport', 'getUsagePatterns'])
            ->getMock();

        $analyzer->expects($this->once())
            ->method('generateReport')
            ->willReturn($reportData);

        $analyzer->expects($this->once())
            ->method('getUsagePatterns')
            ->willReturn($patternsData);

        $textReport = $analyzer->getTextReport();

        $this->assertStringContainsString('使用量分析報告', $textReport);
        $this->assertStringContainsString('總對話數: 300', $textReport);
        $this->assertStringContainsString('總訊息數: 1200', $textReport);
        $this->assertStringContainsString('今日狀況', $textReport);
        $this->assertStringContainsString('對話數: 15', $textReport);
        $this->assertStringContainsString('最活躍日: Tuesday', $textReport);
        $this->assertStringContainsString('20.5%', $textReport);
    }

    public function test_analyzer_creation_through_sdk()
    {
        $realSdk = new AiAssistantSDK([
            'widget_token' => 'test_token',
            'api_url' => 'http://test-api.com',
        ]);

        $analyzer = $realSdk->createUsageAnalyzer('test_jwt', 'test_user');

        $this->assertInstanceOf(UsageAnalyzer::class, $analyzer);
    }
}