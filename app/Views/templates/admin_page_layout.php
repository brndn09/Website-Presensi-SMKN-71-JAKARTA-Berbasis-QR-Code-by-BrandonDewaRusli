
<!DOCTYPE html>
<html lang="id">

<?= $this->include("templates/head") ?>

<body>
   <div>
      <?= $this->include("templates/sidebar") ?>
      <div class="main-panel">

         <?= $this->include("templates/navbar") ?>

         <?= $this->renderSection("content") ?>

         <?= $this->include("templates/footer") ?>

         <?php
         // echo $this->include('templates/fixed_plugin')
         ?>

      </div>
   </div>

   <?= $this->include("templates/js") ?>

   <script>
      var BaseConfig = {
         baseURL: '<?= base_url() ?>',
         csrfTokenName: '<?= csrf_token() ?>',
         textOk: "Ok",
         textCancel: "Batalkan"
      };
   </script>

    <?= $this->renderSection("scripts") ?>

    <!-- Modal Ganti Password -->
    <div class="modal fade" id="modalGantiPassword" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="titleModal">
                    <i class="material-icons align-middle mr-2">security</i> Keamanan Akun
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="<?= base_url('profile/update_password'); ?>" method="POST" id="formKustomPassword">
                <?= csrf_field(); ?> 
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Silakan masukkan password lama Anda untuk memverifikasi identitas, kemudian buat password baru.</p>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Password Saat Ini</label>
                        <div class="input-group">
                            <input type="password" name="old_password" class="form-control border-right-0" placeholder="Masukkan password lama" required>
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0"><i class="material-icons text-muted">lock</i></span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="pass_baru" class="form-control border-right-0" placeholder="Minimal 6 karakter" required minlength="6">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0"><i class="material-icons text-muted">vpn_key</i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">Ulangi Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="konf_pass" class="form-control border-right-0" placeholder="Sama dengan password baru" required>
                            <div class="input-group-append">
                                <span class="input-group-text bg-white border-left-0"><i class="material-icons text-muted">check_circle</i></span>
                            </div>
                        </div>
                        <small id="pesan_error" class="text-danger font-weight-bold mt-2 d-none">Konfirmasi password tidak cocok!</small>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3" style="border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpan" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Notifikasi dan Validasi -->
<script>
    $(document).ready(function() {
        // 1. Validasi Kecocokan Password Baru (Client Side)
        $('#konf_pass, #pass_baru').on('keyup', function() {
            let pass = $('#pass_baru').val();
            let conf = $('#konf_pass').val();

            if (conf !== "" && pass !== conf) {
                $('#pesan_error').removeClass('d-none');
                $('#btnSimpan').attr('disabled', true);
            } else {
                $('#pesan_error').addClass('d-none');
                $('#btnSimpan').attr('disabled', false);
            }
        });

        // 2. Notifikasi SweetAlert2 dari Flashdata Controller
        <?php if (session()->getFlashdata('msg')) : ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('error') ? 'error' : 'success' ?>',
                title: '<?= session()->getFlashdata('error') ? 'Gagal!' : 'Berhasil!' ?>',
                text: '<?= session()->getFlashdata('msg'); ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        <?php endif; ?>
    });
</script>

</body>
</html>