<?php
// Ambil ID dari URL yang sudah di-rewrite (.htaccess)
$edit_no_op = $_GET['id'] ?? '';
$is_edit = !empty($edit_no_op);

$current_exact_code = '';
if ($is_edit) {
    $checkOP = mysqli_query($conn, "SELECT exact_code FROM transaksi_mps WHERE no_op = '$edit_no_op' LIMIT 1");
    $dataOP = mysqli_fetch_assoc($checkOP);
    $current_exact_code = $dataOP['exact_code'] ?? '';
}
?>

<main>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-3 rounded-1 border-bottom">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold me-3 border-end pe-3 text-dark">
                        <i class="fas <?= $is_edit ? 'fa-edit' : 'fa-plus-circle' ?> me-1"></i>
                        Master Proses Cutting
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="submit" form="formAddCutting" id="btnSubmit" class="btn btn-primary fw-bold px-4" disabled>
                            <i class="fas fa-save me-1"></i> <?= $is_edit ? 'UPDATE' : 'SAVE' ?>
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3" onclick="window.location.href='<?= $base_url ?>/produksi/masterproses/cutting'">
                            CLOSE
                        </button>
                    </div>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Status Mode</span>
                    <span class="badge <?= $is_edit ? 'bg-warning text-dark' : 'bg-success' ?> fw-bold">
                        <?= $is_edit ? 'EDITING: '.$edit_no_op : 'NEW ENTRY' ?>
                    </span>
                </div>
            </div>
        </div>

        <form id="formAddCutting" class="form-erp">
            <input type="hidden" name="mode" value="<?= $is_edit ? 'edit' : 'add' ?>">
            <input type="hidden" name="idadmin" value="<?= $idadmin; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="no_op" value="<?= $edit_no_op ?>">
                <input type="hidden" name="exact_code" value="<?= $current_exact_code ?>">
            <?php endif; ?>

            <div class="card border-0 shadow-sm mb-3 rounded-1">
                <div class="card-body p-3 bg-light-subtle">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="label-erp">Produk Utama (Exact Name)</label>
                            <select class="form-select form-select-sm select2" id="exact_code" name="exact_code" required <?= $is_edit ? 'disabled' : '' ?>>
                                <option value="">-- Search Product from Masterplan --</option>
                                <?php
                                $prodQuery = mysqli_query($conn, "SELECT DISTINCT exact_code, exact_name FROM transaksi_mps ORDER BY exact_name ASC");
                                while ($p = mysqli_fetch_assoc($prodQuery)) {
                                    $selected = ($current_exact_code == $p['exact_code']) ? 'selected' : '';
                                    echo "<option value='" . $p['exact_code'] . "' $selected>" . $p['exact_code'] . ' | ' . $p['exact_name'] . "</option>";
                                }
                                ?>
                            </select>
                            <?php if ($is_edit): ?>
                                <small class="text-muted mt-1 d-block italic font-monospace">Note: Product code is locked in edit mode.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="componentContainer" style="display:none;" class="card border-0 shadow-sm rounded-1">
                <div class="card-header bg-white py-2 border-bottom">
                    <span class="fw-bold small text-uppercase text-secondary">Component List & Runtime Configuration</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-erp mb-0" id="tableComponent">
                        <thead>
                            <tr>
                                <th class="ps-3">NAMA KOMPONEN</th>
                                <th class="text-center" width="180">DIMENSI (mm)</th>
                                <th class="text-center" width="180">SISI EDGING</th>
                                <th class="text-center" width="80">QTY</th>
                                <th class="text-center bg-light" width="200">RUNTIME (MIN/PCS)</th>
                            </tr>
                        </thead>
                        <tbody id="componentList">
                            </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</main>

