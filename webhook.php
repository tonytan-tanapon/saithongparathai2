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
$sourceType = $event['source']['type'] ?? 'user';
$sourceId = $event['source']['groupId'] ?? ($event['source']['roomId'] ?? null);
$msgType = $event['message']['type'] ?? 'unknown';
$messageText = $event['message']['text'] ?? null;
$eventTs = $event['timestamp'] ?? 0;
$replyToken = $event['replyToken'] ?? null;

// ------------ Connect MySQL (Hostinger) ------------
$conn = new mysqli(
    "srv577.hstgr.io",
    "u164091347_admin",
    "4aA#IZLN1",
    "u164091347_db_master"
);

if ($conn->connect_error) {
    error_log("DB Connect Error: " . $conn->connect_error);
    echo "DB connection failed";
    exit;
}

// ✅ Insert ลง DB (แก้ column name ให้ตรง table ของคุณ)
$stmt = $conn->prepare(
    "INSERT INTO line_messages
    (line_uid, source_type, source_id, msg_type, message, event_ts, raw_payload)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$jsonPayload = json_encode($event, JSON_UNESCAPED_UNICODE);

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
error_log("INSERT DB STATUS: " . ($stmt->error ?: "success"));
$stmt->close();
$conn->close();

// ------------ ส่งกลับไปที่ LINE chat ด้วย Messaging API (Reply) ------------
if ($replyToken) {
    $channelAccessToken = "QwkhmeW5/XhOlWWY4ZaXueRYo9NxvCoU9A7fO4XxFw4f5lBZdoODXaUdmYEH3htQi7zzG+EclPjqyQl9WdRSWP6YTNPONKhXPpc//vl76cbAefExvKXoSlP8AYfDCwfObIv+Vrg/x1SK93y59piIdAdB04t89/1O/w1cDnyilFU="; // 👈 ต้องใช้ token ของ LINE channel

    $replyData = [
        "replyToken" => $replyToken,
        "messages" => [
            [
                "type" => "text",
                "text" => "✅ บันทึกลง database แล้ว! ข้อความที่ได้รับคือ: $messageText 🚀"
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $channelAccessToken"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($replyData, JSON_UNESCAPED_UNICODE));

    $response = curl_exec($ch);
    error_log("LINE REPLY API RESPONSE: " . $response);
    curl_close($ch);
}

echo "OK";
?>
