<main>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Master Proses Edging</h3>
                <p class="text-muted">Kelola data runtime produk workstation edging.</p>
            </div>
        </div>

        <div class="card table-card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row g-3 align-items-center">

                    <div class="col-12 col-lg-auto me-auto">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="flex-grow-1 flex-md-grow-0" style="min-width: 300px;">
                                <div class="input-group">
                                    <span class="input-group-text border-end-0 bg-transparent">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="customSearch" class="form-control border-start-0" placeholder="Cari No OP/Buyer...">
                                </div>
                            </div>
                            <button type="button" class="btn btn-reset-custom" id="resetFilter" title="Reset">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-auto text-end">
                        <button type="button" class="btn btn-add-new shadow-sm w-100-mobile" data-bs-toggle="modal" data-bs-target="#modalUploadExcel">
                            <i class="fas fa-upload me-1"></i> Upload Master
                        </button>
                    </div>

                </div>
            </div>

            <div class="card table-card shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="tableMasterplan" class="table table-custom table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Exact Code</th>
                                    <th>Exact Name</th>
                                    <th>Buyer</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>



<div class="modal fade" id="modalUploadExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-excel me-2"></i> UPLOAD MASTER EDGING</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUploadExcel" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="background: #f0f9ff; border-radius: 12px;">
                        <i class="fas fa-info-circle text-primary me-3 fa-lg"></i>
                        <div style="font-size: 13px;">
                            Gunakan format yang sesuai. Belum punya template?
                            <a href="<?= $base_url; ?>/media/format_excel/Format Master Edging.xlsx" class="fw-bold text-decoration-none text-primary">Download Template di sini</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="label mb-2">Pilih File Excel (.xlsx / .xls)</label>
                        <div class="input-group">
                            <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xlsx, .xls" required>
                        </div>
                        <div class="form-text mt-2" style="font-size: 11px;">
                            * Pastikan tidak ada kolom yang kosong pada data wajib.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm" id="btnProsesUpload">
                        <i class="fas fa-cloud-upload-alt me-2"></i> PROSES UPLOAD
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditEdging" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> EDIT MASTER EDGING: <span id="label_exact_code"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditEdging">
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="exact_code" id="edit_exact_code">

                    <div id="container_edit_detail">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2 text-muted">Mengambil data komponen...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<div id="confirmModal" class="custom-modal-backdrop">
    <div class="custom-modal-content">
        <div class="modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus data Master Proses ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeConfirmModal()">Batal</button>
            <button class="btn-confirm" id="confirmDeleteBtn">Ya, Hapus Data</button>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        let deleteIdTarget = null;

        // Inisialisasi DataTable ke dalam variabel agar bisa dipanggil
        var table = $('#tableMasterplan').DataTable({
            "processing": true,
            "serverSide": true,
            // Baris di bawah ini adalah kuncinya:
            // t = table
            // i = info (kiri)
            // p = pagination (kanan)
            "dom": "<'row'<'col-sm-12't>>" +
                "<'row mt-4 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
            "ajax": {
                "url": "<?= $base_url ?>/pages/produksi/master_proses/edging/data.php",
                "type": "POST",
                "data": function(d) {}
            },
            "language": {
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                },
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Data tidak ditemukan",
                "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1; // Untuk nomor urut
                    }
                },
                {
                    "data": "exact_code"
                },
                {
                    "data": "exact_name"
                },
                {
                    "data": "buyer"
                },
                {
                    "data": "id",
                    "orderable": false,
                   "render": function(data, type, row) {
                        // Kita ambil exact_name dari object 'row'
                        let name = row.exact_name.replace(/'/g, "\\'"); // Handle jika ada tanda kutip di nama
                        
                        return `
                            <div class="action-buttons">
                                <button class="btn-action btn-edit" onclick="editData('${data}', '${name}')" title="Edit Data Master">
                                    <i class="fas fa-edit"></i>
                                </button>
                                 <button class="btn-action btn-delete" onclick="deleteData('${data}')" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Reload saat bulan diganti
        $('#filterMonthYear').on('change', function() {
            table.draw();
        });

        // Tombol Reset untuk membersihkan filter
        $('#resetFilter').on('click', function() {
            $('#filterMonthYear').val('');
            $('#filterLine').val('');
            $('#customSearch').val('');
            table.search('').draw();
        });

        // Fungsi Pencarian Custom
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Tambahan: Jika ingin dropdown 'Line' juga memicu pencarian
        $('#filterLine').on('change', function() {
            table.search(this.value).draw();
        });


        $('#formUploadExcel').on('submit', function(e) {
            e.preventDefault();

            // 1. Inisialisasi FormData dari form
            let formData = new FormData(this);

            // 2. Tambahkan idadmin ke dalam formData (JANGAN tulis data: {} dua kali)
            formData.append('idadmin', "<?= $idadmin ?>");

            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/master_proses/edging/proses.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#btnProsesUpload').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Memproses...');
                },
                success: function(res) {
                    // Asumsi backend mengembalikan JSON {status: 'success', message: '...'}
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        $('#modalUploadExcel').modal('hide');
                        $('#formUploadExcel')[0].reset();
                        // Reload table Masterplan Boss
                        $('#tableMasterplan').DataTable().ajax.reload();
                    } else {
                        showToast(res.message, 'error');
                    }
                },
                error: function() {
                    showToast('Terjadi kesalahan sistem saat upload!', 'error');
                },
                complete: function() {
                    $('#btnProsesUpload').prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-2"></i> PROSES UPLOAD');
                }
            });
        });

    });

  function editData(id, name) {
    // Tampilkan modal
    $('#modalEditEdging').modal('show');
    
    // Set Title Modal dengan Nama, dan simpan ID di hidden input
    $('#label_exact_code').text(name); // Menampilkan Exact Name di Title
    $('#edit_exact_code').val(id);    // Tetap kirim Exact Code ke proses.php
    
    // Sisa kode AJAX tetap sama...
    $('#container_edit_detail').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Mengambil data ${name}...</p>
        </div>
    `);

    $.ajax({
        url: "<?= $base_url ?>/pages/produksi/master_proses/edging/edit.php",
        type: "POST",
        data: { exact_code: id },
        success: function(res) {
            $('#container_edit_detail').html(res);
        }
    });
}

    // Proses Update
    $('#formEditEdging').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= $base_url ?>/pages/produksi/master_proses/edging/edit_proses.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    showToast(res.message, 'success');
                    $('#modalEditEdging').modal('hide');
                    table.ajax.reload();
                } else {
                    showToast(res.message, 'error');
                }
            }
        });
    });

    function deleteData(id) {
        deleteIdTarget = id; // Simpan ID
        $('#confirmModal').addClass('show'); // Tampilkan Modal
    }

    function closeConfirmModal() {
        $('#confirmModal').removeClass('show');
        deleteIdTarget = null;
    }

    // Handler saat tombol "Ya, Hapus Data" di klik
    $('#confirmDeleteBtn').on('click', function() {
        if (deleteIdTarget) {
            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/master_proses/edging/delete.php",
                type: "POST",
                data: {
                    id: deleteIdTarget
                },
                dataType: "json",
                beforeSend: function() {
                    $('#confirmDeleteBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(res) {
                    closeConfirmModal(); // Tutup modal
                    if (res.status === 'success') {
                        showToast('Data berhasil dihapus!', 'success');
                        // Reload table tanpa reset paging (draw(false))
                        $('#tableMasterplan').DataTable().draw(false);
                    } else {
                        showToast('Gagal: ' + res.message, 'error');
                    }
                },
                error: function() {
                    closeConfirmModal();
                    showToast('Kesalahan server!', 'error');
                },
                complete: function() {
                    $('#confirmDeleteBtn').prop('disabled', false).text('Ya, Hapus Data');
                }
            });
        }
    });

    // Menutup modal jika user klik area backdrop (luar kotak)
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('custom-modal-backdrop')) {
            closeConfirmModal();
        }
    });
</script>


<style>
    /* Paksa modal-body punya scroll kalau konten kepanjangan */
    #modalEditEdging .modal-body {
        max-height: 70vh; /* 70% dari tinggi layar */
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin; /* Untuk Firefox */
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    /* Custom scrollbar untuk Chrome/Edge/Safari */
    #modalEditEdging .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    #modalEditEdging .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    #modalEditEdging .modal-body::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }
</style>