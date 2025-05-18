# 💬 WhatsApp Gateway Dashboard (Laravel + Node.js)

Sistem ini adalah **Frontend WhatsApp Gateway** yang dibangun menggunakan **Laravel 10** dan terhubung dengan server **Node.js (WhatsApp Web API)** untuk mengelola:

- Multi device WhatsApp
- Auto-reply pesan
- Kirim gambar, video, dokumen, dan pesan dari berbagai aplikasi lain
- Monitoring real-time logs via WebSocket

---

## ⚙️ Teknologi yang Digunakan

- 🎯 Laravel 10 (Frontend, REST API & Authorization)
- 🟢 Node.js + `whatsapp-web.js` (Server WA yang powerful — **tidak disertakan di repo ini**)
- 📡 WebSocket (Live log ke dashboard)
- 💾 Bootstrap 5 untuk UI
- 🛡️ API Key per device untuk keamanan dan integrasi lintas aplikasi

---

## 📂 Struktur Proyek

```text
├── app/
│   ├── Http/Controllers/
│   ├── Models/Device.php
│   └── Middleware/VerifyApiKey.php
├── resources/views/device/index.blade.php
├── routes/web.php
├── routes/api.php
├── public/
└── .gitignore
⚠️ Tentang Server WA
Server Node.js tidak termasuk di repo ini karena pengaturannya cukup kompleks.

🛠️ Tapi kalau kamu pengen pakai servernya juga, silakan hubungi saya di Instagram:

📩 @bhara_apm

Server-nya stabil dan support auto-reply, sticker, video, bahkan NSFW (wkwkw).

🧑‍💻 Author
Dibuat dengan ❤️ oleh Bhara
📬 Instagram: @bhara_apm
