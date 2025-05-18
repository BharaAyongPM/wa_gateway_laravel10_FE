<?php

namespace App\Http\Controllers;

use App\Models\AutoReply;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AutoReplyController extends Controller
{
    public function index()
    {
        $autoReplies = AutoReply::all();
        return view('auto_reply.index', compact('autoReplies'));
    }

    public function create()
    {
        return view('auto_reply.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required',
            'response' => 'required',
            'type' => 'required|in:text,sticker,image',
        ]);

        AutoReply::create($request->all());

        return redirect()->route('auto-reply.index')->with('success', 'Berhasil menambahkan.');
    }

    public function edit(AutoReply $autoReply)
    {
        return view('auto_reply.edit', compact('autoReply'));
    }

    public function update(Request $request, AutoReply $autoReply)
    {
        $request->validate([
            'keyword' => 'required',
            'response' => 'required',
            'type' => 'required|in:text,sticker,image',
        ]);

        $autoReply->update($request->all());

        return redirect()->route('auto-reply.index')->with('success', 'Berhasil diubah.');
    }

    public function destroy(AutoReply $autoReply)
    {
        $autoReply->delete();

        return redirect()->route('auto-reply.index')->with('success', 'Berhasil dihapus.');
    }


    //API
    public function handle(Request $request)
    {
        Log::info('🔥 AutoReplyController triggered', $request->all());
        $from = $request->from;
        $sender = $request->sender;
        $message = $request->message;
        $isGroup = $request->is_group;

        if (!$from || !$message) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }
        // Di AutoReplyController atau bagian trigger handler kamu
        $device = Device::where('status', 'connected')->first(); // atau pakai `where('name', ...)`
        $sessionId = $request->session;
        //triger !bantuan

        if (Str::startsWith(Str::lower($message), '!bantuan')) {
            $text =
                "🧕 *Linda - AI WhatsApp Assistant*\n" .
                "Linda adalah asisten virtual yang siap membantu kamu langsung dari WhatsApp. Dibekali AI dan ratusan integrasi API, Linda bisa:\n\n" .
                "🤖 *Chat dengan AI (Bahasa Indonesia)*\n" .
                "➤ Contoh: `linda apa itu big data`\n\n" .
                "🎞️ *Download Video TikTok*\n" .
                "➤ Contoh: `download video tiktok https://vt.tiktok.com/xxxxx/`\n\n" .
                "📷 *Cari Gambar*\n" .
                "➤ Contoh: `kirim gambar kucing`\n" .
                "➤ Contoh: `pin vestia zeta (pinterest)`\n\n" .
                "🌧️ *Cek Cuaca Wilayah*\n" .
                "➤ Contoh: `info cuaca wonogiri`\n\n" .
                "🕌 *Jadwal Sholat Harian*\n" .
                "➤ Contoh: `cek jadwal solat kota tangerang`\n\n" .
                "📡 *Status Website & Hosting*\n" .
                "➤ Contoh: `cek website google.com`\n" .
                "➤ Contoh: `cek domain bisabola.id`\n\n" .
                "🎓 *Tanya Pak Utsad*\n" .
                "➤ Contoh: `ustad gimana hukum judi?`\n\n" .
                "🧠 *Renungan Islam*\n" .
                "➤ Cukup ketik: `renungan islam`\n\n" .
                "🧠 *Fakta Yang kamu belum tau*\n" .
                "➤ Cukup ketik: `fakta`\n\n" .
                "🧠 *Quote Keren*\n" .
                "➤ Cukup ketik: `quote`\n\n" .
                "🎭 *Sticker Maker & Converter*\n" .
                "➤ Kirim gambar + caption `!sticker`\n" .
                "➤ Balas sticker + `!image` untuk ubah ke gambar\n\n" .
                "💡 *Kirim pesan ini kapan saja dengan* `!bantuan` untuk info fitur terbaru.";

            Http::post('http://127.0.0.1:5000/send-message', [
                'to' => $from,
                'text' => $text,
                'session' => $sessionId,
                'isGroup' => $isGroup,
            ]);

            return response()->json(['status' => 'help_sent']);
        }

        // Cek apakah pesan adalah "cek domain ..."
        if (Str::startsWith(Str::lower($message), 'cek domain')) {
            $domain = trim(Str::after($message, 'cek domain'));

            if ($domain) {
                Http::post('http://127.0.0.1:5000/send-domain-check', [
                    'to' => $from,
                    'domain' => $domain,
                    'session' => $sessionId,
                ]);
            }

            return response()->json(['status' => 'cek_domain_sent']);
        }
        // Cek apakah pesan adalah "ustad ..."
        if (Str::contains(Str::lower($message), 'ustad')) {
            Http::post('http://127.0.0.1:5000/send-muslim-ai', [
                'to' => $from,
                'message' => $message,
                'session' => $sessionId,
            ]);
            return response()->json(['status' => 'muslim_ai_sent']);
        }
        // Cek apakah pesan adalah pinterest
        if (Str::startsWith(Str::lower($message), 'pin ')) {
            $query = trim(Str::after($message, 'pin '));

            Http::post('http://127.0.0.1:5000/send-pinterest', [
                'to' => $from,
                'query' => $query,
                'session' => $sessionId,
            ]);

            return response()->json(['status' => 'pinterest_sent']);
        }
        //cek apakah chat gpt
        if (Str::contains(Str::lower($message), 'linda')) {
            $response = Http::get('https://fastrestapis.fasturl.cloud/aillm/gpt-4', [
                'ask' => $message,
                'style' => 'kamu adalah linda pacar romantis dan bergairah serta seksi dalam menjawab',
            ]);

            if ($response->successful() && isset($response['result'])) {
                $reply = $response['result'];

                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => "💞 \n\n$reply",
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                ]);
            } else {
                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => "❌ Linda sedang diam... (API error)",
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                ]);
            }

            return response()->json(['status' => 'linda_replied']);
        }

        // Cek jika pesan adalah "renungan islam"
        if (Str::startsWith(Str::lower($message), 'renungan islam')) {
            Http::post('http://127.0.0.1:5000/send-renungan', [
                'to' => $from,
                'session' => $sessionId,
            ]);
            return response()->json(['status' => 'renungan_sent']);
        }
        // Cek apakah pesan adalah "cek jadwal solat ..."
        if (Str::startsWith(Str::lower($message), 'cek jadwal solat')) {
            $lokasi = trim(Str::after($message, 'cek jadwal solat'));

            // Validasi: harus ada kata "kota" atau "kab"
            if (!Str::contains(Str::lower($lokasi), ['kota', 'kab'])) {
                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => "❌ Format salah!\nGunakan format seperti:\n\n`cek jadwal solat kota tangerang`\n`cek jadwal solat kab bogor`",
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                ]);
                return response()->json(['status' => 'invalid_format']);
            }

            // Kirim permintaan ke API
            $encoded = urlencode($lokasi);
            $response = Http::get("https://islami.api.akuari.my.id/harijadwalshalat/$encoded");

            if ($response->successful() && $response->json('status') == 'success') {
                $data = $response->json('result');

                $text = "🕌 *Jadwal Salat - {$lokasi}*\n📅 {$data['tanggal']}\n\n";
                $text .= "🕓 Imsak: {$data['imsak']}\n";
                $text .= "🕕 Subuh: {$data['subuh']}\n";
                $text .= "🌅 Terbit: {$data['terbit']}\n";
                $text .= "🌞 Dhuha: {$data['dhuha']}\n";
                $text .= "🕛 Dzuhur: {$data['dzuhur']}\n";
                $text .= "🕒 Ashar: {$data['ashar']}\n";
                $text .= "🌇 Maghrib: {$data['maghrib']}\n";
                $text .= "🌙 Isya: {$data['isya']}\n\n";
                $text .= "📌 Sumber: *akuari.my.id*";

                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => $text,
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                ]);

                return response()->json(['status' => 'solat_sent']);
            } else {
                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => "❌ Gagal mengambil data jadwal solat untuk *$lokasi*.",
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                ]);
                return response()->json(['status' => 'solat_error']);
            }
        }

        // Cek apakah pesan adalah "cek website ..."
        if (Str::startsWith(Str::lower($message), 'cek website')) {
            $website = trim(Str::after($message, 'cek website'));

            if (!empty($website)) {
                $response = Http::get("https://webdowncheck.api.akuari.my.id/api", [
                    'url' => $website
                ]);

                if ($response->successful() && $response->json('success')) {
                    $data = $response->json();
                    $status = $data['response'];
                    $tanggal = $data['date'];

                    $text = "🌐 *Status Website*\n";
                    $text .= "🔗 Website: `$website`\n";
                    $text .= "📡 Status: *$status*\n";
                    $text .= "🕒 Diperiksa: $tanggal\n\n";
                    $text .= "✅ Cek oleh Akuari API";

                    Http::post('http://127.0.0.1:5000/send-message', [
                        'to' => $from,
                        'text' => $text,
                        'session' => $sessionId,
                        'isGroup' => $isGroup,
                    ]);

                    return response()->json(['status' => 'website_checked']);
                }
            }

            // Jika URL tidak valid
            Http::post('http://127.0.0.1:5000/send-message', [
                'to' => $from,
                'text' => "❌ Gagal cek website. Pastikan kamu mengetik seperti ini:\n\n`cek website google.com`",
                'session' => $sessionId,
                'isGroup' => $isGroup,
            ]);
            return response()->json(['status' => 'invalid_url']);
        }
        // Cek apakah pesan adalah "info cuaca"
        if (Str::startsWith(Str::lower($message), 'info cuaca')) {
            Log::info("🧪 Kata kunci awal: $message");

            $wilayahInput = trim(Str::replaceFirst('info cuaca', '', strtolower($message)));
            Log::info("🔍 Clean wilayah: $wilayahInput");
            $wilayah = $this->cariKodeWilayah($wilayahInput);
            // $kode = $this->cariKodeWilayah($wilayahInput);

            if ($wilayah) {
                $kode = $wilayah['kode'];
                $level = $wilayah['level'];

                Log::info("🌐 Mengambil data cuaca dari BMKG untuk kode $level: $kode");

                $res = Http::get("https://api.bmkg.go.id/publik/prakiraan-cuaca?$level=$kode");

                Log::info("📥 Status response BMKG: " . $res->status());

                if ($res->successful()) {
                    $cuaca = $res->json();

                    // ambil cuaca[0][0] dari data[0]
                    $data = collect($cuaca['data'][0]['cuaca'][0] ?? [])->first();

                    if ($data) {
                        Log::info("📊 Data cuaca ditemukan:", $data);

                        $text = "🌤️ *Cuaca $wilayahInput*\n\n";
                        $text .= "🕒 Waktu: " . $data['local_datetime'] . "\n";
                        $text .= "🌡️ Suhu: " . $data['t'] . "°C\n";
                        $text .= "💧 Kelembapan: " . $data['hu'] . "%\n";
                        $text .= "🌬️ Angin: " . $data['ws'] . " km/jam\n";
                        $text .= "☁️ Cuaca: " . $data['weather_desc'] . "\n\n";
                        $text .= "📌 Data dari: *BMKG*";

                        Http::post('http://127.0.0.1:5000/send-message', [
                            'to' => $from,
                            'text' => $text,
                            'session' => $sessionId,
                            'isGroup' => $isGroup,
                        ]);
                    } else {
                        Log::warning("⚠️ Data cuaca kosong atau tidak ditemukan dalam response BMKG.");
                    }
                } else {
                    Log::error("❌ Gagal request ke API BMKG. Status: " . $res->status());
                }
            }


            return response()->json(['status' => 'cuaca_checked']);
        }




        // Cek apakah pesan adalah "download video tiktok"
        if (Str::of($message)->lower()->contains('download video tiktok')) {
            preg_match('/https:\/\/vt\.tiktok\.com\/[A-Za-z0-9]+/', $message, $matches);
            if ($matches && isset($matches[0])) {
                $url = $matches[0];

                // Kirim ke server WA
                Http::post('http://127.0.0.1:5000/send-tiktok', [
                    'to' => $from,
                    'url' => $url,
                    'session' => $sessionId,
                ]);
            }
        }




        if (Str::startsWith(Str::lower($message), 'anime ')) {
            $tag = trim(Str::after(Str::lower($message), 'anime ')); // ambil setelah 'anime '

            Http::post('http://127.0.0.1:5000/send-anime', [
                'to' => $from,
                'tag' => $tag,
                'session' => $sessionId,
            ]);

            return response()->json(['status' => 'anime_sent']);
        }

        //cek apakah pesan adalah nsfw
        if (Str::startsWith(Str::lower($message), 'nsfw ')) {
            $prompt = trim(Str::after($message, 'nsfw '));

            if (!empty($prompt)) {
                Http::post('http://127.0.0.1:5000/send-nsfw', [
                    'to' => $from,
                    'prompt' => $prompt,
                    'session' => $sessionId,
                ]);
            }

            return response()->json(['status' => 'nsfw_sent']);
        }
        //cek apakah pesan adalah animeteks
        if (Str::startsWith(Str::lower($message), 'animeteks ')) {
            $teks = trim(Str::after($message, 'animeteks '));

            if (!empty($teks)) {
                Http::post('http://127.0.0.1:5000/send-animebrat', [
                    'to' => $from,
                    'text' => $teks,
                    'session' => $sessionId,
                ]);
            }

            return response()->json(['status' => 'animebrat_sent']);
        }
        //cek apakah pesan adalah twitter
        if (Str::startsWith(Str::lower($message), 'twitter ')) {
            $url = trim(Str::after($message, 'twitter'));

            if (filter_var($url, FILTER_VALIDATE_URL)) {
                Http::post('http://127.0.0.1:5000/send-twitter-video', [
                    'to' => $from,
                    'url' => $url,
                    'session' => $sessionId,
                ]);
                return response()->json(['status' => 'twitter_video_sent']);
            } else {
                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'session' => $sessionId,
                    'text' => '❌ Format URL tidak valid. Gunakan seperti: twitter https://twitter.com/...',
                ]);
                return response()->json(['status' => 'invalid_url']);
            }
        }
        //cek apakah pesan adalah animex
        if (Str::startsWith(Str::lower($message), 'animex ')) {
            $tag = strtolower(trim(Str::after(Str::lower($message), 'animex '))); // pastikan lowercase juga
            Log::info("📥 Tag NSFW Anime:", ['tag' => $tag]);

            Http::post('http://127.0.0.1:5000/send-anime-nsfw', [
                'to' => $from,
                'tag' => $tag,
                'session' => $sessionId,
            ]);

            return response()->json(['status' => 'anime_nsfw_sent']);
        }


        // return response()->json(['status' => 'no_action']);

        // Cek apakah pesan adalah "kirim gambar"
        else if (strtolower($message) === 'kirim gambar') {
            return response()->json([
                'status' => 'need_keyword',
                'message' => 'Silakan tulis "kirim gambar <keyword>", contoh: kirim gambar kucing'
            ]);
        }




        // Cek apakah pesan adalah "cek harga emas"
        else if (strtolower($message) == 'cek harga emas') {
            $response = Http::get('https://logam-mulia-api.vercel.app/prices/anekalogam');
            $data = $response->json('data')[0]; // Ambil data pertama

            $text = "🏷️ *Harga Logam Mulia - Antam (1 gram)*\n\n";
            $text .= "🪙 Jual : Rp" . number_format($data['sell'], 0, ',', '.') . "\n";
            $text .= "💸 Buyback : Rp" . number_format($data['buy'], 0, ',', '.') . "\n\n";
            $text .= "ℹ️ " . $data['info'] . "\n\n";
            $text .= "🔗 https://www.anekalogam.co.id/id";

            Http::post('http://127.0.0.1:5000/send-message', [
                'to' => $from,
                'text' => $text,
                'session' => $sessionId,
                'isGroup' => $isGroup,
            ]);
        }


        //cek resi shope
        else if (str_contains($message, 'cek resi shope')) {
            Http::post('http://127.0.0.1:5000/send-message', [
                'to' => $from,
                'text' => 'Silakan kirim nomor resi Shopee Express Anda (format: SPXID...)',
                'session' => $sessionId,
                'isGroup' => $isGroup,
            ]);
            return response()->json(['status' => 'wait_resi']);
        }

        // Step 2: Jika pesan berupa kode resi Shopee Express (SPXID...)
        else if (preg_match('/spxid[0-9]+/', $message, $matches)) {
            $awb = strtoupper($matches[0]);
            $response = Http::get("https://api.binderbyte.com/v1/track", [
                'api_key' => '950562911aa06a459d4c60ffef8e75dd2fd0df9b4b0d004b063e04dac38194fb',
                'courier' => 'spx',
                'awb' => $awb
            ]);

            if ($response->successful() && $response->json('status') == 200) {
                $data = $response->json('data');

                $text = "*Status Pengiriman ($awb)*\n";
                $text .= "📦 Kurir: " . $data['summary']['courier'] . "\n";
                $text .= "📅 Tanggal: " . $data['summary']['date'] . "\n";
                $text .= "📍 Status: " . $data['summary']['status'] . "\n";
                $text .= "👤 Penerima: " . $data['detail']['receiver'] . "\n\n";
                $text .= "*Riwayat:*\n";

                foreach (array_slice($data['history'], 0, 3) as $his) {
                    $text .= "• " . $his['date'] . " - " . $his['desc'] . "\n";
                }
            } else {
                $text = "❌ Resi tidak ditemukan atau format salah.";
            }

            Http::post('http://127.0.0.1:5000/send-message', [
                'to' => $from,
                'text' => $text,
                'session' => $sessionId,
                'isGroup' => $isGroup,
            ]);

            return response()->json(['status' => 'resi_sent']);
        }
        // Jika keyword "fakta"
        else if ($message === 'fakta') {
            $response = Http::get('https://cinnabar.icaksh.my.id/public/daily/tawiki');

            if ($response->successful()) {
                $infoList = $response->json()['data']['info'];
                $random = collect($infoList)->random(); // ambil satu fakta
                $text = "🧠 Tahukah kamu?\n" . $random['tahukah_anda'];

                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => $text,
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                    'tanggal' => now()->format('Y-m-d'),
                ]);
            }

            return response()->json(['status' => 'fakta sent']);
        }
        // Cek apakah pesan adalah "pddikti ..."
        if (Str::startsWith(Str::lower($message), 'pddikti ')) {
            $keyword = trim(Str::after($message, 'pddikti '));

            Http::post('http://127.0.0.1:5000/send-pddikti', [
                'to' => $from,
                'query' => $keyword,
                'session' => $sessionId,
            ]);

            return response()->json(['status' => 'pddikti_sent']);
        }
        // Jika keyword "quote"
        else if ($message === 'quote') {
            $response = Http::get('https://jagokata-api.vercel.app/acak');

            if ($response->successful()) {
                $quotes = $response->json()['data']['quotes'];

                // Ambil 1 quote secara acak
                $random = collect($quotes)->random();

                $text = $random['quote'] . "\n\n— " . $random['author']['name'];

                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => $text,
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                    'tanggal' => now()->format('Y-m-d'),
                ]);
            }

            return response()->json(['status' => 'quote sent']);
        } else {
            $rule = AutoReply::where('keyword', 'like', "%{$message}%")->first();

            if ($rule) {
                $mention = $request->mention;

                $text = $rule->response;

                if ($isGroup && $mention) {
                    // 👇 Format mention WA dengan karakter khusus
                    $mentionText = "@" . str_replace(['@c.us', '@s.whatsapp.net'], '', $mention);
                    $text = "$mentionText $text";
                }
                Log::info('📤 Mengirim reply ke Node', [
                    'session_id' => $sessionId,
                    'to' => $from,
                    'text' => $text,
                ]);
                Http::post('http://127.0.0.1:5000/send-message', [
                    'to' => $from,
                    'text' => $text,
                    'session' => $sessionId,
                    'isGroup' => $isGroup,
                    'tanggal' => now()->format('Y-m-d'),
                    'mentions' => $isGroup ? [$mention] : [], // kirim dalam array
                    'quoted' => null // 🚫 nonaktifkan quote

                ]);
            }
        }
        return response()->json(['status' => $rule ? 'sent' : 'no_match']);
    }
    // 🔽 Tambahkan di dalam class
    function cariKodeWilayah(string $namaWilayah): ?array
    {
        $namaWilayah = strtolower(trim($namaWilayah));
        $file = storage_path('app/wilayah.csv');
        Log::info("📁 Membuka file wilayah.csv di: $file");

        if (!file_exists($file)) {
            Log::warning("❌ File tidak ditemukan: $file");
            return null;
        }

        $handle = fopen($file, 'r');
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            [$kode, $nama] = $data;
            if (strtolower(trim($nama)) === $namaWilayah) {
                fclose($handle);
                $level = match (substr_count($kode, '.')) {
                    0 => 'adm1',
                    1 => 'adm2',
                    2 => 'adm3',
                    3 => 'adm4',
                    default => 'unknown'
                };
                Log::info("✅ Ditemukan kecocokan wilayah: $nama dengan kode: $kode dan level: $level");
                return ['kode' => $kode, 'level' => $level];
            }
        }
        fclose($handle);
        Log::warning("❌ Tidak ditemukan wilayah yang cocok dengan: $namaWilayah");
        return null;
    }
    public function send(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|string',
            'text' => 'required|string',
        ]);

        $res = Http::post('http://localhost:5000/send-message', [
            'to' => $validated['to'],
            'text' => $validated['text'],
            'session' => $request->session, // dari middleware
            'quoted' => null,
            'isGroup' => false,
        ]);

        return response()->json(['status' => 'sent', 'gateway_response' => $res->json()]);
    }
}
