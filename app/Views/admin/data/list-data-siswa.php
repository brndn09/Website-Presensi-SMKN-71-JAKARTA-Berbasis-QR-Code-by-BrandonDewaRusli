<!-- 1. LOAD SWEETALERT2 (Letakkan di bagian atas atau dalam template header) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card-body p-0">
    <?php if (!$empty): ?>
        <div class="table-responsive">
            <table class="table table-hover align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3" width="40">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkAll">
                                <label class="custom-control-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">No</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">NIS</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Nama Siswa</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Jenis Kelamin</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Kelas</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">No. HP</th>
                        <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($data as $value): ?>
                        <tr>
                            <td class="px-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="checkbox-table" class="custom-control-input checkbox-table" id="item-<?= $value['id_siswa']; ?>" value="<?= $value['id_siswa']; ?>">
                                    <label class="custom-control-label" for="item-<?= $value['id_siswa']; ?>"></label>
                                </div>
                            </td>
                            <td><span class="text-xs font-weight-bold text-muted"><?= $i; ?></span></td>
                            <td><span class="text-xs font-weight-bold"><?= $value['nis']; ?></span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm font-weight-bold text-dark"><?= $value['nama_siswa']; ?></h6>
                                </div>
                            </td>
                            <td><span class="text-xs"><?= $value['jenis_kelamin']; ?></span></td>
                            <td>
                                <span class="badge badge-soft-primary font-weight-bold" style="font-size: 11px;">
                                    <?= $value['kelas']; ?>
                                </span>
                            </td>
                            <td><span class="text-xs text-muted"><?= $value['no_hp'] ?: '-'; ?></span></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= base_url('admin/siswa/edit/' . $value['id_siswa']); ?>" 
                                       class="btn-action btn-soft-primary" title="Edit Data">
                                        <i class="material-icons">edit</i>
                                    </a>

                                    <a href="<?= base_url('admin/qr/siswa/' . $value['id_siswa'] . '/download'); ?>" 
                                       class="btn-action btn-soft-success" title="Download QR">
                                        <i class="material-icons">qr_code_2</i>
                                    </a>

                                    <!-- Form Hapus dengan Class Khusus -->
                                    <form action="<?= base_url('admin/siswa/delete/' . $value['id_siswa']); ?>" method="post" class="d-inline form-delete">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn-action btn-soft-danger btn-delete-trigger" title="Hapus">
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
            <i class="material-icons text-muted mb-2" style="font-size: 48px;">search_off</i>
            <h5 class="text-muted font-weight-light">Data tidak ditemukan</h5>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Styling Tabel Modern */
    .text-xxs { font-size: 0.7rem; letter-spacing: 0.05rem; }
    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    
    .table td {
        vertical-align: middle !important;
        border-bottom: 1px solid #f8f9fc;
        padding: 12px 8px;
    }

    /* Soft Badge Styles */
    .badge-soft-primary {
        background-color: rgba(67, 97, 238, 0.1);
        color: #4361ee;
        padding: 5px 10px;
        border-radius: 8px;
    }

    /* Action Button Styles */
    .btn-action {
        width: 34px;
        height: 34px;
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
    .btn-soft-primary:hover { background: #4361ee; color: #fff; }

    .btn-soft-success { background: rgba(46, 196, 182, 0.1); color: #2ec4b6; }
    .btn-soft-success:hover { background: #2ec4b6; color: #fff; }

    .btn-soft-danger { background: rgba(230, 57, 70, 0.1); color: #e63946; }
    .btn-soft-danger:hover { background: #e63946; color: #fff; }

    /* Custom Checkbox */
    .custom-checkbox .custom-control-label::before {
        border-radius: 6px;
        border: 1.5px solid #cbd5e1;
    }
    
    .gap-2 { gap: 0.5rem; }

    /* Custom SweetAlert Button Radius */
    .swal2-styled { border-radius: 10px !important; }
</style>

<script>
    $(document).ready(function() {
        // Fungsi Check All
        $('#checkAll').on('click', function() {
            $('.checkbox-table').prop('checked', this.checked);
        });

        // SweetAlert2 untuk Konfirmasi Hapus Modern
        $('.btn-delete-trigger').on('click', function(e) {
            let form = $(this).closest('form');
            
            Swal.fire({
                title: 'Hapus data siswa?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
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

        // Opsional: Notifikasi Sukses Otomatis jika ada Flashdata
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('success'); ?>',
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>
    });
</script>