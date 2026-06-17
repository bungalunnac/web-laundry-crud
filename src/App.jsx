import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, WashingMachine, X, Sparkles, AlertCircle } from 'lucide-react';
import './App.css';

function App() {
  const [services, setServices] = useState(() => {
    const savedServices = localStorage.getItem('laundry_services');
    return savedServices ? JSON.parse(savedServices) : [
      { id: 1, name: 'Cuci Setrika Reguler', price: 6000, unit: 'Kg', description: 'Pakaian dicuci bersih, wangi, dan disetrika rapi (2-3 hari).' },
      { id: 2, name: 'Cuci Kering Express', price: 10000, unit: 'Kg', description: 'Cucian selesai dalam 1 hari. Tanpa setrika.' },
      { id: 3, name: 'Cuci Karpet', price: 15000, unit: 'Meter', description: 'Pencucian karpet dengan alat khusus agar bebas debu dan tungau.' },
    ];
  });

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingId, setEditingId] = useState(null);
  
  const [formData, setFormData] = useState({
    name: '',
    price: '',
    unit: 'Kg',
    description: ''
  });

  useEffect(() => {
    localStorage.setItem('laundry_services', JSON.stringify(services));
  }, [services]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const openModal = (service = null) => {
    if (service) {
      setFormData(service);
      setEditingId(service.id);
    } else {
      setFormData({ name: '', price: '', unit: 'Kg', description: '' });
      setEditingId(null);
    }
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setFormData({ name: '', price: '', unit: 'Kg', description: '' });
    setEditingId(null);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (editingId) {
      setServices(services.map(s => s.id === editingId ? { ...formData, id: editingId } : s));
    } else {
      setServices([...services, { ...formData, id: Date.now() }]);
    }
    closeModal();
  };

  const handleDelete = (id) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
      setServices(services.filter(s => s.id !== id));
    }
  };

  const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(number);
  };

  return (
    <div className="app-container">
      <header className="header">
        <div className="header-title">
          <WashingMachine size={36} color="var(--primary)" />
          FreshClean Laundry
        </div>
        <button className="btn btn-primary" onClick={() => openModal()}>
          <Plus size={20} />
          Tambah Layanan
        </button>
      </header>

      <main>
        {services.length === 0 ? (
          <div className="empty-state">
            <Sparkles size={48} className="empty-icon" />
            <h2 className="empty-title">Belum ada layanan</h2>
            <p className="empty-desc">Tambahkan jenis layanan laundry pertama Anda untuk mulai mengelola.</p>
            <button className="btn btn-primary" onClick={() => openModal()}>
              <Plus size={20} />
              Tambah Sekarang
            </button>
          </div>
        ) : (
          <div className="services-grid">
            {services.map(service => (
              <div key={service.id} className="card">
                <div className="flex justify-between items-center mb-2">
                  <h3 style={{ fontSize: '1.25rem', fontWeight: 600 }}>{service.name}</h3>
                  <span className="badge">{service.unit}</span>
                </div>
                
                <div style={{ color: 'var(--primary)', fontSize: '1.5rem', fontWeight: 700, marginBottom: '1rem' }}>
                  {formatRupiah(service.price)} <span style={{ fontSize: '0.875rem', color: 'var(--text-muted)', fontWeight: 400 }}>/ {service.unit}</span>
                </div>
                
                <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '1.5rem', minHeight: '3rem' }}>
                  {service.description}
                </p>
                
                <div className="flex gap-2" style={{ marginTop: 'auto' }}>
                  <button 
                    className="btn btn-outline btn-icon flex" 
                    style={{ flex: 1 }}
                    onClick={() => openModal(service)}
                  >
                    <Edit2 size={16} /> Edit
                  </button>
                  <button 
                    className="btn btn-danger btn-icon flex" 
                    style={{ flex: 1 }}
                    onClick={() => handleDelete(service.id)}
                  >
                    <Trash2 size={16} /> Hapus
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </main>

      {isModalOpen && (
        <div className="modal-overlay" onClick={closeModal}>
          <div className="modal-content" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <h2 className="modal-title">{editingId ? 'Edit Layanan' : 'Tambah Layanan Baru'}</h2>
              <button className="close-btn" onClick={closeModal}>
                <X size={24} />
              </button>
            </div>
            
            <form onSubmit={handleSubmit}>
              <div className="form-group">
                <label className="form-label">Nama Layanan</label>
                <input 
                  type="text" 
                  name="name" 
                  className="form-control" 
                  value={formData.name} 
                  onChange={handleInputChange} 
                  placeholder="Contoh: Cuci Setrika Express"
                  required 
                />
              </div>
              
              <div className="flex gap-4">
                <div className="form-group" style={{ flex: 2 }}>
                  <label className="form-label">Harga (Rp)</label>
                  <input 
                    type="number" 
                    name="price" 
                    className="form-control" 
                    value={formData.price} 
                    onChange={handleInputChange} 
                    placeholder="Contoh: 15000"
                    required 
                    min="0"
                  />
                </div>
                
                <div className="form-group" style={{ flex: 1 }}>
                  <label className="form-label">Satuan</label>
                  <select 
                    name="unit" 
                    className="form-control" 
                    value={formData.unit} 
                    onChange={handleInputChange}
                  >
                    <option value="Kg">Kg</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Meter">Meter</option>
                    <option value="Pasang">Pasang</option>
                  </select>
                </div>
              </div>
              
              <div className="form-group">
                <label className="form-label">Deskripsi Layanan</label>
                <textarea 
                  name="description" 
                  className="form-control" 
                  value={formData.description} 
                  onChange={handleInputChange} 
                  placeholder="Jelaskan detail layanan ini..."
                  rows="3"
                  required 
                ></textarea>
              </div>
              
              <div className="flex justify-between items-center mt-4" style={{ paddingTop: '1rem', borderTop: '1px solid var(--border)' }}>
                <button type="button" className="btn btn-outline" onClick={closeModal}>
                  Batal
                </button>
                <button type="submit" className="btn btn-primary">
                  {editingId ? 'Simpan Perubahan' : 'Tambah Layanan'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

export default App;
