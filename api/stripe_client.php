<?php
function stripe_api_request($secretKey, $method, $path, $params = null)
{
    $secretKey = trim((string)$secretKey);
    if ($secretKey === '') {
        return ['ok' => false, 'status' => 0, 'error' => 'Secret key ausente'];
    }

    $url = 'https://api.stripe.com' . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper((string)$method));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    if ($params !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        return ['ok' => false, 'status' => 0, 'error' => $err ?: 'Falha na requisição'];
    }

    $json = json_decode($raw, true);
    if ($status >= 200 && $status < 300 && is_array($json)) {
        return ['ok' => true, 'status' => $status, 'data' => $json, 'raw' => $raw];
    }

    $msg = 'Erro Stripe';
    if (is_array($json) && isset($json['error']['message'])) {
        $msg = (string)$json['error']['message'];
    }
    return ['ok' => false, 'status' => $status, 'error' => $msg, 'data' => is_array($json) ? $json : null, 'raw' => $raw];
}

function stripe_verify_signature($payload, $sigHeader, $secret, $toleranceSeconds = 300)
{
    $payload = (string)$payload;
    $sigHeader = (string)$sigHeader;
    $secret = (string)$secret;
    if ($payload === '' || $sigHeader === '' || $secret === '') return false;

    $parts = [];
    foreach (explode(',', $sigHeader) as $kv) {
        $kv = trim($kv);
        if ($kv === '') continue;
        $pair = explode('=', $kv, 2);
        if (count($pair) !== 2) continue;
        $parts[trim($pair[0])] = trim($pair[1]);
    }

    $timestamp = isset($parts['t']) ? (int)$parts['t'] : 0;
    $v1 = isset($parts['v1']) ? (string)$parts['v1'] : '';
    if ($timestamp <= 0 || $v1 === '') return false;

    if (abs(time() - $timestamp) > (int)$toleranceSeconds) return false;

    $signed = $timestamp . '.' . $payload;
    $expected = hash_hmac('sha256', $signed, $secret);
    return hash_equals($expected, $v1);
}
