<?php
include '../../../../koneksi.php';
$exact_code = mysqli_real_escape_string($conn, $_POST['exact_code']);

$q = mysqli_query($conn, "SELECT a.*, b.nama_komponen 
                          FROM mp_edging a 
                          JOIN master_produk_komponen_detail b ON a.id_produk_komponen_detail = b.id_produk_komponen_detail 
                          WHERE a.exact_code = '$exact_code'
                          ORDER BY b.nama_komponen ASC");

if (mysqli_num_rows($q) == 0) {
    echo '<div class="alert alert-warning text-center">Data komponen tidak ditemukan.</div>';
    exit;
}

while ($d = mysqli_fetch_assoc($q)) { ?>
    <div class="component-card mb-4 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">
            <div class="fw-bold text-dark"><i class="fas fa-layer-group me-2 text-primary"></i> <?= $d['nama_komponen'] ?></div>
            <div class="badge-id">ID: <?= $d['id_produk_komponen_detail'] ?></div>
            <input type="hidden" name="id_edging[]" value="<?= $d['id'] ?>">
        </div>

        <div class="p-3">
            <div class="row g-4">
                <div class="col-md-4">
                    <span class="section-label text-primary">Edging Dimension</span>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="label-premium">Panjang</label>
                            <input type="number" class="form-control form-control-sm" name="edg_pj[]" value="<?= $d['edging_panjang'] ?>">
                        </div>
                        <div class="col-6">
                            <label class="label-premium">Lebar</label>
                            <input type="number" class="form-control form-control-sm" name="edg_lb[]" value="<?= $d['edging_lebar'] ?>">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <span class="section-label text-warning">Groving Dimension</span>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="label-premium">Panjang</label>
                            <input type="number" class="form-control form-control-sm" name="grov_pj[]" value="<?= $d['groving_panjang'] ?>">
                        </div>
                        <div class="col-6">
                            <label class="label-premium">Lebar</label>
                            <input type="number" class="form-control form-control-sm" name="grov_lb[]" value="<?= $d['groving_lebar'] ?>">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <span class="section-label text-success">Process Metrics</span>
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="label-premium">Total Meter Komponen</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="tot_meter[]" value="<?= number_format($d['total_meter_komponen'], 4) ?>">
                                <span class="input-group-text bg-white">ML</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                 <div class="col-md-8">
                    <span class="section-label text-danger">Machine Process Timing (Minutes)</span>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-group-custom">
                                <label class="label-premium">Waktu Proses J600</label>
                                <input type="text" class="form-control form-control-sm border-danger" name="wkt_j600[]" value="<?= $d['waktu_proses_j600'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group-custom">
                                <label class="label-premium">Waktu Proses J800</label>
                                <input type="text" class="form-control form-control-sm border-danger" name="wkt_j800[]" value="<?= $d['waktu_proses_j800'] ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <span class="section-label text-secondary">Edging-Groving</span>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="label-premium">E-G Pj</label>
                            <input type="number" class="form-control form-control-sm" name="eg_pj[]" value="<?= $d['edging_groving_panjang'] ?>">
                        </div>
                        <div class="col-6">
                            <label class="label-premium">E-G Lb</label>
                            <input type="number" class="form-control form-control-sm" name="eg_lb[]" value="<?= $d['edging_groving_lebar'] ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
<style>
    .component-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        transition: all 0.3s ease;
    }
    .component-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }
    .badge-id {
        font-size: 10px;
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 8px;
        border-radius: 4px;
    }
    .input-group-custom {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px;
    }
    .section-label {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 4px;
    }
</style>

<?php } ?>

