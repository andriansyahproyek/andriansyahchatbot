# Andriansyah Chatbot - Laravel 12 & Google Gemini AI

Aplikasi chatbot modern bernama **Andriansyah Chatbot** berbasis **Laravel 12** yang terintegrasi dengan **Google Gemini AI API** dan penyimpanan riwayat percakapan menggunakan database **MySQL**.

---

## 🚀 Fitur Utama

- **Laravel 12 Framework**: Memanfaatkan arsitektur terbaru Laravel 12.
- **Model Google Gemini**: Mendukung model terkini (seperti `gemini-3.5-flash`, `gemini-3.5-flash-lite`, `gemini-3.8-flash`) dengan performa responsif dan cerdas.
- **Multi-turn Context Conversation**: Gemini mengingat konteks percakapan sebelumnya dalam satu sesi chat.
- **Database MySQL**:
  - Menyimpan setiap sesi obrolan (`conversations`).
  - Menyimpan seluruh riwayat pesan user & AI beserta token (`messages`).
- **Otomatis Judul Percakapan**: Gemini secara cerdas membuat judul percakapan ringkas otomatis berdasarkan topik pesan pertama.
- **Antarmuka Modern & Responsif**:
  - Tampilan ala ChatGPT/Gemini dengan Tailwind CSS.
  - Sidebar daftar riwayat chat, tombol tambah obrolan baru, dan hapus obrolan.
  - Markdown Parser (`marked.js`) & Code Syntax Highlighting (`highlight.js`).
  - Fitur salin pesan/kode ke clipboard.
  - Indikator status mengetik ("Gemini sedang berpikir...").
  - Responsive untuk layar Desktop dan Mobile.

---

## 🛠️ Persyaratan Sistem

- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `curl`, `mbstring`, `openssl`, `json`)
- MySQL Server (>= 8.0 atau MariaDB)
- Composer
- Gemini API Key ([Dapatkan di Google AI Studio](https://aistudio.google.com/app/apikey))

---

## 📂 Struktur File Utama

```
/var/www/gemini-chatbot/
├── app/
│   ├── Http/Controllers/
│   │   └── ChatController.php         # Menangani routing web & API chat/percakapan
│   ├── Models/
│   │   ├── Conversation.php           # Model Eloquent untuk percakapan
│   │   └── Message.php                # Model Eloquent untuk pesan chat
│   └── Services/
│       └── GeminiService.php          # Service integrasi Google Gemini API
├── config/
│   └── gemini.php                     # Konfigurasi model, base URL & prompt sistem
├── database/migrations/
│   ├── ..._create_conversations_table.php
│   └── ..._create_messages_table.php
├── resources/views/chat/
│   └── index.blade.php                # Antarmuka chatbot (Blade + Tailwind + Markdown)
├── routes/
│   └── web.php                        # Rute aplikasi dan endpoint API
└── .env                               # Kredensial database & Gemini API Key
```

---

## ⚙️ Panduan Menjalankan Aplikasi

### 1. Masuk ke direktori proyek
```bash
cd /var/www/gemini-chatbot
```

### 2. Konfigurasi Database & Gemini di `.env`
Pastikan pengaturan di `.env` sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gemini_chatbot
DB_USERNAME=root
DB_PASSWORD=Root123!

GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-3.5-flash
```

### 3. Migrasi Database
Jika belum dilakukan migrasi:
```bash
php artisan migrate
```

### 4. Jalankan Server Development
```bash
php artisan serve
```
Atau jika ingin dapat diakses melalui jaringan lokal / IP publik pada port tertentu:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Buka peramban (browser) di: `http://localhost:8000` atau `http://<IP-SERVER>:8000`.

---

## 🧪 Menjalankan Automated Tests

Aplikasi dilengkapi dengan Feature Tests lengkap untuk memvalidasi alur chat dan persistensi database:

```bash
php artisan test --filter=ChatbotTest
```
