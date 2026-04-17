<?php
include '../../../../koneksi.php';

if (isset($_GET['page']) && $_GET['page'] == 'get-detail-view') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    $q = mysqli_query($conn, "SELECT * FROM mp_cutting_realisasi WHERE id = '$id'");
    $h = mysqli_fetch_assoc($q);

    if (!$h) {
        echo "<div class='alert alert-danger'>Data tidak ditemukan!</div>";
        exit;
    }
?>
    <style>
        /* KODE LAMA BOSS TETAP DISINI */
        .premium-detail-header {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #edf2f7;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .premium-detail-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        /* --- PERBAIKAN: CLASS BARU BIAR GAK TABRAKAN --- */
        .header-item-premium {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .label-premium {
            font-size: 10px !important;
            letter-spacing: 1.2px;
            color: #94a3b8 !important;
            font-weight: 800 !important;
            text-transform: uppercase;
        }

        .text-premium {
            font-size: 14px !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            /* Biar gak turun baris */
        }

        .icon-box-premium {
            width: 32px;
            height: 32px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .badge-completed-premium {
            background: #ecfdf5;
            color: #059669;
            font-size: 10px;
            padding: 2px 10px;
            border-radius: 6px;
            font-weight: 700;
            border: 1px solid #d1fae5;
            margin-bottom: 4px;
            display: inline-block;
            width: fit-content;
        }

        /* KODE TABEL DAN LAINNYA DIBAWAH TETAP AMAN */
        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            border-left: 4px solid #3b82f6;
            padding-left: 10px;
            margin: 25px 0 15px 0;
            text-transform: uppercase;
        }

        .table-premium {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .table-premium thead {
            background-color: #f8fafc;
        }

        .table-premium thead th {
            font-size: 11px;
            color: #475569;
            text-transform: uppercase;
            padding: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .badge-op {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .badge-op i {
            color: #3b82f6;
            margin-right: 6px;
        }

        .summary-box {
            background: #fdfdfd;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 15px;
        }

        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid #edf2f7 !important;
            }
        }


        .premium-detail-header {
            background: #ffffff;
            border-radius: 16px;
            padding: 0;
            /* Padding kita pindah ke kolom biar border rapi */
            border: 1px solid #edf2f7;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .premium-detail-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            z-index: 10;
        }

        /* KUNCI: Membuat semua kolom sama tinggi dan punya garis pembatas */
        .header-col-premium {
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            border-right: 1px solid #edf2f7;
            /* Garis pembatas konsisten */
            height: 100%;
        }

        /* Hapus border di kolom terakhir */
        .header-col-premium.last {
            border-right: none;
        }

        .header-item-premium {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .label-premium {
            font-size: 10px !important;
            letter-spacing: 1.2px;
            color: #94a3b8 !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .text-premium {
            font-size: 14px !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        .icon-box-premium {
            width: 38px;
            height: 38px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sub-text-premium {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .content-box-premium {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
        }

        .box-title-premium {
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            padding: 12px 20px;
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .box-body-premium {
            padding: 20px;
        }

        .stat-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .stat-line:last-child {
            border-bottom: none;
        }

        .stat-label-clean {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .stat-value-clean {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .runtime-summary-clean {
            margin-top: 15px;
            padding: 15px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>

    <div class="premium-detail-header">
        <div class="row g-0 align-items-stretch">
            <div class="col-md-3">
                <div class="header-col-premium">
                    <div class="label-premium">Reference Number</div>
                    <div class="text-premium">
                        <div class="icon-box-premium"><i class="fas fa-hashtag"></i></div>
                        <div class="text-primary"><?= $h['no_transaksi'] ?></div>
                    </div>
                    <div class="sub-text-premium text-muted" style="visibility: hidden;">-</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="header-col-premium">
                    <div class="label-premium">Production Date</div>
                    <div class="text-premium">
                        <div class="icon-box-premium"><i class="far fa-calendar-alt"></i></div>
                        <div><?= date('d M Y', strtotime($h['tanggal'])) ?></div>
                    </div>
                    <div class="sub-text-premium text-muted" style="visibility: hidden;">-</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="header-col-premium">
                    <div class="label-premium">Workstation / Shift</div>
                    <div class="text-premium">
                        <div class="icon-box-premium"><i class="fas fa-cogs"></i></div>
                        <div>
                            <div><?= $h['nama_mesin'] ?></div>
                            <div class="sub-text-premium">SHIFT <?= $h['shift'] ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="header-col-premium last">
                    <div class="label-premium">Result Summary</div>
                    <div class="text-premium">
                        <div class="icon-box-premium" style="background: #ecfdf5; color: #059669;"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <div class="text-success"><?= number_format($h['total_komponen']) ?> <small style="font-size: 10px;">PCS</small></div>
                            <div class="sub-text-premium text-success">COMPLETED</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="section-title">Tim Operator Terlibat</div>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php
        $qOp = mysqli_query($conn, "SELECT DISTINCT nip, nama FROM mp_cutting_realisasi_operator WHERE id_realisasi = '$id'");
        while ($o = mysqli_fetch_assoc($qOp)) {
            echo "<div class='badge-op'><i class='fas fa-user-circle'></i> $o[nip] - $o[nama]</div>";
        }
        ?>
    </div>

    <div class="section-title">Rincian Item & Waktu Aktual</div>
    <div class="table-premium mb-4">
        <table class="table table-hover mb-0" style="font-size: 12px;">
            <thead>
                <tr>
                    <th class="ps-3">No. OP</th>
                    <th>Nama Komponen</th>
                    <th class="text-center">Proses</th>
                    <th class="text-end">Qty Realisasi</th>
                    <th class="text-end pe-3">Waktu (Min)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $qD = mysqli_query($conn, "SELECT d.*, c.no_op, p.nama_komponen 
                                           FROM mp_cutting_realisasi_detail d
                                           JOIN mp_cutting c ON d.id_cutting = c.id
                                           JOIN master_produk_komponen_detail p ON c.id_produk_komponen_detail = p.id_produk_komponen_detail
                                           WHERE d.id_realisasi = '$id'");
                while ($d = mysqli_fetch_assoc($qD)) {
                    echo "<tr>
                            <td class='ps-3 fw-bold text-secondary'>$d[no_op]</td>
                            <td class='fw-semibold'>$d[nama_komponen]</td>
                            <td align='center'><span class='badge bg-light text-dark border'>$d[proses]</span></td>
                            <td align='right'>" . number_format($d['jml_realisasi']) . "</td>
                            <td align='right' class='pe-3 fw-bold text-info'>" . number_format($d['waktu_aktual'], 2) . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-7">
            <div class="content-box-premium">
                <div class="box-title-premium">
                    <i class="fas fa-comment-alt me-2"></i> Catatan & Keterangan
                </div>
                <div class="box-body-premium">
                    <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6;">
                        <?= !empty($h['keterangan']) ? nl2br($h['keterangan']) : '<span class="text-muted italic">Tidak ada catatan tambahan untuk transaksi ini.</span>' ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="content-box-premium">
                <div class="box-title-premium">
                    <i class="fas fa-chart-line me-2"></i> Summary Performance
                </div>
                <div class="box-body-premium">
                    <div class="stat-line">
                        <span class="stat-label-clean">Ganti Palet</span>
                        <span class="stat-value-clean"><?= $h['ganti_palet'] ?> <small class="fw-normal text-muted">x</small></span>
                    </div>
                    <div class="stat-line">
                        <span class="stat-label-clean">Total Downtime</span>
                        <span class="stat-value-clean text-danger"><?= number_format($h['downtime']) ?> <small class="fw-normal text-muted">Min</small></span>
                    </div>

                    <div class="runtime-summary-clean">
                        <div>
                            <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase;">Actual Runtime</div>
                            <div style="font-size: 18px; font-weight: 800; color: #3b82f6;">
                                <?= number_format($h['total_waktu_aktual'], 2) ?> <small style="font-size: 11px; font-weight: 400;">Minutes</small>
                            </div>
                        </div>
                        <i class="fas fa-stopwatch text-muted" style="font-size: 20px; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    exit;
}
