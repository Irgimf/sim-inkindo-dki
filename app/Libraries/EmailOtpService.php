<?php

namespace App\Libraries;

class EmailOtpService
{
    public const OTP_TTL_SECONDS      = 300; // 5 menit
    public const RESEND_COOLDOWN      = 60;  // 60 detik
    public const MAX_VERIFY_ATTEMPTS  = 5;

    /**
     * Pastikan tabel `email_otps` tersedia.
     */
    public function ensureTable(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('email_otps')) {
            return;
        }

        $db->query("
            CREATE TABLE IF NOT EXISTS `email_otps` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(150) NOT NULL,
                `purpose` VARCHAR(40) NOT NULL,
                `code_hash` VARCHAR(128) NOT NULL,
                `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                `ip_address` VARCHAR(45) NULL DEFAULT NULL,
                `expires_at` DATETIME NOT NULL,
                `used_at` DATETIME NULL DEFAULT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_email_otps_lookup` (`email`, `purpose`, `expires_at`),
                KEY `idx_email_otps_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Sensor bagian tengah alamat email untuk keamanan (mis. ir***ad@gmail.com).
     */
    public static function maskEmail(string $email): string
    {
        $email = trim($email);
        if ($email === '' || ! str_contains($email, '@')) {
            return '***';
        }

        [$local, $domain] = explode('@', $email, 2);
        $len = strlen($local);

        if ($len <= 1) {
            $maskedLocal = $local . '***';
        } elseif ($len === 2) {
            $maskedLocal = $local[0] . '***';
        } elseif ($len <= 4) {
            $maskedLocal = $local[0] . '***' . $local[$len - 1];
        } else {
            $maskedLocal = substr($local, 0, 2) . str_repeat('*', min(5, $len - 4)) . substr($local, -2);
        }

        return $maskedLocal . '@' . $domain;
    }

    /**
     * Validasi format email dan (jika online) ketersediaan domain MX/A.
     */
    public function isValidEmailAddress(string $email): bool
    {
        $email = strtolower(trim($email));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $parts = explode('@', $email, 2);
        $domain = $parts[1] ?? '';
        if ($domain === '' || ! str_contains($domain, '.')) {
            return false;
        }

        // Cek DNS MX/A hanya jika bukan lingkungan offline lokal
        if (function_exists('checkdnsrr') && ! in_array($domain, ['localhost', 'example.com', 'test.com'], true)) {
            $hasMx = @checkdnsrr($domain, 'MX');
            $hasA  = $hasMx ? true : @checkdnsrr($domain, 'A');
            // Jika DNS gagal merespons karena tidak ada internet lokal, cek google.com terlebih dahulu
            if (! $hasMx && ! $hasA) {
                $isOnline = @checkdnsrr('google.com', 'MX');
                if ($isOnline) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Cek sisa waktu tunggu kirim ulang OTP (dalam detik).
     */
    public function getRemainingCooldown(string $email, string $purpose): int
    {
        $this->ensureTable();
        $db = \Config\Database::connect();
        $email = strtolower(trim($email));

        $last = $db->table('email_otps')
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->orderBy('id', 'DESC')
            ->get(1)
            ->getRowArray();

        if (! $last || empty($last['created_at'])) {
            return 0;
        }

        $elapsed = time() - strtotime((string) $last['created_at']);
        $remaining = self::RESEND_COOLDOWN - $elapsed;

        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Buat dan kirim kode OTP 6 digit ke email tujuan.
     *
     * @return array{ok: bool, message: string, cooldown?: int, masked_email?: string, dev_otp?: string}
     */
    public function sendOtp(string $email, string $purpose, string $ipAddress = '', string $subjectTitle = 'Kode Verifikasi OTP'): array
    {
        $this->ensureTable();
        $email = strtolower(trim($email));

        // Rate limit berbasis IP (maks 10 permintaan OTP per 5 menit)
        $throttler = \Config\Services::throttler();
        $ipKey = 'otp_ip_' . md5($ipAddress ?: 'unknown');
        if ($throttler->check($ipKey, 10, 300) === false) {
            return [
                'ok'      => false,
                'message' => 'Terlalu banyak permintaan kode OTP. Silakan coba lagi beberapa menit lagi.',
            ];
        }

        // Cek cooldown 60 detik per email + purpose
        $cooldown = $this->getRemainingCooldown($email, $purpose);
        if ($cooldown > 0) {
            return [
                'ok'       => false,
                'cooldown' => $cooldown,
                'message'  => "Mohon tunggu {$cooldown} detik sebelum meminta kode OTP baru.",
            ];
        }

        $db = \Config\Database::connect();

        // Bersihkan OTP lama (> 24 jam)
        $db->table('email_otps')
            ->where('created_at <', date('Y-m-d H:i:s', time() - 86400))
            ->delete();

        // Nonaktifkan OTP sebelumnya yang belum terpakai untuk email + purpose ini
        $now = date('Y-m-d H:i:s');
        $db->table('email_otps')
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->where('used_at IS NULL', null, false)
            ->update(['used_at' => $now]);

        // Generate 6 digit OTP kriptografis
        $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', time() + self::OTP_TTL_SECONDS);

        $db->table('email_otps')->insert([
            'email'      => $email,
            'purpose'    => $purpose,
            'code_hash'  => hash('sha256', $plainCode),
            'attempts'   => 0,
            'ip_address' => $ipAddress ?: null,
            'expires_at' => $expiresAt,
            'used_at'    => null,
            'created_at' => $now,
        ]);

        $emailConfig = config('Email');
        $smtpConfigured = trim((string) $emailConfig->SMTPHost) !== ''
            && trim((string) $emailConfig->SMTPUser) !== ''
            && trim((string) $emailConfig->SMTPPass) !== '';

        if (! $smtpConfigured) {
            return [
                'ok'      => false,
                'message' => 'Pengiriman email OTP belum aktif. Silakan lengkapi konfigurasi SMTP (email.SMTPHost, SMTPUser, SMTPPass) pada file .env terlebih dahulu.',
            ];
        }

        $sent = $this->dispatchEmail($email, $plainCode, $subjectTitle);
        if (! $sent) {
            return [
                'ok'      => false,
                'message' => 'Gagal mengirim email OTP. Periksa kembali koneksi SMTP atau pastikan alamat email tujuan aktif.',
            ];
        }

        return [
            'ok'           => true,
            'cooldown'     => self::RESEND_COOLDOWN,
            'masked_email' => self::maskEmail($email),
            'message'      => 'Kode OTP 6 digit telah dikirim ke ' . self::maskEmail($email) . '. Berlaku selama 5 menit.',
        ];
    }

    /**
     * Verifikasi kode OTP 6 digit.
     *
     * @return array{ok: bool, message: string}
     */
    public function verifyOtp(string $email, string $purpose, string $inputCode): array
    {
        $this->ensureTable();
        $email = strtolower(trim($email));
        $inputCode = preg_replace('/\D+/', '', trim($inputCode));

        if (strlen($inputCode) !== 6) {
            return [
                'ok'      => false,
                'message' => 'Kode OTP harus terdiri dari 6 digit angka.',
            ];
        }

        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $row = $db->table('email_otps')
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->where('used_at IS NULL', null, false)
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->get(1)
            ->getRowArray();

        if (! $row) {
            return [
                'ok'      => false,
                'message' => 'Kode OTP tidak ditemukan atau sudah kedaluwarsa. Silakan kirim ulang OTP.',
            ];
        }

        $attempts = (int) ($row['attempts'] ?? 0);
        if ($attempts >= self::MAX_VERIFY_ATTEMPTS) {
            $db->table('email_otps')->where('id', $row['id'])->update(['used_at' => $now]);
            return [
                'ok'      => false,
                'message' => 'Terlalu banyak percobaan salah. Silakan kirim ulang kode OTP baru.',
            ];
        }

        $expectedHash = (string) $row['code_hash'];
        $inputHash    = hash('sha256', $inputCode);

        if (! hash_equals($expectedHash, $inputHash)) {
            $newAttempts = $attempts + 1;
            $update = ['attempts' => $newAttempts];
            if ($newAttempts >= self::MAX_VERIFY_ATTEMPTS) {
                $update['used_at'] = $now;
            }
            $db->table('email_otps')->where('id', $row['id'])->update($update);

            $sisa = max(0, self::MAX_VERIFY_ATTEMPTS - $newAttempts);
            return [
                'ok'      => false,
                'message' => $sisa > 0
                    ? "Kode OTP tidak sesuai. Sisa percobaan: {$sisa} kali."
                    : 'Kode OTP hangus karena terlalu banyak percobaan salah. Silakan kirim ulang OTP.',
            ];
        }

        // Tandai OTP sudah terpakai secara atomik
        $db->table('email_otps')
            ->where('id', $row['id'])
            ->where('used_at IS NULL', null, false)
            ->update(['used_at' => $now]);

        if ($db->affectedRows() === 0) {
            return [
                'ok'      => false,
                'message' => 'Kode OTP sudah digunakan. Silakan kirim ulang OTP baru.',
            ];
        }

        return [
            'ok'      => true,
            'message' => 'Verifikasi OTP berhasil.',
        ];
    }

    /**
     * Kirim email HTML OTP melalui CodeIgniter Email Service.
     */
    private function dispatchEmail(string $toEmail, string $otpCode, string $subjectTitle): bool
    {
        try {
            $emailCfg = config('Email');
            $fromEmail = trim((string) $emailCfg->fromEmail) ?: trim((string) $emailCfg->SMTPUser);
            $fromName  = trim((string) $emailCfg->fromName) ?: 'SIM-INKINDO DKI Jakarta';

            $email = \Config\Services::email();
            $email->setFrom($fromEmail, $fromName);
            $email->setTo($toEmail);
            $email->setSubject("{$subjectTitle} - SIM-INKINDO DKI Jakarta");
            $email->setMailType('html');

            $safeTitle = htmlspecialchars($subjectTitle, ENT_QUOTES, 'UTF-8');
            $body = <<<HTML
<div style="font-family: 'Segoe UI', Arial, sans-serif; max-width: 520px; margin: 0 auto; background: #f8fafc; padding: 24px; border-radius: 14px;">
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px;">
    <div style="text-align: center; margin-bottom: 18px;">
      <div style="display: inline-block; background: #0A64A4; color: #ffffff; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 999px; letter-spacing: 0.5px;">
        SIM-INKINDO DKI JAKARTA
      </div>
    </div>
    <h2 style="color: #0f172a; font-size: 20px; margin: 0 0 10px; text-align: center;">{$safeTitle}</h2>
    <p style="color: #475569; font-size: 14px; line-height: 1.6; text-align: center; margin: 0 0 22px;">
      Gunakan kode verifikasi (OTP) berikut untuk melanjutkan proses di portal SIM-INKINDO DKI Jakarta:
    </p>
    <div style="background: #eff6ff; border: 1.5px dashed #0A64A4; border-radius: 12px; padding: 18px; text-align: center; margin-bottom: 20px;">
      <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0A64A4; font-family: monospace;">{$otpCode}</span>
    </div>
    <p style="color: #64748b; font-size: 12.5px; line-height: 1.6; text-align: center; margin: 0;">
      Kode ini berlaku selama <strong>5 menit</strong> dan hanya dapat digunakan satu kali.<br>
      Jangan bagikan kode OTP ini kepada siapa pun, termasuk pihak yang mengaku dari INKINDO.
    </p>
  </div>
  <p style="color: #94a3b8; font-size: 11.5px; text-align: center; margin-top: 14px;">
    Email otomatis dari Sistem Informasi &amp; Manajemen DPP INKINDO DKI Jakarta.
  </p>
</div>
HTML;

            $email->setMessage($body);
            if (! $email->send()) {
                log_message('error', 'EmailOtpService send failed: ' . $email->printDebugger(['headers']));
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'EmailOtpService exception: ' . $e->getMessage());
            return false;
        }
    }
}
