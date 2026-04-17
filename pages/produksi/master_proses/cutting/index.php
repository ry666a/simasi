<main>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Master Proses Cutting</h3>
                <p class="text-muted">Kelola data runtime produk workstation cutting.</p>
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

                            <div class="w-100-mobile">
                                <input type="month" id="filterMonthYear" class="form-control">
                            </div>

                            <div class="w-100-mobile">
                                <select id="filterLine" class="form-select">
                                    <option value="">Semua Line</option>
                                    <option value="Line A">Line A</option>
                                    <option value="Line B">Line B</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-reset-custom" id="resetFilter" title="Reset">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-auto text-end">
                        <a href="<?= $base_url; ?>/produksi/masterproses/cutting/crud" class="btn btn-add-new shadow-sm w-100-mobile">
                            <i class="fas fa-plus me-1"></i> Tambah Master
                        </a>
                    </div>

                </div>
            </div>

            <div class="card table-card shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="tableMasterplan" class="table table-custom table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No OP</th>
                                    <th>Exact Code</th>
                                    <th>Exact Name</th>
                                    <th>Buyer</th>
                                    <th>Tgl Packing</th>
                                    <th>Jumlah</th>
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
                "url": "<?= $base_url ?>/pages/produksi/master_proses/cutting/data.php",
                "type": "POST",
                "data": function(d) {
                    d.filter_month_year = $('#filterMonthYear').val(); // Akan mengirim '2026-04'
                    d.filter_line = $('#filterLine').val();
                }
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
                    "data": "no_op"
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
                    "data": "tgl_packing"
                },
                {
                    "data": "jumlah"
                },
                {
                    "data": "id",
                    "orderable": false, // Matikan sort untuk kolom tombol aksi
                    "render": function(data, type, row) {
                        return `
                            <div class="action-buttons">
                                <button class="btn-action btn-edit" onclick="editData('${data}')" title="Edit Data">
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

    });

    function editData(no_op) {
        // Tanpa .php karena sudah pakai RewriteRule
        window.location.href = "<?= $base_url ?>/produksi/masterproses/cutting/crud/" + encodeURIComponent(no_op);
    }

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
                url: "<?= $base_url ?>/pages/produksi/master_proses/cutting/delete.php",
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