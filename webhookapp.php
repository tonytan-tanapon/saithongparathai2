<?php
http_response_code(200);

// รับข้อมูลจาก LINE
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// ---------------------------------------------------------
// ส่งข้อความตอบกลับหา LINE (Reply API)
// ---------------------------------------------------------
$replyToken = $data['events'][0]['replyToken'];

$replyUrl = "https://api.line.me/v2/bot/message/reply";
$accessToken = "QwkhmeW5/XhOlWWY4ZaXueRYo9NxvCoU9A7fO4XxFw4f5lBZdoODXaUdmYEH3htQi7zzG+EclPjqyQl9WdRSWP6YTNPONKhXPpc//vl76cbAefExvKXoSlP8AYfDCwfObIv+Vrg/x1SK93y59piIdAdB04t89/1O/w1cDnyilFU="; // 👈 ต้องใช้ token ของ LINE channel

$replyData = [
    "replyToken" => $replyToken,
    "messages" => [
        ["type" => "text", "text" => "OK รับข้อมูลแล้วครับ 😊"]
    ]
];

$ch = curl_init($replyUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer {$accessToken}"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($replyData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

// ---------------------------------------------------------
// ส่งข้อมูลต่อไป Apps Script แบบ POST + JSON
// ---------------------------------------------------------
$script_url = "https://script.google.com/macros/s/AKfycbw-RHWreGCNVAxAU0QMPAaTDokBaHLUIZk_PHG1GXup_4IFoOQ35UdvWilCdQVdwYAE/exec";

$ch = curl_init($script_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// ---------------------------------------------------------
// บอก LINE ว่า OK (ต้องตอบ 200)
// ---------------------------------------------------------
http_response_code(200);
echo "OK";