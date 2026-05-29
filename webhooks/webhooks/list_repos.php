<?php
$token = trim(file_get_contents('/home/retaillogimaxind/.github_token'));

// List user repositories
$url = "https://api.github.com/user/repos?per_page=100";
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
$repos = json_decode($response, true);
curl_close($ch);

echo "Your accessible repositories:\n";
foreach ($repos as $repo) {
    $visibility = $repo['private'] ? 'private' : 'public';
    echo "- {$repo['full_name']} ({$visibility})\n";
}

// Also list organization repos if any
$url = "https://api.github.com/user/orgs";
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
$orgs = json_decode($response, true);
curl_close($ch);

if (!empty($orgs)) {
    echo "\nYour organizations:\n";
    foreach ($orgs as $org) {
        echo "- {$org['login']}\n";
        
        // List org repos
        $url = "https://api.github.com/orgs/{$org['login']}/repos?per_page=100";
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
        $org_repos = json_decode($response, true);
        curl_close($ch);
        
        if (is_array($org_repos)) {
            foreach ($org_repos as $repo) {
                $visibility = $repo['private'] ? 'private' : 'public';
                echo "  - {$repo['full_name']} ({$visibility})\n";
            }
        }
    }
}
?>
