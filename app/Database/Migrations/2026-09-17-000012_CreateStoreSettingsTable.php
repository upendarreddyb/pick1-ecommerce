<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStoreSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 80],
            'setting_value' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('store_settings', true);

        $settings = $this->db->table('store_settings');
        foreach ([
            'shipping_charge' => 49.00,
            'free_shipping_minimum' => 349.00,
            'gst_rate' => 4.40,
        ] as $key => $value) {
            if (! $settings->where('setting_key', $key)->get()->getFirstRow()) {
                $settings->insert(['setting_key' => $key, 'setting_value' => $value]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('store_settings', true);
    }
}
