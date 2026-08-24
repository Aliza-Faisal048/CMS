<?php

function getAMSAssets($type = null)
{
    $baseUrl = "https://ams-production-bd97.up.railway.app/api/assets.php";

    if ($type !== null) {
        $url = $baseUrl . "?type=" . urlencode($type);
    } else {
        $url = $baseUrl;
    }

    $apiKey = getenv("AMS_API_KEY");

    if (!$apiKey) {
        return [
            "success" => false,
            "message" => "AMS_API_KEY is not configured"
        ];
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
        $error = curl_error($ch);

        curl_close($ch);

        return [
            "success" => false,
            "message" => "AMS API connection failed",
            "error" => $error
        ];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $data = json_decode($response, true);

    if ($data === null) {
        return [
            "success" => false,
            "message" => "Invalid JSON response from AMS",
            "http_code" => $httpCode
        ];
    }

    if ($httpCode !== 200) {
        return [
            "success" => false,
            "message" => $data["message"] ?? "AMS API request failed",
            "http_code" => $httpCode
        ];
    }

    return $data;
}