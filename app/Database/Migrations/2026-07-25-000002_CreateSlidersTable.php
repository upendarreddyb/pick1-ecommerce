<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSlidersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'eyebrow'     => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 180],
            'description' => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'button_text' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'button_url'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'sort_order']);
        $this->forge->createTable('sliders', true);
    }

    public function down()
    {
        $this->forge->dropTable('sliders', true);
    }
}
