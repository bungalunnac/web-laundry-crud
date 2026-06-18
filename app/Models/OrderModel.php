<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_name',
        'service_id',
        'quantity',
        'total_price',
        'notes',
        'status',
        'completion_reason',
        'completed_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil semua pesanan aktif (belum selesai) beserta info layanannya.
     */
    public function getActiveOrders()
    {
        return $this->select('orders.*, services.name as service_name, services.unit as service_unit')
            ->join('services', 'services.id = orders.service_id')
            ->whereIn('orders.status', ['belum_dikerjakan', 'sedang_dikerjakan'])
            ->orderBy('orders.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Ambil semua riwayat pesanan yang sudah selesai beserta info layanannya.
     */
    public function getOrderHistory()
    {
        return $this->select('orders.*, services.name as service_name, services.unit as service_unit')
            ->join('services', 'services.id = orders.service_id')
            ->where('orders.status', 'selesai')
            ->orderBy('orders.completed_at', 'DESC')
            ->findAll();
    }

    /**
     * Ambil satu pesanan beserta info layanannya.
     */
    public function getOrderWithService($id)
    {
        return $this->select('orders.*, services.name as service_name, services.unit as service_unit, services.price as service_price')
            ->join('services', 'services.id = orders.service_id')
            ->where('orders.id', $id)
            ->first();
    }
}
