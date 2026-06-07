<?php

namespace App\Controllers;

use App\Models\PetugasModel;
use Myth\Auth\Password;

class ForgotPassword extends BaseController
{
    protected $petugasModel;

    public function __construct() {
        $this->petugasModel = new PetugasModel();
    }

    public function index() {
        return view('auth/forgot_password'); // Tampilan input email
    }

    public function sendOTP() {
        $email = $this->request->getPost('email');
        $user = $this->petugasModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar.');
        }

        session()->set('reset_email', $email);

        // Generate OTP 6 Digit
        $otp = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Simpan ke database (pastikan tabel users punya kolom reset_otp & reset_expires)
        // Jika belum ada, jalankan migration atau tambahkan manual di DB
        $this->petugasModel->update($user['id'], [
            'reset_otp' => $otp,
            'reset_expires' => $expires
        ]);

        // Kirim Email
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Kode OTP Reset Password');
        $emailService->setMessage("Kode OTP Anda adalah: <b>$otp</b>. Kode ini berlaku selama 15 menit.");

        if ($emailService->send()) {
            session()->set('reset_email', $email);
            return redirect()->to('forgot-password/verify')->with('msg', 'OTP telah dikirim ke email Anda.');
        }

        return redirect()->back()->with('error', 'Gagal mengirim email. Periksa konfigurasi SMTP.');
    }

    public function verifyView() {
        if (!session('reset_email')) return redirect()->to('forgot-password');
        return view('auth/verify_otp');
    }

    public function processReset() {
        $otp = $this->request->getPost('otp');
        $new_pass = $this->request->getPost('password');
        $conf_pass = $this->request->getPost('pass_confirm');
        $email = session('reset_email');

        if ($new_pass !== $conf_pass) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        $user = $this->petugasModel->where('email', $email)
                                   ->where('reset_otp', $otp)
                                   ->where('reset_expires >=', date("Y-m-d H:i:s"))
                                   ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'OTP salah atau sudah kedaluwarsa.');
        }

        // Update Password Baru
        $this->petugasModel->update($user['id'], [
            'password_hash' => Password::hash($new_pass),
            'reset_otp' => null,
            'reset_expires' => null
        ]);

        session()->remove('reset_email');
        return redirect()->to('login')->with('message', 'Password berhasil diubah. Silakan login.');
    }
}