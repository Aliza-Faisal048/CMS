<?php

function getUMSUsers($role = null)
{
    $apiKey = getenv("UMS_API_KEY");

    if (!$apiKey || !function_exists("curl_init")) {
        return [];
    }

    $url = "https://ums-production-34b4.up.railway.app/api/users.php";

    if ($role !== null) {
        $url .= "?role=" . urlencode($role);
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $apiKey,
            "Accept: application/json"
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);
        return [];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        return [];
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        return [];
    }

    $users = $data["users"] ?? $data["data"]["users"] ?? $data["data"] ?? [];

    if ($role !== null) {
        $users = array_filter($users, function ($user) use ($role) {
            return ($user["role"] ?? "") === $role;
        });
    }

    return is_array($users) ? array_values($users) : [];
}
