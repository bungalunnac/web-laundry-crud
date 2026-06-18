<?php
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshClean Laundry - Riwayat Pesanan</title>
    <meta name="description" content="Riwayat semua pesanan laundry yang sudah diselesaikan.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <i data-lucide="washing-machine" size="36"></i>
                FreshClean Laundry
            </div>
            <nav class="nav-tabs">
                <a href="<?= base_url('/') ?>" class="nav-tab">
                    <i data-lucide="layers" size="16"></i> Layanan
                </a>
                <a href="<?= base_url('orders') ?>" class="nav-tab">
                    <i data-lucide="clipboard-list" size="16"></i> Pesanan
                </a>
                <a href="<?= base_url('orders/history') ?>" class="nav-tab nav-tab-active">
                    <i data-lucide="history" size="16"></i> Riwayat
                </a>
            </nav>
            <a href="<?= base_url('orders') ?>" class="btn btn-outline">
                <i data-lucide="arrow-left" size="16"></i>
                Kembali
            </a>
        </header>

        <!-- Main Content -->
        <main>
            <!-- Flash Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle" size="20"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Page Title -->
            <div class="page-section-header">
                <div>
                    <h1 class="page-title">Riwayat Pesanan</h1>
                    <p class="page-subtitle">Semua pesanan yang telah diselesaikan</p>
                </div>
                <div class="stat-badge stat-badge-muted">
                    <span><?= count($history) ?></span> total riwayat
                </div>
            </div>

            <!-- Stats Summary -->
            <?php if (!empty($history)):
                $totalDone    = 0;
                $totalTaken   = 0;
                $totalRevenue = 0;
                foreach ($history as $h) {
                    if ($h['completion_reason'] === 'dikerjakan_dan_diambil') $totalDone++;
                    else $totalTaken++;
                    $totalRevenue += $h['total_price'];
                }
            ?>
            <div class="stats-row">
                <div class="stat-card stat-card-green">
                    <div class="stat-card-icon"><i data-lucide="check-circle-2" size="24"></i></div>
                    <div class="stat-card-value"><?= $totalDone ?></div>
                    <div class="stat-card-label">Dikerjakan & Diambil</div>
                </div>
                <div class="stat-card stat-card-orange">
                    <div class="stat-card-icon"><i data-lucide="package-x" size="24"></i></div>
                    <div class="stat-card-value"><?= $totalTaken ?></div>
                    <div class="stat-card-label">Diambil Tanpa Dikerjakan</div>
                </div>
                <div class="stat-card stat-card-blue">
                    <div class="stat-card-icon"><i data-lucide="wallet" size="24"></i></div>
                    <div class="stat-card-value" style="font-size:1.1rem;"><?= formatRupiah($totalRevenue) ?></div>
                    <div class="stat-card-label">Total Pendapatan</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- History Table -->
            <?php if (empty($history)): ?>
                <div class="empty-state">
                    <i data-lucide="clock" class="empty-icon"></i>
                    <h2 class="empty-title">Belum ada riwayat</h2>
                    <p class="empty-desc">Riwayat akan muncul setelah ada pesanan yang diselesaikan.</p>
                    <a href="<?= base_url('orders') ?>" class="btn btn-primary">
                        <i data-lucide="clipboard-list" size="20"></i>
                        Kelola Pesanan
                    </a>
                </div>
            <?php else: ?>
                <div class="history-table-wrapper">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Keterangan Penyelesaian</th>
                                <th>Tanggal Masuk</th>
                                <th>Tanggal Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $i => $item): ?>
                                <tr class="history-row">
                                    <td class="history-num"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="history-customer">
                                            <div class="order-avatar order-avatar-sm">
                                                <?= strtoupper(substr($item['customer_name'], 0, 1)) ?>
                                            </div>
                                            <?= htmlspecialchars($item['customer_name']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($item['service_name']) ?></td>
                                    <td><?= $item['quantity'] ?> <?= htmlspecialchars($item['service_unit']) ?></td>
                                    <td class="history-price"><?= formatRupiah($item['total_price']) ?></td>
                                    <td>
                                        <?php if ($item['completion_reason'] === 'dikerjakan_dan_diambil'): ?>
                                            <span class="status-badge status-done-full">
                                                <i data-lucide="check-circle-2" size="12"></i>
                                                Dikerjakan & Diambil
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-taken-only">
                                                <i data-lucide="package-x" size="12"></i>
                                                Diambil Tanpa Dikerjakan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="history-date"><?= date('d M Y', strtotime($item['created_at'])) ?></td>
                                    <td class="history-date"><?= date('d M Y, H:i', strtotime($item['completed_at'])) ?></td>
                                    <td>
                                        <button
                                            class="btn btn-danger btn-icon"
                                            onclick="confirmDelete('<?= base_url('orders/delete/' . $item['id']) ?>')"
                                            title="Hapus riwayat"
                                        >
                                            <i data-lucide="trash-2" size="14"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php if (!empty($item['notes'])): ?>
                                <tr class="history-notes-row">
                                    <td></td>
                                    <td colspan="8">
                                        <span class="notes-label">Catatan:</span>
                                        <?= htmlspecialchars($item['notes']) ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function confirmDelete(url) {
            if (confirm('Hapus riwayat ini secara permanen?')) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
