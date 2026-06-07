<div class="card-body table-responsive p-0">
    <table class="table table-hover align-items-center mb-0">
        <thead class="bg-light">
            <tr>
                <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 px-4" style="width: 8%;">No</th>
                <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7" style="width: 15%;">Tingkat</th>
                <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7" style="width: 15%;">Jurusan</th>
                <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7" style="width: 15%;">Indeks</th>
                <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Wali Kelas</th>
                <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center" style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php $i = 1; foreach ($data as $value): ?>
                    <tr id="row-kelas-<?= $value['id_kelas']; ?>">
                        <td class="px-4 py-3">
                            <span class="text-xs font-weight-bold text-muted"><?= $i; ?></span>
                        </td>
                        <td class="py-3">
                            <span class="text-sm font-weight-bold text-dark"><?= $value['tingkat']; ?></span>
                        </td>
                        <td class="py-3">
                            <span class="text-sm text-muted uppercase"><?= $value['jurusan']; ?></span>
                        </td>
                        <td class="py-3">
                            <span class="text-sm text-muted"><?= $value['index_kelas']; ?></span>
                        </td>
                        <td class="py-3">
                            <h6 class="mb-0 text-sm font-weight-bold text-dark"><?= $value['nama_wali_kelas'] ?? '-'; ?></h6>
                        </td>
                        <td class="text-center py-3">
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="<?= base_url('admin/kelas/edit/' . $value['id_kelas']); ?>" 
                                   class="btn btn-sm btn-soft-primary p-2 shadow-none border-0" 
                                   title="Edit Data Kelas" style="border-radius: 8px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="material-icons" style="font-size: 18px;">edit</i>
                                </a>
                                
                                <button type="button" 
                                        onclick="deleteKelasItem('<?= $value['id_kelas']; ?>', '<?= $value['tingkat'] . ' ' . $value['jurusan'] . ' ' . $value['index_kelas']; ?>')" 
                                        class="btn btn-sm btn-soft-danger p-2 shadow-none border-0" 
                                        title="Hapus Data Kelas" style="border-radius: 8px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="material-icons" style="font-size: 18px;">delete_forever</i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php $i++; endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <p class="text-muted mb-0 font-weight-light">Belum ada data kelas untuk ditampilkan</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .text-xxs { font-size: 0.65rem !important; letter-spacing: 0.05rem; }
    .text-xs { font-size: 0.75rem !important; }
    .text-sm { font-size: 0.875rem !important; }
    .uppercase { text-transform: uppercase; }
    
    /* Desain Soft Tombol Aksi */
    .btn-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; transition: 0.2s ease-in-out; }
    .btn-soft-primary:hover { background-color: #4361ee; color: white; transform: translateY(-1px); }
    
    .btn-soft-danger { background-color: rgba(230, 57, 70, 0.1); color: #e63946; transition: 0.2s ease-in-out; }
    .btn-soft-danger:hover { background-color: #e63946; color: white; transform: translateY(-1px); }

    /* Struktur Layout Border Tabel */
    .table thead th { border-bottom: 1px solid #edf2f7; vertical-align: middle; }
    .table td { vertical-align: middle; border-bottom: 1px solid #f8f9fc; }
    .table tbody tr:last-child td { border-bottom: none; }
    .gap-2 { gap: 8px !important; }
</style>