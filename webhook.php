<?php
http_response_code(200);

// รับ webhook จาก LINE (log เผื่อ debug)
$input = file_get_contents("php://input");
error_log("RECEIVED: " . $input);

// =======================
// 💬 ส่งข้อความ (Push Message) ไปยัง LINE chat
// =======================
$accessToken = QwkhmeW5/XhOlWWY4ZaXueRYo9NxvCoU9A7fO4XxFw4f5lBZdoODXaUdmYEH3htQi7zzG+EclPjqyQl9WdRSWP6YTNPONKhXPpc//vl76cbAefExvKXoSlP8AYfDCwfObIv+Vrg/x1SK93y59piIdAdB04t89/1O/w1cDnyilFU=";
$userId = "U07753617368febe0b8a358f2caf23650"; // ✅ user ของคุณ


$messageData = [
    "to" => $userId,
    "messages" => [
        [
            "type" => "text",
            "text" => "hello from Tony 🔥"
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/message/push");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));

$response = curl_exec($ch);
curl_close($ch);

// ส่ง webhook response back ไปที่ LINE Developer Console
echo "hello"; // 👈 ไม่เกี่ยวกับข้อความที่ส่งเข้าห้องแชท
?>
