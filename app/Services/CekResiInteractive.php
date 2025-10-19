<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class CekResiInteractive
{
    public function __construct(
        private readonly CekResiState $state,
        private readonly WaService    $wa,
    ) {}

    public function handle(string $device, string $chatId, string $text): void
    {
        $clean = trim($text);
        $lower = Str::of($clean)->lower()->toString();

        // global controls
        if (in_array($lower, ['batal','cancel','keluar'])) {
            $this->state->clear($device, $chatId);
            $this->send($device, $chatId, "? Sesi *cek resi* dibatalkan.\nKetik *cek resi* untuk mulai lagi. ?");
            return;
        }
        if (in_array($lower, ['menu','help'])) {
            $this->start($device, $chatId);
            return;
        }

        // kalau belum aktif dan user mengetik "cek resi" ? start
        if (!$this->state->isActive($device, $chatId)) {
            if (Str::contains($lower, 'cek') && Str::contains($lower, 'resi')) {
                $this->start($device, $chatId);
            }
            return;
        }

        // route berdasar state
        $state = $this->state->getState($device, $chatId, 'idle');
        $ctx   = $this->state->getCtx($device, $chatId);

        if ($state === 'choose_expedition') {
            $this->onChooseExpedition($device, $chatId, $lower, $ctx);
            return;
        }

        if ($state === 'ask_resi') {
            $this->onAskResi($device, $chatId, $clean, $ctx);
            return;
        }

        // fallback
        $this->start($device, $chatId);
    }

    public function start(string $device, string $chatId): void
    {
        $this->state->put($device, $chatId, 'choose_expedition', ['page' => 1]);
        $this->send($device, $chatId,
"? *Selamat datang di Menu Cek Resi — byZiezIe*  
Silakan pilih *ekspedisi* di bawah ini.

Ketik *angka* (contoh: 1)  
Ketik *next/prev* untuk pindah halaman  
Ketik *batal* untuk membatalkan.");
        $this->sendMenu($device, $chatId, 1);
    }

    private function sendMenu(string $device, string $chatId, int $page): void
    {
        $ops     = (array) config('cekresi_interactive.options');
        $perPage = (int) config('cekresi_interactive.page_size', 8);
        $chunks  = array_chunk($ops, $perPage);
        $pages   = max(1, count($chunks));
        $page    = max(1, min($page, $pages));
        $list    = $chunks[$page-1];

        $startNo = ($perPage * ($page-1)) + 1;
        $lines = [];
        foreach ($list as $i => $opt) {
            $num = $startNo + $i;
            $lines[] = "{$num}. {$opt['label']}";
        }
        $footer = $pages > 1 ? "\n?? *prev* • ?? *next* (hal. {$page}/{$pages})" : '';

        $this->send($device, $chatId, "?? *Daftar Ekspedisi*\n".implode("\n", $lines).$footer);
    }

    private function onChooseExpedition(string $device, string $chatId, string $lower, array $ctx): void
    {
        $page    = (int) ($ctx['page'] ?? 1);
        $ops     = (array) config('cekresi_interactive.options');
        $perPage = (int) config('cekresi_interactive.page_size', 8);

        // navigasi
        if (in_array($lower, ['next','n','>'])) {
            $page++;
            $this->state->put($device, $chatId, 'choose_expedition', ['page' => $page]);
            $this->sendMenu($device, $chatId, $page);
            return;
        }
        if (in_array($lower, ['prev','p','<'])) {
            $page = max(1, $page-1);
            $this->state->put($device, $chatId, 'choose_expedition', ['page' => $page]);
            $this->sendMenu($device, $chatId, $page);
            return;
        }

        // pilihan angka ? langsung by index global
        if (ctype_digit($lower)) {
            $num = (int) $lower;
            $idx = $num - 1;
            if (isset($ops[$idx])) {
                $exp = $ops[$idx];
                $this->state->put($device, $chatId, 'ask_resi', ['exp' => $exp['key'], 'exp_label' => $exp['label']]);
                $this->sendAskResi($device, $chatId, $exp['label']);
                return;
            }
        }

        // cocokan alias/label
        foreach ($ops as $opt) {
            $hay = array_merge([$opt['key'], Str::lower($opt['label'])], (array) ($opt['aliases'] ?? []));
            foreach ($hay as $h) {
                if ($lower === Str::lower($h)) {
                    $this->state->put($device, $chatId, 'ask_resi', ['exp' => $opt['key'], 'exp_label' => $opt['label']]);
                    $this->sendAskResi($device, $chatId, $opt['label']);
                    return;
                }
            }
        }

        $this->send($device, $chatId, "?Tidak dikenali. Ketik *angka*, atau *next/prev*, atau *batal*.");
    }

    private function sendAskResi(string $device, string $chatId, string $label): void
    {
        $this->send($device, $chatId,
"?? *Pengecekan Resi — {$label}*  
Silakan kirim *nomor resi* sekarang.  
Contoh: `SPXID05460537279A`");
    }

    private function onAskResi(string $device, string $chatId, string $resi, array $ctx): void
    {
        if (strlen($resi) < 5) {
            $this->send($device, $chatId, "?Nomor resi terlalu pendek. Coba kirim lagi ya.");
            return;
        }

        $expKey   = $ctx['exp'] ?? null;
        $expLabel = $ctx['exp_label'] ?? '-';
        if (!$expKey) {
            $this->state->put($device, $chatId, 'choose_expedition', ['page' => 1]);
            $this->send($device, $chatId, "?? Ekspedisi belum dipilih. Pilih dulu ya.");
            $this->sendMenu($device, $chatId, 1);
            return;
        }

        // call API Ryzumi
        $url  = (string) config('cekresi_interactive.api_base');
        $resp = Http::timeout(20)->get($url, ['resi' => $resi, 'ekspedisi' => $expKey]);

        if (!$resp->ok()) {
            $this->send($device, $chatId, "?? Server pelacakan sedang sibuk. Coba lagi nanti ya.");
            return;
        }
        $json = $resp->json();

        if (!($json['success'] ?? false)) {
            $msg = $json['message'] ?? 'Nomor resi tidak ditemukan.';
            $this->send($device, $chatId, "?? {$msg}");
            return;
        }

        $data   = (array) ($json['data'] ?? []);
        $status = $data['status'] ?? '-';
        $posisi = $data['lastPosition'] ?? '-';
        $tgl    = $data['tanggalKirim'] ?? '-';
        $cs     = $data['customerService'] ?? '-';
        $link   = $data['shareLink'] ?? null;
        $resiOk = $data['resi'] ?? $resi;

        // ringkas 8 riwayat
        $histLines = [];
        foreach ((array) ($data['history'] ?? []) as $i => $h) {
            if ($i >= 8) { $histLines[] = "… (riwayat dipotong)"; break; }
            $histLines[] = "• {$h['tanggal']} — {$h['keterangan']}";
        }
        $histText = $histLines ? "\n\n?? *Riwayat Terbaru:*\n".implode("\n", $histLines) : "";

        $msg =
"? *Hasil Pelacakan*  
?? *{$expLabel}*  
?? *Resi:* `{$resiOk}`  
?? *Status:* {$status}
?? *Posisi Terakhir:* {$posisi}
?? *Tgl Kirim:* {$tgl}
?? *CS:* {$cs}".($link ? "\n?? *Link:* {$link}" : "").$histText.
"\n\nButuh cek resi lain? Ketik *cek resi*. ?";

        $this->send($device, $chatId, $msg);
        $this->state->clear($device, $chatId); // selesai ? reset
    }

    private function send(string $device, string $chatId, string $text): void
    {
        $this->state->touch($device, $chatId); // refresh TTL tiap kirim
        $this->wa->sendMessage([
            'session' => $device,
            'to'      => $chatId,
            'text'    => $text,
        ]);
    }
}
