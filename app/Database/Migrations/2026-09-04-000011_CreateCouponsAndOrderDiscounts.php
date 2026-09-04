<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCouponsAndOrderDiscounts extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('coupons')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'code' => ['type' => 'VARCHAR', 'constraint' => 50],
                'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('code');
            $this->forge->createTable('coupons');
        }

        if (! $this->db->fieldExists('discount_amount', 'orders')) {
            $this->forge->addColumn('orders', [
                'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0, 'null' => false, 'after' => 'shipping_amount'],
                'coupon_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'discount_amount'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('discount_amount', 'orders')) $this->forge->dropColumn('orders', ['discount_amount', 'coupon_code']);
        if ($this->db->tableExists('coupons')) $this->forge->dropTable('coupons');
    }
}
