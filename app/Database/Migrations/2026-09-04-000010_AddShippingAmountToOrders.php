<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShippingAmountToOrders extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('shipping_amount', 'orders')) {
            $this->forge->addColumn('orders', [
                'shipping_amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0,
                    'after' => 'total_amount',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('shipping_amount', 'orders')) {
            $this->forge->dropColumn('orders', 'shipping_amount');
        }
    }
}
