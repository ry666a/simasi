<main>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Masterplan Produksi</h3>
                <p class="text-muted">Jadwal perencanaan produksi jangka menengah dan panjang.</p>
            </div>
        </div>

        <div class="card table-card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-transparent"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="customSearch" class="form-control border-start-0" placeholder="Cari No OP/Buyer...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="month" id="filterMonthYear" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <select id="filterLine" class="form-select">
                            <option value="">Semua Line</option>
                            <option value="Line A">Line A</option>
                            <option value="Line B">Line B</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-reset-custom" id="resetFilter" title="Bersihkan Semua Filter">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>

                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-export me-2" id="exportExcel">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </button>
                        <button class="btn btn-add-new shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fas fa-plus me-1"></i> Tambah
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

<script>
    $(document).ready(function() {
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
                "url": "<?= $base_url ?>/pages/masterplan/data.php",
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

        $('#exportExcel').on('click', function() {
            // Ambil nilai filter yang sedang aktif
            var search = $('#customSearch').val();
            var monthYear = $('#filterMonthYear').val();
            var line = $('#filterLine').val();

            // Kirim ke file export.php menggunakan window.open (tab baru)
            var url = "<?= $base_url ?>/pages/masterplan/excel.php?search=" + search +
                "&month_year=" + monthYear +
                "&line=" + line;

            window.open(url, '_blank');
        });
    });

    // Fungsi placeholder untuk tombol (Anda tinggal buat logika selanjutnya)
    function editData(id) {
        alert("Edit data dengan ID: " + id);
        // Contoh: window.location.href = 'edit.php?id=' + id;
    }

    function deleteData(id) {
        if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
            alert("Proses hapus ID: " + id);
        }
    }
</script>