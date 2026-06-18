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
    <title>FreshClean Laundry - Pesanan Aktif</title>
    <meta name="description" content="Kelola pesanan laundry aktif. Pantau status pengerjaan dan selesaikan pesanan pelanggan.">
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
                <a href="<?= base_url('orders') ?>" class="nav-tab nav-tab-active">
                    <i data-lucide="clipboard-list" size="16"></i> Pesanan
                </a>
                <a href="<?= base_url('orders/history') ?>" class="nav-tab">
                    <i data-lucide="history" size="16"></i> Riwayat
                </a>
            </nav>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i data-lucide="plus" size="20"></i>
                Pesanan Baru
            </button>
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

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <i data-lucide="alert-circle" size="20"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Page Title -->
            <div class="page-section-header">
                <div>
                    <h1 class="page-title">Pesanan Aktif</h1>
                    <p class="page-subtitle">Daftar pesanan yang sedang dalam proses pengerjaan</p>
                </div>
                <div class="stat-badge">
                    <span><?= count($orders) ?></span> pesanan aktif
                </div>
            </div>

            <!-- Orders List -->
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i data-lucide="inbox" class="empty-icon"></i>
                    <h2 class="empty-title">Tidak ada pesanan aktif</h2>
                    <p class="empty-desc">Belum ada pesanan yang masuk. Tambahkan pesanan pertama sekarang.</p>
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i data-lucide="plus" size="20"></i>
                        Tambah Pesanan
                    </button>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <!-- Order Header -->
                            <div class="order-card-header">
                                <div class="order-identity">
                                    <div class="order-avatar">
                                        <?= strtoupper(substr($order['customer_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h3 class="order-customer"><?= htmlspecialchars($order['customer_name']) ?></h3>
                                        <span class="order-service-name">
                                            <i data-lucide="tag" size="12"></i>
                                            <?= htmlspecialchars($order['service_name']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-header-right">
                                    <?php
                                        $statusClass = match($order['status']) {
                                            'belum_dikerjakan'  => 'status-pending',
                                            'sedang_dikerjakan' => 'status-inprogress',
                                            default             => 'status-done',
                                        };
                                        $statusLabel = match($order['status']) {
                                            'belum_dikerjakan'  => 'Belum Dikerjakan',
                                            'sedang_dikerjakan' => 'Sedang Dikerjakan',
                                            default             => 'Selesai',
                                        };
                                        $statusIcon = match($order['status']) {
                                            'belum_dikerjakan'  => 'clock',
                                            'sedang_dikerjakan' => 'loader',
                                            default             => 'check-circle',
                                        };
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <i data-lucide="<?= $statusIcon ?>" size="12"></i>
                                        <?= $statusLabel ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Order Details -->
                            <div class="order-details">
                                <div class="order-detail-item">
                                    <span class="detail-label">Jumlah</span>
                                    <span class="detail-value"><?= $order['quantity'] ?> <?= htmlspecialchars($order['service_unit']) ?></span>
                                </div>
                                <div class="order-detail-item">
                                    <span class="detail-label">Total</span>
                                    <span class="detail-value detail-price"><?= formatRupiah($order['total_price']) ?></span>
                                </div>
                                <div class="order-detail-item">
                                    <span class="detail-label">Tanggal Masuk</span>
                                    <span class="detail-value"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
                                </div>
                                <?php if (!empty($order['notes'])): ?>
                                <div class="order-detail-item order-notes">
                                    <span class="detail-label">Catatan</span>
                                    <span class="detail-value"><?= htmlspecialchars($order['notes']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Order Actions -->
                            <div class="order-actions">
                                <!-- Update Status -->
                                <?php if ($order['status'] === 'belum_dikerjakan'): ?>
                                    <form method="post" action="<?= base_url('orders/status/' . $order['id']) ?>" style="flex:1;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="sedang_dikerjakan">
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i data-lucide="play-circle" size="16"></i>
                                            Mulai Kerjakan
                                        </button>
                                    </form>
                                <?php elseif ($order['status'] === 'sedang_dikerjakan'): ?>
                                    <form method="post" action="<?= base_url('orders/status/' . $order['id']) ?>" style="flex:1;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="belum_dikerjakan">
                                        <button type="submit" class="btn btn-outline btn-block">
                                            <i data-lucide="rotate-ccw" size="16"></i>
                                            Tandai Belum
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Selesaikan -->
                                <button
                                    class="btn btn-secondary btn-block"
                                    style="flex:1;"
                                    onclick="openCompleteModal(<?= $order['id'] ?>, '<?= htmlspecialchars($order['customer_name']) ?>')"
                                >
                                    <i data-lucide="check-circle" size="16"></i>
                                    Selesaikan
                                </button>

                                <!-- Hapus -->
                                <button
                                    class="btn btn-danger btn-icon"
                                    onclick="confirmDelete('<?= base_url('orders/delete/' . $order['id']) ?>')"
                                    title="Hapus pesanan"
                                >
                                    <i data-lucide="trash-2" size="16"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <!-- Modal: Tambah Pesanan Baru -->
        <div class="modal-overlay" id="addOrderModal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Pesanan Baru</h2>
                    <button class="close-btn" onclick="closeAddModal()">
                        <i data-lucide="x" size="24"></i>
                    </button>
                </div>

                <form method="post" action="<?= base_url('orders/create') ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label">Nama Pelanggan</label>
                        <input
                            type="text"
                            name="customer_name"
                            class="form-control"
                            placeholder="Contoh: Budi Santoso"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Layanan</label>
                        <select name="service_id" id="serviceSelect" class="form-control" required onchange="updatePricePreview()">
                            <option value="">-- Pilih Layanan --</option>
                            <?php foreach ($services as $service): ?>
                                <option
                                    value="<?= $service['id'] ?>"
                                    data-price="<?= $service['price'] ?>"
                                    data-unit="<?= htmlspecialchars($service['unit']) ?>"
                                >
                                    <?= htmlspecialchars($service['name']) ?> — <?= formatRupiah($service['price']) ?>/<?= htmlspecialchars($service['unit']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah / Berat</label>
                        <div style="display:flex; gap:0.5rem; align-items:center;">
                            <input
                                type="number"
                                name="quantity"
                                id="quantityInput"
                                class="form-control"
                                placeholder="Contoh: 3"
                                required
                                min="0.1"
                                step="0.1"
                                style="flex:1;"
                                oninput="updatePricePreview()"
                            />
                            <span id="unitLabel" class="unit-display">—</span>
                        </div>
                    </div>

                    <!-- Preview Harga -->
                    <div class="price-preview" id="pricePreview" style="display:none;">
                        <i data-lucide="calculator" size="16"></i>
                        <span>Total: <strong id="totalPrice">Rp 0</strong></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea
                            name="notes"
                            class="form-control"
                            placeholder="Catatan khusus untuk pesanan ini..."
                            rows="2"
                        ></textarea>
                    </div>

                    <div class="flex justify-between items-center mt-4" style="padding-top: 1rem; border-top: 1px solid var(--border);">
                        <button type="button" class="btn btn-outline" onclick="closeAddModal()">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus" size="16"></i>
                            Buat Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Selesaikan Pesanan -->
        <div class="modal-overlay" id="completeModal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Selesaikan Pesanan</h2>
                    <button class="close-btn" onclick="closeCompleteModal()">
                        <i data-lucide="x" size="24"></i>
                    </button>
                </div>

                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                    Pilih alasan penyelesaian untuk pesanan <strong id="completeCustomerName"></strong>:
                </p>

                <form method="post" id="completeForm" action="">
                    <?= csrf_field() ?>

                    <div class="reason-options">
                        <label class="reason-card" id="reason1">
                            <input type="radio" name="completion_reason" value="dikerjakan_dan_diambil" required>
                            <div class="reason-card-body">
                                <div class="reason-icon reason-icon-success">
                                    <i data-lucide="check-circle-2" size="24"></i>
                                </div>
                                <div>
                                    <strong>Sudah Dikerjakan & Diambil</strong>
                                    <p>Laundry sudah selesai dikerjakan dan sudah diambil oleh pelanggan.</p>
                                </div>
                            </div>
                        </label>

                        <label class="reason-card" id="reason2">
                            <input type="radio" name="completion_reason" value="diambil_tanpa_dikerjakan" required>
                            <div class="reason-card-body">
                                <div class="reason-icon reason-icon-warning">
                                    <i data-lucide="package-x" size="24"></i>
                                </div>
                                <div>
                                    <strong>Belum Dikerjakan, Sudah Diambil</strong>
                                    <p>Pelanggan mengambil laundry sebelum sempat dikerjakan.</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-between items-center mt-4" style="padding-top: 1rem; border-top: 1px solid var(--border);">
                        <button type="button" class="btn btn-outline" onclick="closeCompleteModal()">Batal</button>
                        <button type="submit" class="btn btn-secondary">
                            <i data-lucide="check" size="16"></i>
                            Konfirmasi Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // ─── Add Modal ───────────────────────────────────────────────────────────
        const addModal     = document.getElementById('addOrderModal');
        const serviceSelect = document.getElementById('serviceSelect');
        const quantityInput = document.getElementById('quantityInput');
        const unitLabel     = document.getElementById('unitLabel');
        const pricePreview  = document.getElementById('pricePreview');
        const totalPrice    = document.getElementById('totalPrice');

        function openAddModal() {
            addModal.style.display = 'flex';
        }
        function closeAddModal() {
            addModal.style.display = 'none';
        }

        function updatePricePreview() {
            const selected = serviceSelect.options[serviceSelect.selectedIndex];
            const price    = parseFloat(selected?.dataset?.price || 0);
            const unit     = selected?.dataset?.unit || '—';
            const qty      = parseFloat(quantityInput.value || 0);

            unitLabel.textContent = unit;

            if (price > 0 && qty > 0) {
                const total = price * qty;
                totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
                pricePreview.style.display = 'flex';
            } else {
                pricePreview.style.display = 'none';
            }
            lucide.createIcons();
        }

        // ─── Complete Modal ───────────────────────────────────────────────────────
        const completeModal        = document.getElementById('completeModal');
        const completeForm         = document.getElementById('completeForm');
        const completeCustomerName = document.getElementById('completeCustomerName');
        const BASE_COMPLETE_URL    = '<?= base_url("orders/complete/") ?>';

        function openCompleteModal(id, name) {
            completeForm.action = BASE_COMPLETE_URL + id;
            completeCustomerName.textContent = name;
            // Reset radio
            document.querySelectorAll('.reason-card input[type=radio]').forEach(r => r.checked = false);
            document.querySelectorAll('.reason-card').forEach(c => c.classList.remove('selected'));
            completeModal.style.display = 'flex';
        }
        function closeCompleteModal() {
            completeModal.style.display = 'none';
        }

        // Highlight selected reason card
        document.querySelectorAll('.reason-card input[type=radio]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.reason-card').forEach(c => c.classList.remove('selected'));
                radio.closest('.reason-card').classList.add('selected');
            });
        });

        function confirmDelete(url) {
            if (confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) {
                window.location.href = url;
            }
        }

        // Close modals on overlay click
        addModal.addEventListener('click', e => { if (e.target === addModal) closeAddModal(); });
        completeModal.addEventListener('click', e => { if (e.target === completeModal) closeCompleteModal(); });
    </script>
</body>
</html>
