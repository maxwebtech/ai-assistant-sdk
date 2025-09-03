<?php

declare(strict_types=1);

namespace MaxWebTech\AiAssistant\Tests;

use Exception;
use MaxWebTech\AiAssistant\AiAssistantSDK;
use PHPUnit\Framework\TestCase;

class UsageSDKTest extends TestCase
{
    private AiAssistantSDK $sdk;

    private string $testJwt;

    private string $testUserId;

    protected function setUp(): void
    {
        $this->sdk = $this->getMockBuilder(AiAssistantSDK::class)
            ->setConstructorArgs([[
                'widget_token' => 'test_token',
                'api_url' => 'http://test-api.com',
            ]])
            ->onlyMethods(['makeApiRequestWithJWT'])
            ->getMock();

        $this->testJwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.test';
        $this->testUserId = 'test_user_123';
    }

    public function test_get_monthly_usage_all_users()
    {
        $expectedResponse = [
            'success' => true,
            'data' => [
                'month' => '2025-09',
                'total_conversations' => 150,
                'total_messages' => 600,
                'unique_users' => 10,
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/monthly', ['month' => '2025-09'], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getMonthlyUsage('2025-09', $this->testJwt);

        $this->assertEquals($expectedResponse['data'], $result);
    }

    public function test_get_monthly_usage_specific_user()
    {
        $expectedResponse = [
            'success' => true,
            'data' => [
                'month' => '2025-09',
                'total_conversations' => 50,
                'total_messages' => 200,
                'unique_users' => 1,
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/monthly', [
                'month' => '2025-09',
                'user_id' => $this->testUserId,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getMonthlyUsage('2025-09', $this->testJwt, $this->testUserId);

        $this->assertEquals($expectedResponse['data'], $result);
    }

    public function test_get_daily_usage_date_range()
    {
        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-07',
                'daily_usage' => [
                    ['date' => '2025-09-01', 'conversations' => 10, 'messages' => 40, 'unique_users' => 2],
                    ['date' => '2025-09-02', 'conversations' => 15, 'messages' => 60, 'unique_users' => 3],
                    ['date' => '2025-09-03', 'conversations' => 12, 'messages' => 48, 'unique_users' => 2],
                    ['date' => '2025-09-04', 'conversations' => 8, 'messages' => 32, 'unique_users' => 1],
                    ['date' => '2025-09-05', 'conversations' => 20, 'messages' => 80, 'unique_users' => 4],
                    ['date' => '2025-09-06', 'conversations' => 5, 'messages' => 20, 'unique_users' => 1],
                    ['date' => '2025-09-07', 'conversations' => 0, 'messages' => 0, 'unique_users' => 0],
                ],
                'summary' => [
                    'total_conversations' => 70,
                    'total_messages' => 280,
                    'total_days' => 7,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-07',
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getDailyUsage('2025-09-01', '2025-09-07', $this->testJwt);

        $this->assertEquals($expectedResponse['data'], $result);
        $this->assertIsArray($result['daily_usage']);
        $this->assertCount(7, $result['daily_usage']);
        $this->assertEquals(70, $result['summary']['total_conversations']);
    }

    public function test_get_daily_usage_specific_user()
    {
        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-03',
                'daily_usage' => [
                    ['date' => '2025-09-01', 'conversations' => 5, 'messages' => 20, 'unique_users' => 1],
                    ['date' => '2025-09-02', 'conversations' => 8, 'messages' => 32, 'unique_users' => 1],
                    ['date' => '2025-09-03', 'conversations' => 3, 'messages' => 12, 'unique_users' => 1],
                ],
                'summary' => [
                    'total_conversations' => 16,
                    'total_messages' => 64,
                    'total_days' => 3,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-03',
                'user_id' => $this->testUserId,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getDailyUsage('2025-09-01', '2025-09-03', $this->testJwt, $this->testUserId);

        $this->assertEquals(16, $result['summary']['total_conversations']);
        $this->assertEquals(64, $result['summary']['total_messages']);
    }

    public function test_get_today_usage()
    {
        $today = date('Y-m-d');
        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => $today,
                'end_date' => $today,
                'daily_usage' => [
                    ['date' => $today, 'conversations' => 12, 'messages' => 48, 'unique_users' => 3],
                ],
                'summary' => [
                    'total_conversations' => 12,
                    'total_messages' => 48,
                    'total_days' => 1,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => $today,
                'end_date' => $today,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getTodayUsage($this->testJwt);

        $this->assertEquals($today, $result['date']);
        $this->assertEquals(12, $result['conversations']);
        $this->assertEquals(48, $result['messages']);
        $this->assertEquals(3, $result['unique_users']);
    }

    public function test_get_today_usage_specific_user()
    {
        $today = date('Y-m-d');
        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => $today,
                'end_date' => $today,
                'daily_usage' => [
                    ['date' => $today, 'conversations' => 5, 'messages' => 20, 'unique_users' => 1],
                ],
                'summary' => [
                    'total_conversations' => 5,
                    'total_messages' => 20,
                    'total_days' => 1,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => $today,
                'end_date' => $today,
                'user_id' => $this->testUserId,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getTodayUsage($this->testJwt, $this->testUserId);

        $this->assertEquals(5, $result['conversations']);
        $this->assertEquals(20, $result['messages']);
        $this->assertEquals(1, $result['unique_users']);
    }

    public function test_get_this_month_usage()
    {
        $thisMonth = date('Y-m');
        $expectedResponse = [
            'success' => true,
            'data' => [
                'month' => $thisMonth,
                'total_conversations' => 300,
                'total_messages' => 1200,
                'unique_users' => 25,
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/monthly', ['month' => $thisMonth], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getThisMonthUsage($this->testJwt);

        $this->assertEquals($thisMonth, $result['month']);
        $this->assertEquals(300, $result['total_conversations']);
        $this->assertEquals(1200, $result['total_messages']);
    }

    public function test_get_this_week_usage()
    {
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $today = date('Y-m-d');

        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => $startOfWeek,
                'end_date' => $today,
                'daily_usage' => [
                    ['date' => $startOfWeek, 'conversations' => 15, 'messages' => 60, 'unique_users' => 5],
                    ['date' => date('Y-m-d', strtotime($startOfWeek.' +1 day')), 'conversations' => 12, 'messages' => 48, 'unique_users' => 4],
                    ['date' => $today, 'conversations' => 18, 'messages' => 72, 'unique_users' => 6],
                ],
                'summary' => [
                    'total_conversations' => 45,
                    'total_messages' => 180,
                    'total_days' => 3,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => $startOfWeek,
                'end_date' => $today,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getThisWeekUsage($this->testJwt);

        $this->assertEquals($startOfWeek, $result['week_start']);
        $this->assertEquals($today, $result['week_end']);
        $this->assertEquals(45, $result['total_conversations']);
        $this->assertEquals(180, $result['total_messages']);
        $this->assertIsArray($result['daily_usage']);
    }

    public function test_get_usage_trend()
    {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));

        // Create mock daily usage data for 31 days
        $dailyUsageData = [];
        for ($i = 0; $i <= 30; $i++) {
            $date = date('Y-m-d', strtotime("$startDate +$i days"));
            $conversations = rand(5, 25);
            $messages = $conversations * rand(3, 6);
            $dailyUsageData[] = [
                'date' => $date,
                'conversations' => $conversations,
                'messages' => $messages,
                'unique_users' => rand(1, 5),
            ];
        }

        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily_usage' => $dailyUsageData,
                'summary' => [
                    'total_conversations' => array_sum(array_column($dailyUsageData, 'conversations')),
                    'total_messages' => array_sum(array_column($dailyUsageData, 'messages')),
                    'total_days' => 31,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getUsageTrend($this->testJwt);

        $this->assertEquals($startDate, $result['period']['start']);
        $this->assertEquals($endDate, $result['period']['end']);
        $this->assertIsArray($result['trends']['conversations']);
        $this->assertIsArray($result['trends']['messages']);
        $this->assertIsNumeric($result['averages']['last_7_days_conversations']);
        $this->assertIsNumeric($result['averages']['last_7_days_messages']);
        $this->assertCount(31, $result['daily_usage']);
    }

    public function test_get_usage_trend_with_user_id()
    {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));

        $expectedResponse = [
            'success' => true,
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily_usage' => [
                    ['date' => $startDate, 'conversations' => 5, 'messages' => 20, 'unique_users' => 1],
                    ['date' => $endDate, 'conversations' => 8, 'messages' => 32, 'unique_users' => 1],
                ],
                'summary' => [
                    'total_conversations' => 13,
                    'total_messages' => 52,
                    'total_days' => 2,
                ],
            ],
        ];

        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/daily', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $this->testUserId,
            ], $this->testJwt)
            ->willReturn($expectedResponse);

        $result = $this->sdk->getUsageTrend($this->testJwt, $this->testUserId);

        $this->assertEquals(13, $result['summary']['total_conversations']);
        $this->assertEquals(52, $result['summary']['total_messages']);
    }

    public function test_api_error_handling()
    {
        $this->sdk->expects($this->once())
            ->method('makeApiRequestWithJWT')
            ->with('GET', '/api/usage/monthly', ['month' => '2025-09'], $this->testJwt)
            ->willThrowException(new Exception('API Error (400): Invalid month format'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('API Error (400): Invalid month format');

        $this->sdk->getMonthlyUsage('2025-09', $this->testJwt);
    }

    public function test_response_data_extraction()
    {
        // Test that the SDK properly extracts 'data' from response or returns full response
        $responseWithData = [
            'success' => true,
            'data' => ['month' => '2025-09', 'total_conversations' => 100],
        ];

        $responseWithoutData = ['month' => '2025-09', 'total_conversations' => 100];

        // Test with 'data' key
        $this->sdk->expects($this->exactly(2))
            ->method('makeApiRequestWithJWT')
            ->withConsecutive(
                ['GET', '/api/usage/monthly', ['month' => '2025-09'], $this->testJwt],
                ['GET', '/api/usage/monthly', ['month' => '2025-08'], $this->testJwt]
            )
            ->willReturnOnConsecutiveCalls($responseWithData, $responseWithoutData);

        $result1 = $this->sdk->getMonthlyUsage('2025-09', $this->testJwt);
        $result2 = $this->sdk->getMonthlyUsage('2025-08', $this->testJwt);

        $this->assertEquals($responseWithData['data'], $result1);
        $this->assertEquals($responseWithoutData, $result2);
    }
}
