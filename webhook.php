<?php
http_response_code(200);
$data = file_get_contents("php://input");
error_log("RECEIVED: " . $data);
echo "hello"; // LINE จะได้รับ response body นี้
?>
