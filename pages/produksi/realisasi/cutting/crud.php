<?php
// Parameter 'id' ini berisi No OP yang di-encode
$edit_no_op = $_GET['id'] ?? '';
$is_edit = !empty($edit_no_op);

$current_exact_code = '';
if ($is_edit) {
    $checkOP = mysqli_query($conn, "SELECT exact_code FROM transaksi_mps WHERE no_op = '$edit_no_op' LIMIT 1");
    $dataOP = mysqli_fetch_assoc($checkOP);
    $current_exact_code = $dataOP['exact_code'] ?? '';
}
?>

<?php
$jsonkaryawan       = 'http://192.168.1.231/xampp/php/datakaryawan.php?page=karyawan';
$chkaryawan         = curl_init($jsonkaryawan);
$optionskaryawan    = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array('Content-type: application/json'),
);
curl_setopt_array($chkaryawan, $optionskaryawan); // setting curl options
$resultkaryawan = curl_exec($chkaryawan); // getting json result string
$datakaryawan = json_decode($resultkaryawan, true);
$type           = "cutting-tambah-realisasi";
?>

<main class="form-erp">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-3 rounded-0 border-bottom">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center border-top-dua">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold me-3 border-end pe-3">Realisasi Cutting</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="submit" id="btnSimpanSemua" class="btn btn-info fw-bold px-3">
                            <i class="fas fa-save me-1"></i> SAVE & RELEASE
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3" onclick="history.back()">
                            CANCEL
                        </button>
                    </div>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">No. Transaksi</span>
                    <span class="fw-bold">NEW TRANSACTION</span>
                </div>
            </div>
        </div>

        <form id="formRealisasi">
            <div class="card border-0 shadow-sm mb-3 rounded-0">
                <div class="card-header py-2 bg-white border-bottom">
                    <span class="fw-bold small text-muted text-uppercase">
                        <i class="fas fa-info-circle me-2 text-dua"></i> Informasi Utama Realisasi
                    </span>
                </div>

                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="label-erp">Mesin Utama</label>
                            <select class="form-select form-select-sm" name="id_mesin" required>
                                <option value="">-- Pilih Mesin --</option>
                                <?php
                                $sqlMesin = mysqli_query($conn, "SELECT * FROM master_mesin WHERE kategori = 'Cutting' ORDER BY nama ASC");
                                while ($rowMesin = mysqli_fetch_array($sqlMesin)) {
                                    echo "<option value='" . $rowMesin['nama'] . "'>" . $rowMesin['nama'] . "</option>";
                                }
                                ?>
                            </select>
                            <label class="label-erp mt-2">Tanggal Realisasi</label>
                            <input type="date" class="form-control form-control-sm" name="tgl_realisasi" value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="label-erp">Shift Produksi</label>
                            <select class="form-select form-select-sm" name="shift">
                                <option value="Shift 1">Shift 1 (07:00 - 15:00)</option>
                                <option value="Shift 2">Shift 2 (15:00 - 23:00)</option>
                                <option value="Non Shift">Non Shift</option>
                            </select>
                            <label class="label-erp mt-2">Downtime (Menit)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" name="downtime" value="0">
                                <span class="input-group-text bg-light">MIN</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="label-erp">Pilih Operator (Team)</label>
                            <div class="select2-container-wrapper">
                                <select class="form-select select2" multiple name="operator[]" id="selectKaryawan" required style="width:100%">
                                    <option value="">【 PILIH 】</option>
                                </select>
                            </div>
                            <div class="mt-1">
                                <small class="text-muted" style="font-size: 9px; line-height: 1;">
                                    <i class="fas fa-info-circle me-1"></i> Pilih satu atau beberapa anggota tim.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="label-erp">Keterangan / Kendala</label>
                            <textarea class="form-control form-control-sm" name="catatan" rows="3" placeholder="Opsional..." style="height: 80px;"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header py-2 bg-white border-bottom">
                    <span class="fw-bold small text-muted text-uppercase">
                        <i class="fa-solid fa-list-ol me-2 text-dua"></i> Production Line Items
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-erp mb-0">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="280">SCAN NO. OP</th>
                                <th>NAMA KOMPONEN / PRODUK</th>
                                <th width="120" class="text-center">QTY (PCS)</th>
                                <th width="120" class="text-center">TIME (MIN)</th>
                                <th width="80" class="text-center">PROC.</th>
                                <th width="45" class="bg-light text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-light-subtle" style="background-color: #f1f5f9;">
                            <tr>
                                <td></td>
                                <td class="p-0">
                                    <div class="d-flex flex-column justify-content-center h-100">
                                        <input type="text" class="input-grid fw-bold" id="input_no_op" placeholder="NO OP...">
                                        <div id="display_exact_name" class="px-2 mt-2 text-primary fw-bold" style="font-size: 9px; line-height: 1.2; min-height: 12px;">Exact Name :</div>
                                    </div>
                                </td>
                                <td class="p-0">
                                    <select class="select-grid" id="input_komponen">
                                        <option value="">-- Pilih Komponen --</option>
                                    </select>
                                </td>
                                <td class="p-0"><input type="number" class="input-grid text-center fw-bold text-primary" id="input_jml_realisasi" value="0"></td>
                                <td class="p-0"><input type="number" class="input-grid text-center" id="input_waktu_aktual" value="0"></td>
                                <td class="p-0">
                                    <select class="select-grid text-center" id="input_proses">
                                        <option value="P1">P1</option>
                                        <option value="P2">P2</option>
                                    </select>
                                </td>
                                <td class="text-center align-middle bg-light">
                                    <button type="button" class="btn btn-default btn-sm w-100" id="addRow">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tbody id="realisasiBodyTmp" class="border-top-0">
                        </tbody>
                        <tfoot>
                            <tr class="bg-light fw-bold" style="border-top: 2px solid #cbd5e1;">
                                <td colspan="3" class="text-end py-2 align-middle" style="font-size: 11px;">TOTAL :</td>
                                <td id="totalQty" class="text-center text-primary py-2 align-middle" style="font-size: 13px;">0 <small>PCS</small></td>
                                <td id="totalTime" class="text-center text-primary py-2 align-middle" style="font-size: 13px;">0 <small>MIN</small></td>
                                <td colspan="2" class="bg-light"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">

                <div class="card-header py-2 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-muted text-uppercase">
                        <i class="fas fa-layer-group me-2 text-dua"></i> Perhitungan Lembar Partikel
                    </span>

                    <button type="button" class="btn btn-default btn-sm fw-bold" id="addPartikel">
                        <i class="fas fa-plus me-1"></i> TAMBAH LEMBAR
                    </button>
                </div>


                <div class="card-body p-0">
                    <table class="table table-bordered mb-0" style="font-size: 12px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3">Ketebalan (mm)</th>
                                <th class="py-2 px-3" width="300">Jumlah Tarikan (Lembar)</th>
                                <th class="py-2 text-center" width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="partikelBody">
                            <tr>
                                <td class="p-0">
                                    <input type="number" name="ketebalan[]" class="input-grid fw-bold" placeholder="KETEBALAN">
                                </td>
                                <td class="p-0">
                                    <input type="number" name="jumlah[]" class="input-grid fw-bold" placeholder="JUMLAH">
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-link text-danger btn-remove-partikel p-0"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</main>


