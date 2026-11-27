<?php
    $portalKey = ($portal ?? 'anggota') === 'mitra' ? 'mitra' : 'anggota';
?>
<style>
    .otp-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        margin: 4px 0 12px;
    }
    .otp-step {
        flex: 1;
        text-align: center;
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 6px 8px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .otp-step.is-active {
        background: #eff6ff;
        color: var(--inkindo-blue);
        border-color: #93c5fd;
    }
    .otp-step.is-done {
        background: #f0fdf4;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .otp-step-panel {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .otp-alert {
        width: 100%;
        font-size: 12.5px;
        line-height: 1.45;
        padding: 9px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        text-align: left;
        font-weight: 500;
    }
    .otp-alert.is-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .otp-alert.is-success {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .otp-dev-box {
        width: 100%;
        font-size: 12px;
        background: #fffbeb;
        color: #92400e;
        border: 1px dashed #f59e0b;
        border-radius: 10px;
        padding: 8px 12px;
        margin-bottom: 10px;
        text-align: center;
    }
    .otp-masked-box,
    .otp-badge-verified {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 12.5px;
        padding: 9px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        word-break: break-all;
    }
    .otp-masked-box {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }
    .otp-badge-verified {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .otp-code-input {
        width: 100% !important;
        text-align: center !important;
        font-size: 22px !important;
        font-weight: 700 !important;
        letter-spacing: 8px !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        padding: 12px 14px !important;
        border-radius: 12px !important;
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #0f172a !important;
        margin: 4px 0 8px !important;
    }
    .otp-code-input:focus {
        border-color: var(--inkindo-blue) !important;
        box-shadow: 0 0 0 3px rgba(10, 100, 164, 0.14) !important;
        outline: none !important;
    }
    .otp-resend-row {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin: 2px 0 10px;
    }
    .otp-link-btn {
        background: transparent !important;
        border: none !important;
        padding: 4px 2px !important;
        margin: 0 !important;
        width: auto !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: var(--inkindo-blue) !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        box-shadow: none !important;
        cursor: pointer;
    }
    .otp-link-btn:disabled {
        color: #94a3b8 !important;
        cursor: not-allowed;
    }
    .otp-match-msg {
        width: 100%;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        min-height: 18px;
        margin: 2px 0 6px;
    }
    .otp-modal-card {
        max-width: 420px !important;
        width: calc(100% - 28px) !important;
        text-align: left !important;
        padding: 22px 22px 20px !important;
    }
    .otp-modal-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
        position: relative;
    }
    .otp-modal-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--inkindo-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .otp-modal-header h3 {
        margin: 0 0 2px;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
    }
    .otp-modal-header p {
        margin: 0;
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.4;
    }
    .otp-modal-close {
        margin-left: auto;
        background: #f1f5f9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }
    .otp-modal-card .auth-input-group {
        width: 100%;
        margin: 6px 0;
    }
    .otp-modal-card .auth-input-group input {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 42px 12px 40px;
        font-size: 15px;
        color: #0f172a;
    }
    .otp-modal-actions {
        width: 100%;
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    .otp-btn-primary {
        flex: 1;
        background: var(--inkindo-blue);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        min-height: 44px;
    }
    .otp-btn-primary:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    .otp-btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        min-height: 44px;
    }
    @media (max-width: 575.98px) {
        .otp-modal-card {
            padding: 18px 16px !important;
        }
        .otp-modal-card .auth-input-group input {
            font-size: 16px;
        }
    }
</style>

<script>
(function () {
    const PORTAL = <?= json_encode($portalKey) ?>;
    let regCooldownTimer = null;
    let forgotCooldownTimer = null;

    function getCsrfHeader() {
        const metaVal = document.querySelector('meta[name="csrf-token-value"]');
        return metaVal ? metaVal.getAttribute('content') : '';
    }

    function updateCsrfToken(newHash) {
        if (!newHash) return;
        const metaVal = document.querySelector('meta[name="csrf-token-value"]');
        const metaName = document.querySelector('meta[name="csrf-token-name"]');
        if (metaVal) metaVal.setAttribute('content', newHash);
        const tokenName = metaName ? metaName.getAttribute('content') : '';
        if (tokenName) {
            document.querySelectorAll('input[name="' + tokenName + '"]').forEach((inp) => {
                inp.value = newHash;
            });
        }
    }

    async function postOtpJson(url, payload) {
        const formData = new FormData();
        Object.keys(payload).forEach((k) => formData.append(k, payload[k]));

        const metaName = document.querySelector('meta[name="csrf-token-name"]');
        const tokenName = metaName ? metaName.getAttribute('content') : '';
        const tokenVal = getCsrfHeader();
        if (tokenName && tokenVal) {
            formData.append(tokenName, tokenVal);
        }

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenVal
            },
            body: formData,
            credentials: 'same-origin'
        });

        let data = {};
        try {
            data = await res.json();
        } catch (e) {
            data = { ok: false, message: 'Terjadi kesalahan jaringan. Silakan coba lagi.' };
        }
        if (data.csrf_hash) updateCsrfToken(data.csrf_hash);
        return data;
    }

    function setBoxAlert(elId, msg, isSuccess) {
        const el = document.getElementById(elId);
        if (!el) return;
        if (!msg) {
            el.style.display = 'none';
            el.textContent = '';
            return;
        }
        el.className = 'otp-alert ' + (isSuccess ? 'is-success' : 'is-error');
        el.textContent = msg;
        el.style.display = 'block';
    }

    function setDevOtp(elId, devCode) {
        const el = document.getElementById(elId);
        if (!el) return;
        if (!devCode) {
            el.style.display = 'none';
            el.innerHTML = '';
            return;
        }
        el.innerHTML = 'Mode Lokal (SMTP belum aktif) — Kode OTP: <strong style="font-size:14px; letter-spacing:2px;">' + devCode + '</strong>';
        el.style.display = 'block';
    }

    function updateStepPills(containerId, activeStep) {
        const wrap = document.getElementById(containerId);
        if (!wrap) return;
        wrap.querySelectorAll('.otp-step').forEach((pill) => {
            const s = Number(pill.getAttribute('data-step'));
            pill.classList.remove('is-active', 'is-done');
            if (s < activeStep) pill.classList.add('is-done');
            else if (s === activeStep) pill.classList.add('is-active');
        });
    }

    function startCooldown(btnId, seconds, timerSetter) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        let left = Number(seconds || 60);
        btn.disabled = true;
        btn.textContent = 'Kirim Ulang (' + left + 'd)';
        const id = setInterval(() => {
            left--;
            if (left <= 0) {
                clearInterval(id);
                btn.disabled = false;
                btn.textContent = 'Kirim Ulang OTP';
            } else {
                btn.textContent = 'Kirim Ulang (' + left + 'd)';
            }
        }, 1000);
        timerSetter(id);
    }

    // ==================== REGISTER OTP WIZARD ====================
    window.goRegStep = function (step) {
        [1, 2, 3].forEach((n) => {
            const panel = document.getElementById('regOtpStep' + n);
            if (panel) panel.style.display = (n === step) ? 'flex' : 'none';
        });
        updateStepPills('regOtpStepPills', step);
        if (step === 1) {
            setDevOtp('regOtpDevBox', '');
        }
    };

    window.regSendOtp = async function (isResend) {
        const emailInp = document.getElementById('regEmailInput');
        const hpInp = document.getElementById('regWebsiteHp');
        const btn = document.getElementById(isResend ? 'regResendOtpBtn' : 'regSendOtpBtn');
        const email = emailInp ? emailInp.value.trim() : '';

        if (!email) {
            setBoxAlert('regOtpAlert', 'Masukkan alamat email aktif terlebih dahulu.', false);
            if (emailInp) emailInp.focus();
            return;
        }

        setBoxAlert('regOtpAlert', '', false);
        if (btn) {
            btn.disabled = true;
            if (!isResend) btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Memeriksa Email...';
        }

        const res = await postOtpJson('/auth/register/send-otp', {
            portal: PORTAL,
            email: email,
            website_hp: hpInp ? hpInp.value : ''
        });

        if (btn && !isResend) {
            btn.disabled = false;
            btn.textContent = 'Daftar';
        }

        if (!res.ok) {
            setBoxAlert('regOtpAlert', res.message || 'Gagal mengirim OTP.', false);
            if (res.cooldown && isResend) {
                if (regCooldownTimer) clearInterval(regCooldownTimer);
                startCooldown('regResendOtpBtn', res.cooldown, (t) => { regCooldownTimer = t; });
            } else if (btn && isResend) {
                btn.disabled = false;
            }
            return;
        }

        const maskedEl = document.getElementById('regMaskedEmailText');
        if (maskedEl) maskedEl.textContent = res.masked_email || email;
        setBoxAlert('regOtpAlert', isResend ? 'Kode OTP baru telah dikirim ke email Anda.' : '', true);
        window.goRegStep(2);

        const otpInp = document.getElementById('regOtpCodeInput');
        if (otpInp) {
            otpInp.value = '';
            setTimeout(() => otpInp.focus(), 60);
        }
        if (regCooldownTimer) clearInterval(regCooldownTimer);
        startCooldown('regResendOtpBtn', res.cooldown || 60, (t) => { regCooldownTimer = t; });
    };

    window.regVerifyOtp = async function () {
        const emailInp = document.getElementById('regEmailInput');
        const otpInp = document.getElementById('regOtpCodeInput');
        const btn = document.getElementById('regVerifyOtpBtn');
        const email = emailInp ? emailInp.value.trim() : '';
        const otp = otpInp ? otpInp.value.replace(/\D+/g, '') : '';

        if (otp.length !== 6) {
            setBoxAlert('regOtpAlert', 'Masukkan 6 digit kode OTP yang dikirim ke email Anda.', false);
            if (otpInp) otpInp.focus();
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Memverifikasi...';
        }

        const res = await postOtpJson('/auth/register/verify-otp', {
            portal: PORTAL,
            email: email,
            otp: otp
        });

        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Verifikasi OTP <i class="bi bi-check-circle ms-1"></i>';
        }

        if (!res.ok) {
            setBoxAlert('regOtpAlert', res.message || 'Kode OTP tidak sesuai.', false);
            return;
        }

        const hiddenEmail = document.getElementById('regVerifiedEmailHidden');
        const verifiedTxt = document.getElementById('regVerifiedEmailText');
        if (hiddenEmail) hiddenEmail.value = res.email || email;
        if (verifiedTxt) verifiedTxt.textContent = res.email || email;

        setDevOtp('regOtpDevBox', '');
        setBoxAlert('regOtpAlert', res.message, true);
        window.goRegStep(3);

        const passInp = document.getElementById('regOtpPasswordInput');
        if (passInp) setTimeout(() => passInp.focus(), 60);
    };

    // Validasi kecocokan password pada step 3 pendaftaran
    const regP1 = document.getElementById('regOtpPasswordInput');
    const regP2 = document.getElementById('regOtpConfPasswordInput');
    const regPMsg = document.getElementById('regOtpPassMatchMsg');
    function checkRegOtpMatch() {
        if (!regP1 || !regP2 || !regPMsg) return true;
        if (!regP2.value) { regPMsg.textContent = ''; return true; }
        if (regP1.value !== regP2.value) {
            regPMsg.textContent = 'Password tidak sama';
            regPMsg.style.color = '#dc2626';
            return false;
        }
        regPMsg.textContent = 'Password sesuai';
        regPMsg.style.color = '#16a34a';
        return true;
    }
    if (regP1) regP1.addEventListener('input', checkRegOtpMatch);
    if (regP2) regP2.addEventListener('input', checkRegOtpMatch);

    const regOtpForm = document.getElementById('registerOtpForm');
    if (regOtpForm) {
        regOtpForm.addEventListener('submit', function (e) {
            if (!checkRegOtpMatch()) {
                e.preventDefault();
                if (regP2) regP2.focus();
                return;
            }
            const hiddenEmail = document.getElementById('regVerifiedEmailHidden');
            if (!hiddenEmail || !hiddenEmail.value.trim()) {
                e.preventDefault();
                setBoxAlert('regOtpAlert', 'Silakan selesaikan verifikasi OTP email terlebih dahulu.', false);
                window.goRegStep(1);
            }
        });
    }

    // Auto-numeric filter pada input OTP
    ['regOtpCodeInput', 'forgotOtpInput'].forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input', function () {
            this.value = this.value.replace(/\D+/g, '').slice(0, 6);
        });
    });

    // ==================== FORGOT PASSWORD OTP MODAL ====================
    window.goForgotStep = function (step) {
        [1, 2, 3].forEach((n) => {
            const panel = document.getElementById('forgotStep' + n);
            if (panel) panel.style.display = (n === step) ? 'flex' : 'none';
        });
        updateStepPills('forgotStepPills', step);
        if (step === 1) {
            setDevOtp('forgotDevOtpBox', '');
        }
    };

    window.openForgotModal = function (e) {
        if (e) e.preventDefault();
        setBoxAlert('forgotAlertBox', '', false);
        setDevOtp('forgotDevOtpBox', '');
        window.goForgotStep(1);
        document.getElementById('forgotModal')?.classList.add('is-open');
        const inp = document.getElementById('forgotIdentifierInput');
        if (inp) setTimeout(() => inp.focus(), 80);
    };

    window.closeForgotModal = function () {
        document.getElementById('forgotModal')?.classList.remove('is-open');
    };

    window.forgotSendOtp = async function (isResend) {
        const idInp = document.getElementById('forgotIdentifierInput');
        const btn = document.getElementById(isResend ? 'forgotResendBtn' : 'forgotSendOtpBtn');
        const identifier = idInp ? idInp.value.trim() : '';

        if (!identifier || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(identifier)) {
            setBoxAlert('forgotAlertBox', 'Masukkan alamat email yang valid dan terdaftar.', false);
            if (idInp) idInp.focus();
            return;
        }

        setBoxAlert('forgotAlertBox', '', false);
        if (btn) {
            btn.disabled = true;
            if (!isResend) btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Mengirim...';
        }

        const res = await postOtpJson('/auth/forgot/send-otp', {
            portal: PORTAL,
            identifier: identifier
        });

        if (btn && !isResend) {
            btn.disabled = false;
            btn.innerHTML = 'Kirim OTP <i class="bi bi-send ms-1"></i>';
        }

        if (!res.ok) {
            setBoxAlert('forgotAlertBox', res.message || 'Gagal mengirim kode OTP.', false);
            if (res.cooldown && isResend) {
                if (forgotCooldownTimer) clearInterval(forgotCooldownTimer);
                startCooldown('forgotResendBtn', res.cooldown, (t) => { forgotCooldownTimer = t; });
            } else if (btn && isResend) {
                btn.disabled = false;
            }
            return;
        }

        const maskedEl = document.getElementById('forgotMaskedEmailText');
        if (maskedEl) maskedEl.textContent = res.masked_email || '***';
        setBoxAlert('forgotAlertBox', isResend ? 'Kode OTP baru telah dikirim ke email Anda.' : '', true);
        window.goForgotStep(2);

        const otpInp = document.getElementById('forgotOtpInput');
        if (otpInp) {
            otpInp.value = '';
            setTimeout(() => otpInp.focus(), 60);
        }
        if (forgotCooldownTimer) clearInterval(forgotCooldownTimer);
        startCooldown('forgotResendBtn', res.cooldown || 60, (t) => { forgotCooldownTimer = t; });
    };

    window.forgotVerifyOtp = async function () {
        const otpInp = document.getElementById('forgotOtpInput');
        const btn = document.getElementById('forgotVerifyOtpBtn');
        const otp = otpInp ? otpInp.value.replace(/\D+/g, '') : '';

        if (otp.length !== 6) {
            setBoxAlert('forgotAlertBox', 'Masukkan 6 digit kode OTP.', false);
            if (otpInp) otpInp.focus();
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Memverifikasi...';
        }

        const res = await postOtpJson('/auth/forgot/verify-otp', { otp: otp });

        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Verifikasi OTP <i class="bi bi-check-circle ms-1"></i>';
        }

        if (!res.ok) {
            setBoxAlert('forgotAlertBox', res.message || 'Kode OTP tidak sesuai.', false);
            return;
        }

        const verTxt = document.getElementById('forgotVerifiedEmailText');
        if (verTxt) verTxt.textContent = res.masked_email || '***';
        setDevOtp('forgotDevOtpBox', '');
        setBoxAlert('forgotAlertBox', res.message, true);
        window.goForgotStep(3);

        const p1 = document.getElementById('forgotNewPassInput');
        if (p1) setTimeout(() => p1.focus(), 60);
    };

    const fP1 = document.getElementById('forgotNewPassInput');
    const fP2 = document.getElementById('forgotConfPassInput');
    const fPMsg = document.getElementById('forgotPassMatchMsg');
    function checkForgotMatch() {
        if (!fP1 || !fP2 || !fPMsg) return true;
        if (!fP2.value) { fPMsg.textContent = ''; return true; }
        if (fP1.value !== fP2.value) {
            fPMsg.textContent = 'Password tidak sama';
            fPMsg.style.color = '#dc2626';
            return false;
        }
        fPMsg.textContent = 'Password sesuai';
        fPMsg.style.color = '#16a34a';
        return true;
    }
    if (fP1) fP1.addEventListener('input', checkForgotMatch);
    if (fP2) fP2.addEventListener('input', checkForgotMatch);

    window.forgotResetPassword = async function () {
        if (!checkForgotMatch()) {
            if (fP2) fP2.focus();
            return;
        }
        const pass = fP1 ? fP1.value : '';
        const conf = fP2 ? fP2.value : '';
        const btn = document.getElementById('forgotSavePassBtn');

        if (pass.length < 8) {
            setBoxAlert('forgotAlertBox', 'Password baru minimal 8 karakter.', false);
            if (fP1) fP1.focus();
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';
        }

        const res = await postOtpJson('/auth/forgot/reset-password', {
            password: pass,
            confpassword: conf
        });

        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Simpan Password Baru <i class="bi bi-key ms-1"></i>';
        }

        if (!res.ok) {
            setBoxAlert('forgotAlertBox', res.message || 'Gagal memperbarui password.', false);
            return;
        }

        setBoxAlert('forgotAlertBox', res.message, true);
        if (fP1) fP1.value = '';
        if (fP2) fP2.value = '';
        setTimeout(() => {
            window.closeForgotModal();
        }, 1600);
    };
})();
</script>
