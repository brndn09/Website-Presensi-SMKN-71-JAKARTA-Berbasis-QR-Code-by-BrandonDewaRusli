<?= $this->extend('templates/starting_page_layout'); ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4361ee, #3a86ff);
        --bg-overlay: rgba(15, 23, 42, 0.7);
        --text-dark: #1e293b;
    }

    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: #0f172a; /* Fallback color */
    }

    .main-panel {
        background: var(--bg-overlay);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* CARD UTAMA */
    .card-login {
        border: none;
        border-radius: 24px; /* Sudut melengkung */
        background: #ffffff;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        width: 100%;
        max-width: 400px;
        /* WAJIB: overflow hidden agar header biru terpotong mengikuti lengkungan kartu */
        overflow: hidden; 
        display: flex;
        flex-direction: column;
    }

    /* HEADER BIRU - SEKARANG PASTI MENEMPEL */
    .card-header-auth {
        background: var(--primary-gradient);
        padding: 35px 20px;
        text-align: center;
        color: white;
        margin: 0; 
        width: 100%;
        border: none;
        display: block; /* Memastikan dia mengambil ruang penuh */
    }

    .card-header-auth h4 {
        font-weight: 800;
        font-size: 1.7rem;
        margin: 0;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    /* BODY FORM */
    .card-body-login {
        padding: 40px 30px;
    }

    .form-group-custom {
        margin-bottom: 22px;
    }

    .form-group-custom label {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: block;
        letter-spacing: 0.5px;
    }

    /* INPUT MODERN */
    .form-control-modern {
        border-radius: 12px;
        border: 2px solid #f1f5f9;
        padding: 14px 18px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background-color: #f8fafc;
        width: 100%;
        box-sizing: border-box; /* Memastikan padding tidak merusak lebar */
        color: var(--text-dark);
    }

    .form-control-modern:focus {
        border-color: #4361ee;
        background-color: #fff;
        outline: none;
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
    }

    /* TOMBOL LOGIN */
    .btn-login-modern {
        background: var(--primary-gradient);
        border: none;
        border-radius: 14px;
        padding: 16px;
        font-weight: 800;
        font-size: 1rem;
        color: #fff;
        text-transform: uppercase;
        width: 100%;
        cursor: pointer;
        box-shadow: 0 10px 20px -5px rgba(67, 97, 238, 0.4);
        margin-top: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-login-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(67, 97, 238, 0.5);
    }

    .btn-login-modern:active {
        transform: translateY(0);
    }

    /* FOOTER */
    .login-footer {
        color: #fff;
        margin-top: 25px;
        text-align: center;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    /* Animasi Masuk */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Error Message Styling */
    .invalid-feedback {
        display: block;
        font-size: 0.75rem;
        margin-top: 5px;
        color: #ef4444;
    }
</style>
<?= $this->endSection() ?>


<?= $this->section('content'); ?>
<div class="main-panel">
    <div class="card-login">
        <div class="card-header-auth"><h4>RESET PASSWORD</h4></div>
        <div class="card-body-login">
            <form action="<?= base_url('forgot-password/reset') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group-custom">
                    <label>Kode OTP (6 Digit)</label>
                    <input type="text" name="otp" class="form-control-modern" required maxlength="6">
                </div>
                <div class="form-group-custom">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-control-modern" required>
                </div>
                <div class="form-group-custom">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="pass_confirm" class="form-control-modern" required>
                </div>
                <button type="submit" class="btn-login-modern">Reset Password</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>