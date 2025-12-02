<?php
http_response_code(200);
// รับข้อมูลจาก LINE
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// URL ของ Apps Script (Web App)
$script_url = "https://script.google.com/macros/s/AKfycbxaljimVep8oA7QSq5vnJ6jch8t4hOg4hAADO13RsLAT6E3crJuNgiLD1qggpJOK-yH8Q/exec";

// ส่งข้อมูลต่อไป Apps Script แบบ POST + JSON
$ch = curl_init($script_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// บอก LINE ว่า OK (ต้องตอบ 200)
http_response_code(200);
echo "OK";
