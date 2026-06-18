<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'service_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'total_price' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Status pengerjaan: belum_dikerjakan | sedang_dikerjakan | selesai
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_dikerjakan', 'sedang_dikerjakan', 'selesai'],
                'default'    => 'belum_dikerjakan',
            ],
            // Alasan penyelesaian: null saat belum selesai
            // dikerjakan_dan_diambil | diambil_tanpa_dikerjakan
            'completion_reason' => [
                'type'       => 'ENUM',
                'constraint' => ['dikerjakan_dan_diambil', 'diambil_tanpa_dikerjakan'],
                'null'       => true,
                'default'    => null,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
