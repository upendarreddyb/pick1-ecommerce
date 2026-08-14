<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVideoTestimonialsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'review'        => ['type' => 'TEXT', 'null' => true],
            'rating'        => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 5],
            'video'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'poster'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order'    => ['type' => 'INT', 'default' => 0],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'sort_order']);
        $this->forge->createTable('video_testimonials', true);
    }

    public function down()
    {
        $this->forge->dropTable('video_testimonials', true);
    }
}
