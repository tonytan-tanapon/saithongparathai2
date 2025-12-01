const express = require("express");
const app = express();

// ให้ server อ่าน body เป็น text/json ได้หมด
app.use(express.json());
app.use(express.text());

app.post("/webhook", async (req, res) => {
  console.log("📥 Webhook received:", req.body);

  // ====== LINE Push Message demo ======
  const lineToken =
    "QwkhmeW5/XhOlWWY4ZaXueRYo9NxvCoU9A7fO4XxFw4f5lBZdoODXaUdmYEH3htQi7zzG+EclPjqyQl9WdRSWP6YTNPONKhXPpc//vl76cbAefExvKXoSlP8AYfDCwfObIv+Vrg/x1SK93y59piIdAdB04t89/1O/w1cDnyilFU=";
  const lineUser = "U07753617368febe0b8a358f2caf23650";

  await fetch("https://api.line.me/v2/bot/message/push", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: "Bearer " + lineToken,
    },
    body: JSON.stringify({
      to: lineUser,
      messages: [{ type: "text", text: "🚀 Hello from your webhook server!" }],
    }),
  });

  // ต้องตอบ 200 OK ให้ LINE/GitHub เสมอ
  res.status(200).send("OK");
});

app.get("/", (req, res) => {
  res.json({ message: "Webhook server is running ✅" });
});

// รัน server ที่ port 8000
app.listen(8000, () => console.log("✅ Webhook server running on 8000"));
