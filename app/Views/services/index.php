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
    <title>FreshClean Laundry - Kelola Jasa Cuci</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
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
            <button class="btn btn-primary" onclick="openAddModal()">
                <i data-lucide="plus" size="20"></i>
                Tambah Layanan
            </button>
        </header>

        <!-- Main Content -->
        <main>
            <!-- Flash Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i data-lucide="sparkles" size="20"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <i data-lucide="alert-circle" size="20"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Services List -->
            <?php if (empty($services)): ?>
                <div class="empty-state">
                    <i data-lucide="sparkles" class="empty-icon"></i>
                    <h2 class="empty-title">Belum ada layanan</h2>
                    <p class="empty-desc">Tambahkan jenis layanan laundry pertama Anda untuk mulai mengelola.</p>
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i data-lucide="plus" size="20"></i>
                        Tambah Sekarang
                    </button>
                </div>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="card">
                            <div class="flex justify-between items-center mb-2">
                                <h3 style="font-size: 1.25rem; font-weight: 600;"><?= htmlspecialchars($service['name']) ?></h3>
                                <span class="badge"><?= htmlspecialchars($service['unit']) ?></span>
                            </div>
                            
                            <div style="color: var(--primary); font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">
                                <?= formatRupiah($service['price']) ?> 
                                <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 400;">/ <?= htmlspecialchars($service['unit']) ?></span>
                            </div>
                            
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; min-height: 3rem;">
                                <?= htmlspecialchars($service['description']) ?>
                            </p>
                            
                            <div class="flex gap-2" style="margin-top: auto;">
                                <button 
                                    class="btn btn-outline btn-icon flex" 
                                    style="flex: 1;"
                                    data-id="<?= $service['id'] ?>"
                                    data-name="<?= htmlspecialchars($service['name']) ?>"
                                    data-price="<?= $service['price'] ?>"
                                    data-unit="<?= htmlspecialchars($service['unit']) ?>"
                                    data-description="<?= htmlspecialchars($service['description']) ?>"
                                    onclick="openEditModal(this)"
                                >
                                    <i data-lucide="edit-2" size="16"></i> Edit
                                </button>
                                <button 
                                    class="btn btn-danger btn-icon flex" 
                                    style="flex: 1;"
                                    onclick="confirmDelete('<?= base_url('services/delete/' . $service['id']) ?>')"
                                >
                                    <i data-lucide="trash-2" size="16"></i> Hapus
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <!-- Form Overlay Modal -->
        <div class="modal-overlay" id="serviceModal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="modalTitle">Tambah Layanan Baru</h2>
                    <button class="close-btn" onclick="closeModal()">
                        <i data-lucide="x" size="24"></i>
                    </button>
                </div>
                
                <form id="serviceForm" method="post" action="">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label">Nama Layanan</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="serviceName"
                            class="form-control" 
                            placeholder="Contoh: Cuci Setrika Express"
                            required 
                        />
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label">Harga (Rp)</label>
                            <input 
                                type="number" 
                                name="price" 
                                id="servicePrice"
                                class="form-control" 
                                placeholder="Contoh: 15000"
                                required 
                                min="0"
                            />
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Satuan</label>
                            <select 
                                name="unit" 
                                id="serviceUnit"
                                class="form-control"
                            >
                                <option value="Kg">Kg</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Meter">Meter</option>
                                <option value="Pasang">Pasang</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Deskripsi Layanan</label>
                        <textarea 
                            name="description" 
                            id="serviceDescription"
                            class="form-control" 
                            placeholder="Jelaskan detail layanan ini..."
                            rows="3"
                            required 
                        ></textarea>
                    </div>
                    
                    <div class="flex justify-between items-center mt-4" style="padding-top: 1rem; border-top: 1px solid var(--border);">
                        <button type="button" class="btn btn-outline" onclick="closeModal()">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            Tambah Layanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        const modal = document.getElementById('serviceModal');
        const modalTitle = document.getElementById('modalTitle');
        const serviceForm = document.getElementById('serviceForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // Form fields
        const serviceName = document.getElementById('serviceName');
        const servicePrice = document.getElementById('servicePrice');
        const serviceUnit = document.getElementById('serviceUnit');
        const serviceDescription = document.getElementById('serviceDescription');

        function openAddModal() {
            modalTitle.textContent = 'Tambah Layanan Baru';
            serviceForm.action = '<?= base_url('services/create') ?>';
            serviceForm.reset();
            submitBtn.textContent = 'Tambah Layanan';
            modal.style.display = 'flex';
        }

        function openEditModal(btn) {
            modalTitle.textContent = 'Edit Layanan';
            const id = btn.getAttribute('data-id');
            serviceForm.action = '<?= base_url('services/update') ?>/' + id;
            
            serviceName.value = btn.getAttribute('data-name');
            servicePrice.value = btn.getAttribute('data-price');
            serviceUnit.value = btn.getAttribute('data-unit');
            serviceDescription.value = btn.getAttribute('data-description');
            
            submitBtn.textContent = 'Simpan Perubahan';
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function confirmDelete(url) {
            if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
