<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOutForDeliveryOrderStatus extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipped','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        $this->db->table('orders')->where('status', 'out_for_delivery')->update(['status' => 'shipped']);
        $this->db->query("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
    }
}
