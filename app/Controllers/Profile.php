<?php

namespace App\Controllers;

use App\Models\PetugasModel;
use Myth\Auth\Password;

class Profile extends BaseController
{
    protected $petugasModel;

    public function __construct()
    {
        $this->petugasModel = new PetugasModel();
    }

    public function update_password()
    {
        // Ambil data user yang sedang login
        $user = user();
        $userId = $user->id;

        $old_pass = $this->request->getPost('old_password');
        $new_pass = $this->request->getPost('new_password');
        $conf_pass = $this->request->getPost('confirm_password');

        // 1. Validasi Input
        if (empty($old_pass) || empty($new_pass)) {
            return redirect()->back()->with('msg', 'Password tidak boleh kosong')->with('error', true);
        }

        // 2. Verifikasi Password Lama
        // Karena kamu pakai Myth/Auth, verifikasi pakai Password::verify
        if (! Password::verify($old_pass, $user->password_hash)) {
            return redirect()->back()->with('msg', 'Password lama salah!')->with('error', true);
        }

        // 3. Validasi Konfirmasi
        if ($new_pass !== $conf_pass) {
            return redirect()->back()->with('msg', 'Konfirmasi password baru tidak cocok!')->with('error', true);
        }

        // 4. Update Password
        $dataUpdate = [
            'password_hash' => Password::hash($new_pass)
        ];

        if ($this->petugasModel->update($userId, $dataUpdate)) {
            return redirect()->back()->with('msg', 'Password berhasil diperbarui!')->with('error', false);
        }

        return redirect()->back()->with('msg', 'Password saat ini salah!')->with('error', true);
    }
}
