<?php
    header('Content-Type: application/json');

    if (!is_logged_in() || !has_a_priority(3)) {
        header('HTTP/1.1 403 Forbidden');
		die;
    }

    $channel_id = filter_var($_GET['cid'], FILTER_VALIDATE_INT);
    
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);

    if ($channel_id && $data && isset($data['content'])) {
        $clean_json = json_encode($data);

        $stmt = $pdo->prepare('UPDATE CHANNELS SET layout=:layout WHERE CHANNEL_ID=:cid');
        $result = $stmt->execute([
            'layout' => $clean_json,
            'cid'    => $channel_id
        ]);

        if ($result) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Input error']);
    }
    exit;

?>