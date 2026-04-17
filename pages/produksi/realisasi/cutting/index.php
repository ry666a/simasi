<main>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-3 rounded-0 border-bottom">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center border-top-dua">
                <div class="">
                    <h3 class="fw-bold">Realisasi Cutting</h3>
                    <p class="text-muted">Kelola data realisasi produk workstation cutting.</p>
                </div>
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
                                    <input type="text" id="customSearch" class="form-control border-start-0" placeholder="Cari No Transaksi/Mesin...">
                                </div>
                            </div>

                            <div class="w-100-mobile">
                                <input type="month" id="filterMonthYear" class="form-control">
                            </div>

                            <button type="button" class="btn btn-reset-custom" id="resetFilter" title="Reset">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-auto text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="<?= $base_url; ?>/produksi/raalisasi/cutting/crud" class="btn btn-info fw-bold px-3">
                              <i class="fas fa-plus me-1"></i> Tambah Realisasi
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card table-card shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="tableMasterplan" class="table table-custom table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Mesin</th>
                                    <th>Komponen</th>
                                    <th>Waktu Actual</th>
                                    <th>Ganti Palet</th>
                                    <th>Downtime</th>
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



<div class="modal fade" id="modalDetailRealisasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-alt me-2"></i> DETAIL REALISASI</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContentBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Set default filter ke bulan & tahun sekarang (Opsional, tapi bagus buat UX)
        if ($('#filterMonthYear').val() === "") {
            let now = new Date();
            let month = ("0" + (now.getMonth() + 1)).slice(-2);
            let today = now.getFullYear() + "-" + month;
            $('#filterMonthYear').val(today);
        }

        var table = $('#tableMasterplan').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [
                [0, "desc"]
            ], // Default sort ke No Transaksi Desc
            "dom": "<'row'<'col-sm-12't>>" +
                "<'row mt-4 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
            "ajax": {
                "url": "<?= $base_url ?>/pages/produksi/realisasi/cutting/data.php?page=data-realisasi",
                "type": "POST",
                "data": function(d) {
                    // Mengirim filter ke server
                    d.filter_month_year = $('#filterMonthYear').val();
                    d.idadmin = "<?= $idadmin ?>";
                }
            },
            "language": {
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                },
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Data tidak ditemukan",
                "processing": "<div class='spinner-border text-primary' role='status'></div>"
            },
            "columns": [{
                    "data": "no_transaksi"
                },
                {
                    "data": "tanggal"
                },
                {
                    "data": "nama_mesin"
                },
                {
                    "data": "total_komponen"
                },
                {
                    "data": "total_waktu_aktual"
                },
                {
                    "data": "ganti_palet"
                },
                {
                    "data": "downtime"
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <div class="action-buttons">
                                <button class="btn-action btn-delete" onclick="deleteData('${data}')" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Event Pencarian Custom (Keyup)
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Event Filter Bulan (Change)
        $('#filterMonthYear').on('change', function() {
            table.draw();
        });

        // Event Reset Filter
        $('#resetFilter').on('click', function() {
            $('#filterMonthYear').val('');
            $('#customSearch').val('');
            table.search('').draw();
            showToast('Filter telah direset', 'info');
        });

        // Gunakan delegasi ke BODY agar elemen yang baru dirender DataTable tetap terbaca
        // Gunakan delegasi agar tidak 'Illegal invocation'
        $(document).on('click', '.btn-view-detail', function(e) {
            e.preventDefault();

            // Ambil ID dari data-id link yang diklik
            var idRealisasi = $(this).data('id');

            // 1. Reset isi modal & Tampilkan loader
            $('#detailContentBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');

            // 2. Munculkan Modal Bootstrap (Cara Standar)
            var myModal = new bootstrap.Modal(document.getElementById('modalDetailRealisasi'));
            myModal.show();

            // 3. Tarik data dari detail.php
            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/detail.php?page=get-detail-view",
                type: "POST",
                data: {
                    id: idRealisasi
                },
                success: function(res) {
                    $('#detailContentBody').html(res);
                },
                error: function() {
                    showToast('Gagal memuat data!', 'error');
                }
            });
        });
    });




    // Pastikan fungsi close ini menghapus state dengan benar
    function closeDetailModal() {
        let modal = $('#modalDetailRealisasi');
        modal.removeClass('show');

        // Opsional: Paksa sembunyi setelah animasi selesai (jika pake transition)
        setTimeout(function() {
            if (!modal.hasClass('show')) {
                modal.css('display', 'none');
            }
        }, 300);
    }






    function deleteData(id) {
        console.log(id);
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
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/delete.php",
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