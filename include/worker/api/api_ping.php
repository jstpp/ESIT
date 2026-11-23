<?php

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $tag = base64_decode($data['tag']);

    $content = json_decode(openssl_decrypt($data['content'], 'AES-128-GCM', $worker_network_private_key, $options = 0, base64_decode($data['nonce']), $tag), true);

if (!isset($content['ip_addr'])) {
    header("HTTP/1.0 403 Forbidden");
    die;
}

try {
    if (
        isset(
            $content['device_type'],
            $content['device_name'],
            $content['ip_addr'],
            $content['cpu_total'],
            $content['cpu_free'],
            $content['mem_total'],
            $content['mem_free'],
            $content['heartbeat'],
            $content['misc']
        )
    ) {
        $db_query = $pdo->prepare("INSERT INTO NETWORK_LOG (device_type, device_name, ip_address, cpu_total, cpu_free, mem_total, mem_free, heartbeat, misc) VALUES (:dev_type, :dev_name, :ip, :cpu_total, :cpu_free, :mem_total, :mem_free, :heartbeat, :misc)");
        $db_query->execute(['dev_type' => $content['device_type'], 'dev_name' => $content['device_name'], 'ip' => $content['ip_addr'], 'cpu_total' => $content['cpu_total'], 'cpu_free' => $content['cpu_free'], 'mem_total' => $content['mem_total'], 'mem_free' => $content['mem_free'], 'heartbeat' => $content['heartbeat'], 'misc' => $content['misc']]);
    }
    http_response_code(200);
} catch (Throwable $t) {
    http_response_code(500);
    extended_exception_handler($t);
    die;
}