<div id="confirmModal" class="custom-modal-backdrop">
    <div class="custom-modal-content">
        <div class="modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus data realisasi ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeConfirmModal()">Batal</button>
            <button type="button" class="btn-confirm" id="confirmDeleteBtn">Ya, Hapus Data</button>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> EDIT ITEM REALISASI</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="formEditTmp">
                    <input type="hidden" id="edit_id_tmp" name="id">

                    <div class="mb-3">
                        <label class="label-erp">No. OP</label>
                        <input type="text" class="form-control form-control-sm fw-bold bg-light" id="edit_no_op" readonly>
                        <small id="edit_display_exact" class="text-primary fw-bold" style="font-size: 10px;"></small>
                    </div>

                    <div class="mb-3">
                        <label class="label-erp">Pilih Komponen</label>
                        <select class="form-select form-select-sm fw-bold" id="edit_komponen" name="id_cutting">
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="label-erp">Qty Realisasi (PCS)</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-primary" id="edit_jml_realisasi" name="jml_realisasi">
                        </div>
                        <div class="col-6">
                            <label class="label-erp">Waktu Aktual (MIN)</label>
                            <input type="number" class="form-control form-control-sm" id="edit_waktu_aktual" name="waktu_aktual">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="label-erp">Proses</label>
                        <select class="form-select form-select-sm" id="edit_proses" name="proses">
                            <option value="P1">P1</option>
                            <option value="P2">P2</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-info btn-sm px-4" id="btnUpdateTmp">SIMPAN PERUBAHAN</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKonfirmasiSimpan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-question-circle text-primary fa-3x"></i>
                </div>
                <h5 class="fw-bold">Simpan Realisasi?</h5>
                <p class="text-muted small">Pastikan semua data sudah benar. Data yang disimpan tidak dapat diubah kembali.</p>

                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light btn-sm w-100 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" id="confirmExecuteSimpan">YA, SIMPAN</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // --- FUNGSI GLOBAL ---
    window.closeConfirmModal = function() {
        $('#confirmModal').removeClass('show');
        idToDelete = null;
    };

    $(document).ready(function() {
        var parentExactCode = "";
        var idToDelete = null;

        // Helper untuk Autocomplete Multiple
        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        // --- 1. AUTOCOMPLETE (INPUT ATAS) ---
        $("#input_no_op").on("keyup", function() {
            if ($(this).val() === "") {
                parentExactCode = "";
                $("#display_exact_name").text("");
                $("#input_komponen").html('<option value="">-- Pilih Komponen --</option>');
            }
        }).autocomplete({
            source: function(request, response) {
                var currentValues = split(this.element.val());
                $.ajax({
                    url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/data.php?page=cari-noop",
                    type: "GET",
                    dataType: "json",
                    data: {
                        q: extractLast(request.term),
                        exact_code: parentExactCode,
                        exclude: currentValues.join(',')
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.no_op + " | " + item.exact_name,
                                value: item.no_op,
                                exact_code: item.exact_code,
                                exact_name: item.exact_name
                            };
                        }));
                    }
                });
            },
            focus: function() {
                return false;
            },
            select: function(event, ui) {
                var terms = split(this.value);
                terms.pop();
                terms.push(ui.item.value);
                if (terms.length === 1) {
                    parentExactCode = ui.item.exact_code;
                    $("#display_exact_name").text(ui.item.exact_name);
                    loadKomponenAcuan(ui.item.value);
                }
                terms.push("");
                this.value = terms.join(", ");
                var that = this;
                setTimeout(function() {
                    $(that).focus();
                    that.setSelectionRange(that.value.length, that.value.length);
                }, 10);
                return false;
            }
        });

        function loadKomponenAcuan(noOpAcuan) {
            $("#input_komponen").html('<option value="">Loading...</option>');
            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/data.php?page=get-komponen-awal",
                type: "GET",
                data: {
                    no_op: noOpAcuan
                },
                dataType: "json",
                success: function(data) {
                    let options = '<option value="">-- Pilih Komponen --</option>';
                    $.each(data, function(key, val) {
                        options += `<option value="${val.id_cutting}">${val.nama_komponen}</option>`;
                    });
                    $("#input_komponen").html(options);
                }
            });
        }

        // --- 2. ADD TO TMP ---
        $('#addRow').on('click', function() {
            const no_op_raw = $('#input_no_op').val();
            const id_cutting = $('#input_komponen').val();
            const jml_realisasi = $('#input_jml_realisasi').val();
            const waktu_aktual = $('#input_waktu_aktual').val();
            const proses = $('#input_proses').val();

            if (no_op_raw === "" || id_cutting === "" || jml_realisasi === "0") {
                showToast("Data belum lengkap!", 'error');
                return;
            }

            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/proses.php?page=add-tmp",
                type: "POST",
                data: {
                    no_op_raw: no_op_raw,
                    id_cutting: id_cutting,
                    jml_realisasi: jml_realisasi,
                    waktu_aktual: waktu_aktual,
                    proses: proses,
                    idadmin: "<?= $idadmin ?>"
                },
                dataType: "json",
                success: function(res) {
                    if (res.success) {
                        $('#input_no_op').val("");
                        $('#input_komponen').html('<option value="">-- Pilih Komponen --</option>');
                        $('#input_jml_realisasi').val(0);
                        $('#input_waktu_aktual').val(0);
                        $('#display_exact_name').text("");
                        showToast('Berhasil ditambahkan', 'success');
                        loadTableTmp();
                    }
                }
            });
        });

        // --- 3. LOAD TABLE TMP (READ ONLY MODE) ---
        window.loadTableTmp = function() {
            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/data.php?page=list-tmp",
                type: "GET",
                data: {
                    idadmin: "<?= $idadmin ?>"
                },
                dataType: "json",
                success: function(res) {
                    let html = '';
                    let no = 1;
                    if (res && res.length > 0) {
                        res.forEach(item => {
                            // Kita buat baris ini bisa diklik (cursor pointer) untuk trigger Edit Modal
                            html += `
                    <tr class="bg-white row-edit-trigger" data-id="${item.id}" style="cursor:pointer;">
                        <td class="text-center align-middle fw-bold" style="font-size:10px;">${no++}</td>
                        <td class="align-middle px-3">
                            <div class="d-flex align-items-start">
                                <button type="button" class="btn btn-link p-0 me-2 btn-copy-tmp" data-id="${item.id}">
                                    <i class="fas fa-copy text-primary" style="font-size: 13px;"></i>
                                </button>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold" style="font-size: 11px;">${item.no_op}</span>
                                    <span class="text-muted" style="font-size: 9px;">${item.exact_name}</span>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle px-3">
                            <div class="fw-bold text-dark" style="font-size: 12px;">${item.nama_komponen}</div>
                            <div class="text-muted small" style="font-size: 10px;">
                                <i class="fas fa-info-circle me-1"></i> Kebutuhan: ${item.kebutuhan_pcs.toLocaleString()} PCS
                            </div>
                        </td>
                        <td class="text-center align-middle fw-bold text-primary" style="font-size: 13px;">
                            ${item.jml_realisasi}
                        </td>
                        <td class="text-center align-middle" style="font-size: 12px;">
                            ${item.waktu_aktual}
                        </td>
                        <td class="text-center align-middle fw-bold" style="font-size: 12px;">
                            ${item.proses}
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 btn-hapus-tmp" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="7" class="py-4 text-center text-muted small">BELUM ADA ITEM</td></tr>';
                    }
                    $('#realisasiBodyTmp').html(html);
                    calculateTotals();
                }
            });
        }

        // --- 4. COPY & DELETE OPERASI ---

        // --- EVENT: KLIK BARIS UNTUK EDIT ---
        $(document).on('click', '.row-edit-trigger', function(e) {
            // Abaikan jika yang diklik adalah tombol copy atau hapus
            if ($(e.target).closest('.btn-copy-tmp, .btn-hapus-tmp').length) return;

            const id_tmp = $(this).data('id');

            // 1. Ambil data baris ini dari database via AJAX
            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/data.php?page=get-single-tmp",
                type: "GET",
                data: {
                    id: id_tmp
                },
                dataType: "json",
                success: function(res) {
                    if (res) {
                        // 2. Isi form modal
                        $('#edit_id_tmp').val(res.id);
                        $('#edit_no_op').val(res.no_op);
                        $('#edit_display_exact').text(res.exact_name);
                        $('#edit_jml_realisasi').val(res.jml_realisasi);
                        $('#edit_waktu_aktual').val(res.waktu_aktual);
                        $('#edit_proses').val(res.proses);

                        // 3. Load dropdown komponen khusus untuk produk ini
                        loadKomponenEdit(res.id, res.id_cutting);

                        // 4. Tampilkan Modal
                        $('#modalEdit').modal('show');
                    }
                }
            });
        });

        // Fungsi load komponen di dalam modal edit
        function loadKomponenEdit(rowId, selectedIdCutting) {
            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/data.php?page=get-komponen",
                type: "GET",
                data: {
                    id_tmp: rowId
                },
                dataType: "json",
                success: function(data) {
                    let options = '<option value="">-- Pilih Komponen --</option>';
                    $.each(data, function(key, val) {
                        let isSel = (val.id_cutting == selectedIdCutting) ? 'selected' : '';
                        options += `<option value="${val.id_cutting}" ${isSel}>${val.nama_komponen}</option>`;
                    });
                    $('#edit_komponen').html(options);
                }
            });
        }

        // --- EVENT: SIMPAN PERUBAHAN ---
        $('#btnUpdateTmp').on('click', function() {
            let btn = $('#btnUpdateTmp');
            // Simpan teks asli: "SIMPAN PERUBAHAN"
            const originalHtml = 'SIMPAN PERUBAHAN';

            const id_tmp = $('#edit_id_tmp').val();
            const id_cutting = $('#edit_komponen').val();
            const jml = $('#edit_jml_realisasi').val();
            const waktu = $('#edit_waktu_aktual').val();
            const proses = $('#edit_proses').val();

            if (!id_cutting || jml <= 0) {
                showToast("Data tidak valid!", 'error');
                return;
            }

            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/proses.php?page=update-all-tmp",
                type: "POST",
                data: {
                    id: id_tmp,
                    id_cutting: id_cutting,
                    jml_realisasi: jml,
                    waktu_aktual: waktu,
                    proses: proses,
                    idadmin: "<?= $idadmin ?>"
                },
                dataType: "json",
                beforeSend: function() {
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>SAVING...');
                },
                success: function(res) {
                    if (res.success) {
                        setTimeout(() => {
                            // KUNCI PERBAIKAN: Kembalikan tombol ke kondisi semula sebelum tutup modal
                            btn.prop('disabled', false).html(originalHtml);

                            $('#modalEdit').modal('hide');
                            showToast('Baris berhasil diupdate!', 'success');
                            loadTableTmp();
                        }, 1500);
                    } else {
                        showToast('Gagal update: ' + res.message, 'error');
                        btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function() {
                    // JANGAN LUPA: Jika koneksi putus (error system), tombol juga harus balik normal
                    showToast('Kesalahan sistem!', 'error');
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        $(document).on('click', '.btn-copy-tmp', function() {
            const id = $(this).data('id');
            $.post("<?= $base_url ?>/pages/produksi/realisasi/cutting/proses.php?page=copy-tmp", {
                id: id,
                idadmin: "<?= $idadmin ?>"
            }, function(res) {
                if (res.success) {
                    loadTableTmp();
                }
            }, 'json');
        });

        $(document).on('click', '.btn-hapus-tmp', function() {
            idToDelete = $(this).data('id');
            $('#confirmModal').addClass('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (idToDelete) {
                $.getJSON("<?= $base_url ?>/pages/produksi/realisasi/cutting/proses.php", {
                    page: "delete-tmp",
                    id: idToDelete,
                    idadmin: "<?= $idadmin ?>"
                }, function(res) {
                    if (res.success) {
                        closeConfirmModal();
                        loadTableTmp();
                    }
                });
            }
        });

        // --- 5. FOOTER CALCULATION ---
        window.calculateTotals = function() {
            let totalQty = 0;
            let totalTime = 0;

            // Loop setiap baris di body tabel TMP
            $('#realisasiBodyTmp tr').each(function() {
                // Ambil teks dari kolom Qty (index 3), hapus koma jika ada, lalu konversi ke float
                let qtyText = $(this).find('td:eq(3)').text().replace(/,/g, '').trim();
                let qty = parseFloat(qtyText) || 0;

                // Ambil teks dari kolom Time (index 4), hapus koma jika ada, lalu konversi ke float
                let timeText = $(this).find('td:eq(4)').text().replace(/,/g, '').trim();
                let time = parseFloat(timeText) || 0;

                totalQty += qty;
                totalTime += time;
            });

            // Update angka di TFOOT dengan format ribuan
            $('#totalQty').html(totalQty.toLocaleString() + ' <small>PCS</small>');
            $('#totalTime').html(totalTime.toLocaleString() + ' <small>MIN</small>');
        }

        // --- 6. TAMBAH PARTIKEL ---
        $('#addPartikel').on('click', function() {
            let newRow = `
                    <tr>
                        <td class="p-0">
                            <input type="number" name   ="ketebalan[]" class="input-grid fw-bold" placeholder="KETEBALAN">
                        </td>
                        <td class="p-0">
                            <input type="number" name="jumlah[]" class="input-grid fw-bold" placeholder="JUMLAH">
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-link text-danger btn-remove-partikel p-0"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>`;
            $('#partikelBody').append(newRow);
        });

        // Event Hapus Baris Partikel
        $(document).on('click', '.btn-remove-partikel', function() {
            // Sisakan minimal 1 baris jika diinginkan, atau hapus saja semua
            $(this).closest('tr').remove();
        });

        loadTableTmp(); // Start


        // 7. Inisialisasi Select2
        $('.select2').select2({
            placeholder: "【 PILIH 】",
            allowClear: false,
            closeOnSelect: false,
        });

        // 2. Tarik Data dari API
        $.ajax({
            url: "http://192.168.1.231/xampp/php/datakaryawan.php?page=karyawan",
            type: "GET",
            dataType: "json",
            success: function(response) {
                let data = response.datakaryawanapi || [];
                let $select = $("#selectKaryawan");

                $select.empty();
                $select.append('<option value="">【 PILIH 】</option>');

                $.each(data, function(index, item) {
                    let pembagian = item.pembagian2_nama ? item.pembagian2_nama.toUpperCase() : "";

                    // Kita pakai filter yang umum: Jika ada nama pembagiannya
                    if (pembagian !== "") {
                        let optionText = item.pegawai_nama + " (" + item.pembagian2_nama + ")";

                        // Gunakan format value pemisah ± seperti aplikasi Boss sebelumnya
                        $select.append(
                            $('<option>', {
                                value: item.pegawai_nip + '±' + item.pegawai_nama,
                                text: optionText
                            })
                        );
                    }
                });

                // Trigger change agar Select2 sadar ada data baru
                $select.trigger('change');
                $("#loadStatusKaryawan").html('<i class="fas fa-check-circle text-success me-1"></i> Data Karyawan Siap.');
            },
            error: function(xhr, status, error) {
                console.error("Gagal memuat data karyawan:", error);
                $("#loadStatusKaryawan").html('<i class="fas fa-times-circle text-danger me-1"></i> Gagal memuat API Karyawan.');
            }
        });

        // 1. Trigger Modal saat tombol Simpan Utama diklik
        $('#btnSimpanSemua').on('click', function() {
            // Validasi Operator dulu sebelum buka modal
            let operatorCount = $('#selectKaryawan').val();
            if (!operatorCount || operatorCount.length === 0) {
                showToast('Pilih minimal 1 operator!', 'error');
                return;
            }

            // Jika ok, baru munculkan modal konfirmasi
            $('#modalKonfirmasiSimpan').modal('show');
        });

        // 2. Eksekusi Simpan saat tombol di dalam Modal diklik
        $('#confirmExecuteSimpan').on('click', function() {
            // 1. Identifikasi tombol dengan jelas agar tidak tabrakan
            let btnModal = $(this);
            let btnUtama = $('#btnSimpanSemua');

            // Teks asli untuk tombol modal (karena ini di dalam modal konfirmasi)
            const textAsliModal = 'YA, SIMPAN';
            // Teks asli untuk tombol utama di halaman
            const textAsliUtama = '<i class="fas fa-save me-2"></i>SIMPAN PERMANEN';

            let form = document.getElementById('formRealisasi');
            let formData = new FormData(form);
            formData.append('idadmin', '<?= $idadmin ?>');

            $.ajax({
                url: "<?= $base_url ?>/pages/produksi/realisasi/cutting/proses.php?page=simpan-realisasi",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function() {
                    // Matikan kedua tombol agar tidak ada double input
                    btnModal.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                    btnUtama.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>SAVING...');
                },
                success: function(res) {
                    if (res.success) {
                        // Tutup modal
                        $('#modalKonfirmasiSimpan').modal('hide');
                        showToast('Data berhasil disimpan! No: ' + res.no_transaksi, 'success');

                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        // Jika gagal (misal: kapasitas OP tidak cukup)
                        $('#modalKonfirmasiSimpan').modal('hide'); // Tutup modal biar user bisa benerin data
                        showToast('Gagal: ' + res.message, 'error');

                        // Kembalikan status tombol
                        btnModal.prop('disabled', false).text(textAsliModal);
                        btnUtama.prop('disabled', false).html(textAsliUtama);
                    }
                },
                error: function(xhr, status, error) {
                    $('#modalKonfirmasiSimpan').modal('hide');
                    showToast('Kesalahan Sistem!', 'error');

                    // Kembalikan status tombol
                    btnModal.prop('disabled', false).text(textAsliModal);
                    btnUtama.prop('disabled', false).html(textAsliUtama);
                }
            });
        });
    });
</script>

<style>
    .form-erp {
        background-color: #f8fafc;
        font-family: 'Segoe UI', sans-serif;
    }

    .label-erp {
        font-size: 10px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .form-erp .form-control-sm,
    .form-erp .form-select-sm {
        border-radius: 3px;
        border: 1px solid #cbd5e1;
        font-size: 12px;
    }

    .table-erp {
        border: 1px solid #cbd5e1 !important;
    }

    .table-erp thead th {
        background-color: #f1f5f9;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        padding: 10px;
        text-transform: uppercase;
        border: 1px solid #cbd5e1 !important;
    }

    .table-erp tbody td {
        padding: 4px 10px !important;
        vertical-align: middle;
    }

    .input-grid,
    .select-grid {
        width: 100%;
        border: none;
        padding: 6px 10px;
        font-size: 12px;
        outline: none;
        background: transparent;
    }

    /* Ganti background saat input aktif/fokus */
    .input-grid:focus,
    .select-grid:focus,
    .form-erp .form-control-sm:focus {
        background-color: #E0E4E8 !important;
        /* Warna biru muda sangat lembut */
        outline: none;
        border-color: #E0E4E8;
        /* Opsional: kasih border biru tipis */
    }

    .bg-light-subtle {
        background-color: #f8fafc;
    }

    .ui-autocomplete {
        background: #fff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 9999 !important;
    }

    .ui-menu-item {
        padding: 8px 12px;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
    }

    .ui-state-active {
        background: #3498db !important;
        color: #fff !important;
        border: none;
    }
</style>