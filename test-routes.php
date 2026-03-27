#!/usr/bin/env php
<?php
/**
 * Route Testing Script
 * Tests all key application flows
 */

require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Http;
use App\Models\Tenant;

$results = [];

// Test helper
function test_route($name, $method, $url, $data = []) {
    global $results;
    try {
        if ($method === 'GET') {
            $response = Http::get($url);
        } else {
            $response = Http::post($url, $data);
        }
        
        $status = $response->status();
        $success = $status >= 200 && $status < 400;
        
        $results[] = [
            'name' => $name,
            'url' => $url,
            'method' => $method,
            'status' => $status,
            'success' => $success
        ];
        
        return $success ? "✅ PASS" : "❌ FAIL (HTTP {$status})";
    } catch (\Exception $e) {
        $results[] = [
            'name' => $name,
            'url' => $url,
            'method' => $method,
            'error' => $e->getMessage(),
            'success' => false
        ];
        return "❌ ERROR: " . $e->getMessage();
    }
}

echo "════════════════════════════════════════════════════════════════\n";
echo "           ROUTE TESTING - Central App (localhost:8000)\n";
echo "════════════════════════════════════════════════════════════════\n\n";

echo "Testing Central App Routes:\n";
echo "1. Landing page: " . test_route('central-landing', 'GET', 'http://localhost:8000/') . "\n";
echo "2. 127.0.0.1 access: " . test_route('central-127', 'GET', 'http://127.0.0.1:8000/') . "\n";
echo "3. Login page: " . test_route('central-login', 'GET', 'http://localhost:8000/login') . "\n";
echo "4. Register page: " . test_route('central-register', 'GET', 'http://localhost:8000/register') . "\n";
echo "5. Dashboard (protected): " . test_route('central-dashboard', 'GET', 'http://localhost:8000/dashboard') . "\n";

echo "\n════════════════════════════════════════════════════════════════\n";
echo "           ROUTE TESTING - Tenant App (tenant domain:8000)\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$tenants = Tenant::all();
foreach ($tenants as $tenant) {
    echo "Testing Tenant: {$tenant->name} ({$tenant->domain}:8000)\n";
    echo "1. Landing page: " . test_route("tenant-{$tenant->id}-landing", 'GET', "http://{$tenant->domain}:8000/") . "\n";
    echo "2. Login page: " . test_route("tenant-{$tenant->id}-login", 'GET', "http://{$tenant->domain}:8000/login") . "\n";
    echo "3. Register page: " . test_route("tenant-{$tenant->id}-register", 'GET', "http://{$tenant->domain}:8000/register") . "\n";
    echo "4. Accommodations: " . test_route("tenant-{$tenant->id}-accommodations", 'GET', "http://{$tenant->domain}:8000/accommodations") . "\n";
    echo "\n";
}

echo "════════════════════════════════════════════════════════════════\n";
echo "                      TEST SUMMARY\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$passed = array_filter($results, fn($r) => $r['success']);
$failed = array_filter($results, fn($r) => !$r['success']);

echo "✅ Passed: " . count($passed) . "\n";
echo "❌ Failed: " . count($failed) . "\n";
echo "📊 Total:  " . count($results) . "\n\n";

if (!empty($failed)) {
    echo "Failed Tests:\n";
    foreach ($failed as $test) {
        echo "  - {$test['name']}: {$test['url']} (Status: {$test['status']})\n";
        if (isset($test['error'])) {
            echo "    Error: {$test['error']}\n";
        }
    }
} else {
    echo "🎉 All tests passed!\n";
}
