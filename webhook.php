<?php
http_response_code(200);

// รับ LINE webhook JSON
$input = file_get_contents("php://input");
error_log("RECEIVED: " . $input);

// decode JSON
$data = json_decode($input, true);
if (!isset($data['events'][0])) {
    echo "No events"; 
    exit;
}

// ดึงข้อมูลจาก event แรก
$event = $data['events'][0];
$lineUid = $event['source']['userId'] ?? null;
$sourceType = $event['source']['type'] ?? $event['source']['type'] ?? 'user';
$sourceId = $event['source']['groupId'] ?? ($event['source']['roomId'] ?? null);
$msgType = $event['message']['type'] ?? 'unknown';
$messageText = $event['message']['text'] ?? null;
$eventTs = $event['timestamp'] ?? 0;

// ------------ Connect MySQL ------------
$conn = new mysqli(
    "srv577.hstgr.io",
    "u164091347_admin",   // 👈 เปลี่ยนเป็นของคุณ
    "4aA#IZLN1",   // 👈 เปลี่ยนเป็นของคุณ
    "u164091347_db_master"
);



if ($conn->connect_error) {
    error_log("DB Connect Error: " . $conn->connect_error);
    echo "DB connection failed";
    exit;
}

// Insert SQL (Hybrid: relational + raw JSON)
$stmt = $conn->prepare(
    "INSERT INTO line_messages
    (line_uid, source_type, source_id, msg_type, message, event_ts, raw_payload)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$jsonPayload = json_encode($event, JSON_UNESCAPED_UNICODE); // เก็บทั้ง events[].message object
$stmt->bind_param(
    "sssssis",
    $lineUid,
    $sourceType,
    $sourceId,
    $msgType,
    $messageText,
    $eventTs,
    $jsonPayload
);

$stmt->execute();
if ($stmt->error) {
    error_log("INSERT ERROR: " . $stmt->error);
}

$stmt->close();
$conn->close();

echo "OK";
