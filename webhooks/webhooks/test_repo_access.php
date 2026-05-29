<?php
$token = trim(file_get_contents('/home/retaillogimaxind/.github_token'));
$repo_owner = 'Logimax-Technologies';
$repo_name = 'etail_development_src';

// Test 1: Check if repository exists
$url = "https://api.github.com/repos/$repo_owner/$repo_name";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/vnd.github.v3+json',
        'Authorization: token ' . $token,
        'User-Agent: PHP-Test'
    ]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Repository Access Test:\n";
echo "HTTP Code: $http_code\n";
echo "Response: $response\n\n";

// Test 2: Check token permissions
$url = "https://api.github.com/user";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/vnd.github.v3+json',
        'Authorization: token ' . $token,
        'User-Agent: PHP-Test'
    ]
]);

$response = curl_exec($ch);
$user_data = json_decode($response, true);
curl_close($ch);

echo "Token Permissions:\n";
echo "User: " . ($user_data['login'] ?? 'Unknown') . "\n";
echo "Scopes: " . ($_SERVER['HTTP_X_OAUTH_SCOPES'] ?? 'Not available') . "\n";
?>
