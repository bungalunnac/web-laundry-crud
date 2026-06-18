<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ServiceModel;

class OrderController extends BaseController
{
    protected $orderModel;
    protected $serviceModel;

    public function __construct()
    {
        $this->orderModel   = new OrderModel();
        $this->serviceModel = new ServiceModel();
    }

    /**
     * Halaman daftar pesanan aktif (belum selesai).
     */
    public function index()
    {
        $data['orders']   = $this->orderModel->getActiveOrders();
        $data['services'] = $this->serviceModel->findAll();
        return view('orders/index', $data);
    }

    /**
     * Simpan pesanan baru.
     */
    public function create()
    {
        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[255]',
            'service_id'    => 'required|numeric',
            'quantity'      => 'required|numeric|greater_than[0]',
            'notes'         => 'permit_empty',
        ];

        if ($this->validate($rules)) {
            $serviceId = $this->request->getPost('service_id');
            $quantity  = $this->request->getPost('quantity');
            $service   = $this->serviceModel->find($serviceId);

            if (!$service) {
                session()->setFlashdata('error', 'Layanan tidak ditemukan.');
                return redirect()->to('/orders');
            }

            $totalPrice = $service['price'] * $quantity;

            $this->orderModel->save([
                'customer_name' => $this->request->getPost('customer_name'),
                'service_id'    => $serviceId,
                'quantity'      => $quantity,
                'total_price'   => $totalPrice,
                'notes'         => $this->request->getPost('notes'),
                'status'        => 'belum_dikerjakan',
            ]);

            session()->setFlashdata('success', 'Pesanan baru berhasil dibuat!');
        } else {
            session()->setFlashdata('error', 'Gagal membuat pesanan. Pastikan data valid.');
        }

        return redirect()->to('/orders');
    }

    /**
     * Update status pengerjaan (belum_dikerjakan → sedang_dikerjakan).
     */
    public function updateStatus($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order || $order['status'] === 'selesai') {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan atau sudah selesai.');
            return redirect()->to('/orders');
        }

        $newStatus = $this->request->getPost('status');
        $allowed   = ['belum_dikerjakan', 'sedang_dikerjakan'];

        if (!in_array($newStatus, $allowed)) {
            session()->setFlashdata('error', 'Status tidak valid.');
            return redirect()->to('/orders');
        }

        $this->orderModel->update($id, ['status' => $newStatus]);

        $statusLabel = $newStatus === 'sedang_dikerjakan' ? 'Sedang Dikerjakan' : 'Belum Dikerjakan';
        session()->setFlashdata('success', "Status pesanan diperbarui menjadi: {$statusLabel}.");

        return redirect()->to('/orders');
    }

    /**
     * Selesaikan pesanan dengan alasan.
     */
    public function complete($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->to('/orders');
        }

        if ($order['status'] === 'selesai') {
            session()->setFlashdata('error', 'Pesanan ini sudah diselesaikan sebelumnya.');
            return redirect()->to('/orders');
        }

        $reason  = $this->request->getPost('completion_reason');
        $allowed = ['dikerjakan_dan_diambil', 'diambil_tanpa_dikerjakan'];

        if (!in_array($reason, $allowed)) {
            session()->setFlashdata('error', 'Alasan penyelesaian tidak valid.');
            return redirect()->to('/orders');
        }

        $this->orderModel->update($id, [
            'status'            => 'selesai',
            'completion_reason' => $reason,
            'completed_at'      => date('Y-m-d H:i:s'),
        ]);

        $label = $reason === 'dikerjakan_dan_diambil'
            ? 'Sudah dikerjakan dan diambil'
            : 'Belum dikerjakan tapi sudah diambil';

        session()->setFlashdata('success', "Pesanan diselesaikan: {$label}.");

        return redirect()->to('/orders');
    }

    /**
     * Halaman riwayat pesanan yang sudah selesai.
     */
    public function history()
    {
        $data['history'] = $this->orderModel->getOrderHistory();
        return view('orders/history', $data);
    }

    /**
     * Hapus pesanan (hanya yang sudah selesai atau belum dikerjakan).
     */
    public function delete($id)
    {
        $order = $this->orderModel->find($id);

        if ($order) {
            $this->orderModel->delete($id);
            session()->setFlashdata('success', 'Pesanan berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
        }

        return redirect()->back();
    }
}
