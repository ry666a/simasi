    <main>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4">Ringkasan Produksi Hari Ini</h4>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stats bg-primary text-white shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Batch</h6>
                                <h2 class="mb-0">12</h2>
                            </div>
                            <i class="fas fa-layer-group fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-warning text-dark shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">In Progress</h6>
                                <h2 class="mb-0">5</h2>
                            </div>
                            <i class="fas fa-spinner fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-success text-white shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Selesai (QC OK)</h6>
                                <h2 class="mb-0">1200 <small style="font-size: 0.5em;">Unit</small></h2>
                            </div>
                            <i class="fas fa-check-double fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-danger text-white shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Produk Reject</h6>
                                <h2 class="mb-0">8</h2>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-list me-2"></i>Antrean Produksi Aktif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Batch</th>
                                        <th>Produk</th>
                                        <th>Target</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#PRD-001</td>
                                        <td>Mesin Pompa A1</td>
                                        <td>500 Unit</td>
                                        <td>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-info" style="width: 75%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info text-dark">Assembly</span></td>
                                        <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
                                    </tr>
                                    <tr>
                                        <td>#PRD-002</td>
                                        <td>Filter Industri X</td>
                                        <td>200 Unit</td>
                                        <td>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-warning text-dark">Material Prep</span></td>
                                        <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <i class="fas fa-bell me-2"></i>Aktivitas Terbaru
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-3 border-bottom pb-2">
                                <span class="text-muted d-block">10:45 - Sistem</span>
                                <strong>Batch #PRD-001</strong> berpindah ke tahap Assembly.
                            </li>
                            <li class="mb-3 border-bottom pb-2">
                                <span class="text-muted d-block">09:30 - Supervisor</span>
                                Input stok bahan baku Plat Baja masuk (500 kg).
                            </li>
                            <li>
                                <span class="text-muted d-block">08:15 - QC Admin</span>
                                <strong>Batch #PRD-000</strong> dinyatakan Selesai.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>