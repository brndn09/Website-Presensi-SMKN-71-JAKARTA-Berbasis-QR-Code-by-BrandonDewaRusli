<!-- 1. LOAD SWEETALERT2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card-body p-0">
    <?php if (!$empty): ?>
        <div class="table-responsive">
            <table class="table table-hover align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <!-- Kolom Checkbox Dihapus -->
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 px-4">No</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">NIP / NUPTK</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Nama Guru</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Jenis Kelamin</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($data as $value): ?>
                        <tr>
                            <!-- Kolom Checkbox Dihapus -->
                            <td class="px-4">
                                <span class="text-xs font-weight-bold text-muted"><?= $i; ?></span>
                            </td>
                            <td>
                                <span class="text-xs font-weight-bold"><?= $value['nuptk']; ?></span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm font-weight-bold text-dark"><?= $value['nama_guru']; ?></h6>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= ($value['jenis_kelamin'] == 'Laki-laki' || $value['jenis_kelamin'] == '1') ? 'badge-soft-info' : 'badge-soft-warning' ?> font-weight-bold" style="font-size: 11px;">
                                    <?= ($value['jenis_kelamin'] == '1') ? 'Laki-laki' : (($value['jenis_kelamin'] == '2') ? 'Perempuan' : $value['jenis_kelamin']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= base_url('admin/guru/edit/' . $value['id_guru']); ?>" 
                                       class="btn-action btn-soft-primary" title="Edit Data">
                                        <i class="material-icons">edit</i>
                                    </a>

                                    <form action="<?= base_url('admin/guru/delete/' . $value['id_guru']); ?>" method="post" class="d-inline form-delete-guru">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn-action btn-soft-danger btn-delete-guru" title="Hapus">
                                            <i class="material-icons">delete_outline</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="material-icons text-muted mb-2" style="font-size: 48px;">person_off</i>
            <h5 class="text-muted font-weight-light">Data guru tidak ditemukan</h5>
        </div>
    <?php endif; ?>
</div>

<style>
    .text-xxs { font-size: 0.7rem; letter-spacing: 0.05rem; }
    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    
    .table td {
        vertical-align: middle !important;
        border-bottom: 1px solid #f8f9fc;
        padding: 12px 8px;
    }

    /* Soft Badge Styles */
    .badge-soft-info { background-color: rgba(58, 134, 255, 0.1); color: #3a86ff; padding: 5px 10px; border-radius: 8px; }
    .badge-soft-warning { background-color: rgba(255, 159, 67, 0.1); color: #ff9f43; padding: 5px 10px; border-radius: 8px; }

    /* Action Button Styles */
    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        border: none;
        text-decoration: none !important;
        cursor: pointer;
    }

    .btn-soft-primary { background: rgba(67, 97, 238, 0.1); color: #4361ee; }
    .btn-soft-primary:hover { background: #4361ee; color: #fff; transform: translateY(-2px); }

    .btn-soft-danger { background: rgba(230, 57, 70, 0.1); color: #e63946; }
    .btn-soft-danger:hover { background: #e63946; color: #fff; transform: translateY(-2px); }

    .gap-2 { gap: 0.5rem; }
    .swal2-styled { border-radius: 10px !important; }
</style>

<script>
    $(document).ready(function() {
        // Notifikasi Flashdata (Hanya satu fungsi untuk semua pesan)
        <?php if (session()->getFlashdata('msg')) : ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('error') ? 'error' : 'success' ?>',
                title: '<?= session()->getFlashdata('error') ? 'Gagal!' : 'Berhasil!' ?>',
                text: '<?= session()->getFlashdata('msg'); ?>',
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>

        // SweetAlert2 Konfirmasi Hapus Guru
        $('.btn-delete-guru').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            
            Swal.fire({
                title: 'Hapus data guru?',
                text: "Seluruh data terkait guru ini akan terhapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>