<style>
/* ERP Reset & Layout */
body { background-color: #f3f4f6; }

.label-erp {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    display: block;
    margin-bottom: 4px;
}

/* Form Controls - Rigid Enterprise Style */
.form-erp .form-select-sm,
.form-erp .form-control-sm {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 13px;
    color: #334155;
}

/* Grid Table Style */
.table-erp {
    border: 1px solid #cbd5e1 !important;
}

.table-erp thead th {
    background-color: #f1f5f9;
    color: #475569;
    font-size: 11px;
    font-weight: 800;
    padding: 10px;
    border: 1px solid #cbd5e1 !important;
    vertical-align: middle;
}

.table-erp tbody td {
    padding: 8px 10px !important;
    font-size: 13px;
    border: 1px solid #cbd5e1 !important;
    vertical-align: middle;
}

/* Borderless Input inside Table */
.input-grid-erp {
    width: 100%;
    border: none;
    background: transparent;
    padding: 5px;
    font-weight: 700;
    text-align: center;
    outline: none;
}

.input-grid-erp:focus {
    background-color: #fffef0;
}

/* Edging Visual Boxes */
.edging-wrapper { display: flex; justify-content: center; gap: 3px; }
.edging-box { 
    display: flex; border: 1px solid #cbd5e1; 
    font-size: 10px; font-weight: 800; border-radius: 2px;
    overflow: hidden;
}
.edging-label { padding: 2px 5px; background: #64748b; color: #fff; }
.edging-value { padding: 2px 7px; background: #fff; }
.box-panjang { border-color: #3498db; }
.box-panjang .edging-label { background: #3498db; }
.box-pendek { border-color: #f39c12; }
.box-pendek .edging-label { background: #f39c12; }

/* Action Toolbar Aksen */
.card-body.py-2.px-3 {
    border-top: 3px solid #3498db;
}

.bg-light-subtle { background-color: #f8fafc !important; }

/* Runtime Input Group */
.runtime-group {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #3498db;
    border-radius: 4px;
    overflow: hidden;
}
.runtime-group .unit {
    background: #3498db;
    color: #fff;
    padding: 4px 8px;
    font-size: 10px;
    font-weight: 800;
}
</style>

<script>
$(document).ready(function() {
    // 1. Inisialisasi Select2
    $('#exact_code').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Search Product --',
        allowClear: true,
        width: '100%'
    });

    // 2. Fungsi Load Data
    function loadComponents(exactCode) {
        if (!exactCode) {
            $('#componentContainer').hide();
            return;
        }

        $.ajax({
            url: "<?= $base_url ?>/pages/produksi/master_proses/cutting/get_components.php",
            type: 'POST',
            data: { 
                exact_code: exactCode,
                no_op: '<?= $edit_no_op ?>'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#componentContainer').show();
                $('#componentList').html(`<tr><td colspan="5" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i><div class="text-muted small">FETCHING DATA...</div></td></tr>`);
                $('#btnSubmit').prop('disabled', true);
            },
            success: function(data) {
                let html = '';
                if (data && data.length > 0) {
                    data.forEach(function(item) {
                        let runtimeVal = item.runtime ? item.runtime : '';
                        html += `
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">${item.nama_komponen}</div>
                                <div class="text-muted" style="font-size:10px">ID: ${item.id_komponen}</div>
                                <input type="hidden" name="id_komponen[]" value="${item.id_komponen}">
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fw-bold" style="font-family:monospace">${item.dimensi_panjang} x ${item.dimensi_lebar}</span>
                            </td>
                            <td class="text-center">
                                <div class="edging-wrapper">
                                    <div class="edging-box box-panjang">
                                        <div class="edging-label">P</div>
                                        <div class="edging-value">${item.sisi_panjang}</div>
                                    </div>
                                    <div class="edging-box box-pendek">
                                        <div class="edging-label">S</div>
                                        <div class="edging-value">${item.sisi_pendek}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-bold text-primary">${item.jml_komponen}</td>
                            <td class="bg-light">
                                <div class="runtime-group mx-auto" style="max-width: 140px;">
                                    <input type="number" step="0.01" name="runtime[]" 
                                           class="input-grid-erp" 
                                           value="${runtimeVal}" placeholder="0.00" required>
                                    <div class="unit">MIN</div>
                                </div>
                            </td>
                        </tr>`;
                    });
                    $('#btnSubmit').prop('disabled', false);
                } else {
                    html = `<tr><td colspan="5" class="text-center py-5 text-muted small">NO COMPONENTS FOUND.</td></tr>`;
                    $('#btnSubmit').prop('disabled', true);
                }
                $('#componentList').html(html);
            }
        });
    }

    // 3. Events
    $('#exact_code').on('change', function() {
        loadComponents($(this).val());
    });

    <?php if ($is_edit): ?>
        loadComponents('<?= $current_exact_code ?>');
    <?php endif; ?>

    $('#formAddCutting').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSubmit');
        const originalHtml = btn.html();

        $.ajax({
            url: "<?= $base_url ?>/pages/produksi/master_proses/cutting/save.php",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>SAVING...');
            },
            success: function(res) {
                if (res.status === 'success') {
                    showToast(res.message, 'success');
                    setTimeout(() => {
                        window.location.href = "<?= $base_url ?>/produksi/masterproses/cutting";
                    }, 1500);
                } else {
                    showToast(res.message, 'error');
                    btn.prop('disabled', false).html(originalHtml);
                }
            }
        });
    });
});
</script>