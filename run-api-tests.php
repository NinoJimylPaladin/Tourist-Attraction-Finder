<?php

// API Test Runner
// This script runs comprehensive tests on the Tourist Attraction Finder API

require_once 'vendor/autoload.php';

use TouristAttractionFinder\Presentation\Controllers\TestController;
use TouristAttractionFinder\Presentation\Controllers\HealthController;

class APITestRunner
{
    private TestController $testController;
    private HealthController $healthController;
    private array $results = [];

    public function __construct()
    {
        $this->testController = new TestController();
        $this->healthController = new HealthController();
    }

    public function runAllTests(): array
    {
        echo "🚀 Starting Tourist Attraction Finder API Test Suite\n";
        echo "================================================\n\n";

        // Test 1: Health Check
        echo "1. Testing Health Endpoints...\n";
        $this->testHealthEndpoints();

        // Test 2: Full API Test Suite
        echo "2. Running Full API Test Suite...\n";
        $this->testFullSuite();

        // Test 3: Manual Endpoint Testing
        echo "3. Testing Individual Endpoints...\n";
        $this->testIndividualEndpoints();

        // Generate Report
        return $this->generateReport();
    }

    private function testHealthEndpoints(): void
    {
        try {
            // Test /api/health
            $healthResult = $this->healthController->check();
            $this->results['health_check'] = [
                'status' => $healthResult['status'] === 'healthy' ? 'passed' : 'failed',
                'message' => 'Health check endpoint working',
                'details' => $healthResult
            ];

            // Test /api/info
            $infoResult = $this->healthController->info();
            $this->results['api_info'] = [
                'status' => isset($infoResult['api_name']) ? 'passed' : 'failed',
                'message' => 'API info endpoint working',
                'details' => $infoResult
            ];

            // Test /api/metrics
            $metricsResult = $this->healthController->metrics();
            $this->results['api_metrics'] = [
                'status' => isset($metricsResult['timestamp']) ? 'passed' : 'failed',
                'message' => 'API metrics endpoint working',
                'details' => $metricsResult
            ];

        } catch (\Exception $e) {
            $this->results['health_check'] = [
                'status' => 'failed',
                'message' => 'Health endpoints failed: ' . $e->getMessage()
            ];
        }
    }

    private function testFullSuite(): void
    {
        try {
            $fullTestResult = $this->testController->runFullTest();

            $this->results['full_test_suite'] = [
                'status' => $fullTestResult['summary']['overall_status'] === 'all_passed' ? 'passed' : 'failed',
                'message' => 'Full test suite completed',
                'details' => $fullTestResult
            ];

        } catch (\Exception $e) {
            $this->results['full_test_suite'] = [
                'status' => 'failed',
                'message' => 'Full test suite failed: ' . $e->getMessage()
            ];
        }
    }

    private function testIndividualEndpoints(): void
    {
        $endpoints = [
            ['GET', '/api/status'],
            ['GET', '/api/health'],
            ['GET', '/api/info'],
            ['GET', '/api/metrics'],
            ['GET', '/api/docs'],
            ['GET', '/api/test/full'],
            ['POST', '/api/test/endpoint', ['method' => 'GET', 'path' => '/api/status']]
        ];

        foreach ($endpoints as $endpoint) {
            $method = $endpoint[0];
            $path = $endpoint[1];
            $data = $endpoint[2] ?? [];

            try {
                $result = $this->testController->testEndpoint($method, $path, $data);

                $this->results['endpoint_tests'][$path] = [
                    'status' => $result['success'] ? 'passed' : 'failed',
                    'message' => "Endpoint $method $path",
                    'details' => $result
                ];
            } catch (\Exception $e) {
                $this->results['endpoint_tests'][$path] = [
                    'status' => 'failed',
                    'message' => "Endpoint $method $path failed: " . $e->getMessage()
                ];
            }
        }
    }

    private function generateReport(): array
    {
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'api_name' => 'Tourist Attraction Finder API',
            'version' => '1.0.0',
            'test_summary' => [
                'total_tests' => 0,
                'passed_tests' => 0,
                'failed_tests' => 0,
                'success_rate' => 0
            ],
            'test_results' => $this->results,
            'recommendations' => []
        ];

        // Calculate summary
        $total = 0;
        $passed = 0;

        foreach ($this->results as $category => $tests) {
            if ($category === 'endpoint_tests') {
                foreach ($tests as $test) {
                    $total++;
                    if ($test['status'] === 'passed') $passed++;
                }
            } else {
                $total++;
                if ($tests['status'] === 'passed') $passed++;
            }
        }

        $report['test_summary']['total_tests'] = $total;
        $report['test_summary']['passed_tests'] = $passed;
        $report['test_summary']['failed_tests'] = $total - $passed;
        $report['test_summary']['success_rate'] = round(($passed / $total) * 100, 2);

        // Generate recommendations
        $report['recommendations'] = $this->generateRecommendations();

        return $report;
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];

        // Check for failed tests
        foreach ($this->results as $category => $tests) {
            if ($category === 'endpoint_tests') {
                foreach ($tests as $endpoint => $test) {
                    if ($test['status'] === 'failed') {
                        $recommendations[] = "Fix endpoint: $endpoint";
                    }
                }
            } else {
                if ($tests['status'] === 'failed') {
                    $recommendations[] = "Investigate: $category";
                }
            }
        }

        // Add general recommendations
        if (empty($recommendations)) {
            $recommendations[] = "All tests passed! Consider adding more integration tests.";
            $recommendations[] = "Consider implementing rate limiting for production.";
            $recommendations[] = "Add logging for better monitoring.";
        } else {
            $recommendations[] = "Review database connection and configuration.";
            $recommendations[] = "Check JWT secret key configuration.";
            $recommendations[] = "Verify environment variables are set correctly.";
        }

        return $recommendations;
    }

    public function printReport(): void
    {
        $report = $this->runAllTests();

        echo "\n📊 API Test Report\n";
        echo "==================\n\n";

        echo "Summary:\n";
        echo "- Total Tests: {$report['test_summary']['total_tests']}\n";
        echo "- Passed: {$report['test_summary']['passed_tests']}\n";
        echo "- Failed: {$report['test_summary']['failed_tests']}\n";
        echo "- Success Rate: {$report['test_summary']['success_rate']}%\n\n";

        echo "Test Results:\n";
        foreach ($report['test_results'] as $category => $tests) {
            echo "\n$category:\n";
            if ($category === 'endpoint_tests') {
                foreach ($tests as $endpoint => $test) {
                    $status = $test['status'] === 'passed' ? '✅' : '❌';
                    echo "  $status $endpoint\n";
                }
            } else {
                $status = $tests['status'] === 'passed' ? '✅' : '❌';
                echo "  $status $category\n";
            }
        }

        echo "\nRecommendations:\n";
        foreach ($report['recommendations'] as $recommendation) {
            echo "- $recommendation\n";
        }

        echo "\n🏁 Test Suite Complete!\n";
    }
}

// Run the tests
$runner = new APITestRunner();
$runner->printReport();