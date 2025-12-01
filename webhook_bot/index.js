const express = require("express");
const app = express();

// ให้ server อ่าน body เป็น text/json ได้หมด
app.use(express.json());
app.use(express.text());

app.post("/webhook", async (req, res) => {
  console.log("📥 Webhook received:", req.body);

  // ====== LINE Push Message demo ======
  const lineToken = "ใส่ Channel Access Token ของนายที่ LINE console";
  const lineUser = "ใส่ LINE User/Group ID ที่จะส่งไป";

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
