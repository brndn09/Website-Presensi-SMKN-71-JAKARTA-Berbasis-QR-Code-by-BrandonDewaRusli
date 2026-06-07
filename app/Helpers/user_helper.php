<?php

use App\Libraries\enums\UserRole;

function user_role(): UserRole
{
    // Muat helper auth agar fungsi user() tersedia
    helper('auth');
    $u = user();

    // Berikan fallback jika user tidak ditemukan (misal saat logout)
    if (!$u) {
        return UserRole::StafPetugas; // Atau role default lainnya
    }

    return UserRole::from(intval($u->is_superadmin));
}

function getUserRole(int|string $role): string
{
    return UserRole::from(intval($role))->label();
}

function is_wali_kelas(): bool
{
    helper('auth');
    $u = user();
    
    // Gunakan pengecekan yang lebih aman agar tidak error saat $u null
    return !empty($u) && !empty($u->id_guru);
}

function is_superadmin(): bool
{
    helper('auth');
    return logged_in() && user_role()->isSuperAdmin();
}

function is_kepsek(): bool
{
    helper('auth');
    return logged_in() && user_role() === UserRole::Kepsek;
}

function can_edit_attendance(): bool
{
    helper('auth');
    return logged_in() && in_array(user_role(), [UserRole::SuperAdmin, UserRole::StafPetugas]);
}

function can_generate_qr(): bool
{
    helper('auth');
    return logged_in() && in_array(user_role(), [UserRole::SuperAdmin, UserRole::StafPetugas]);
}

function can_view_report(): bool
{
    helper('auth');
    return logged_in() && in_array(user_role(), [UserRole::SuperAdmin, UserRole::StafPetugas, UserRole::Kepsek]);
}