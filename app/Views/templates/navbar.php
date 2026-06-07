<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
   <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
         <span class="sr-only">Toggle navigation</span>
         <span class="navbar-toggler-icon icon-bar"></span>
         <span class="navbar-toggler-icon icon-bar"></span>
         <span class="navbar-toggler-icon icon-bar"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end">
         <ul class="navbar-nav">
            
            <?php if (user_role() === \App\Libraries\enums\UserRole::Scanner): ?>
            <li class="nav-item dropdown">
               <a class="nav-link" href="javascript:;" id="navbarDropdownScan" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">qr_code</i>
                  <p class="d-lg-none d-md-block">
                     Scan
                  </p>
               </a>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownScan">
                  <a class="dropdown-item" href="<?= base_url('scan/masuk'); ?>">Absen masuk</a>
                  <a class="dropdown-item" href="<?= base_url('scan/pulang'); ?>">Absen pulang</a>
               </div>
            </li>
            <?php endif; ?>
            <li class="nav-item dropdown">
               <a class="nav-link <?= is_superadmin() ? 'text-danger' : ''; ?>" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                     Account
                  </p>
                  <span>User : <?= user()->toArray()['username']; ?></span>
               </a>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <a class="dropdown-item" href="#">
                     <i class="material-icons mr-2" style="font-size: 1.2rem; vertical-align: middle;">email</i>
                     Email: <?= user()->toArray()['email']; ?>
                  </a>
                  <a class="dropdown-item" href="#">
                     <i class="material-icons mr-2" style="font-size: 1.2rem; vertical-align: middle;">verified_user</i>
                     Role:
                     <span class="h6 text-capitalize ml-2 my-auto badge badge-<?= is_superadmin() ? 'danger' : 'success'; ?>">
                        <?= user_role()->label(); ?>
                     </span>
                  </a>
                  <a class="dropdown-item" href="javascript:;" data-toggle="modal" data-target="#modalGantiPassword">
                     <i class="material-icons mr-2" style="font-size: 1.2rem; vertical-align: middle;">lock_reset</i>
                     Ganti Password
                  </a>
                  
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="javascript:;" onclick="konfirmasiLogOut('<?= base_url('/logout'); ?>')">
                     <i class="material-icons mr-2" style="font-size: 1.2rem; vertical-align: middle;">logout</i>
                     Log Out
                  </a>
               </div>
            </li>
         </ul>
      </div>
   </div>
</nav>

<script type="text/javascript">
function konfirmasiLogOut(urlLogout) {
    Swal.fire({
        title: 'Konfirmasi Keluar',
        text: 'Apakah Anda yakin ingin keluar dari sistem manajemen presensi?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946', // Menggunakan skema warna merah bahaya kustom
        cancelButtonColor: '#718096',  // Warna muted kustom
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Membuka animasi loading penutup sesaat sebelum diarahkan ke halaman logout
            Swal.fire({
                title: 'Memproses Keluar...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Alihkan halaman menuju URL destruksi sesi session asli
            window.location.href = urlLogout;
        }
    });
}
</script>