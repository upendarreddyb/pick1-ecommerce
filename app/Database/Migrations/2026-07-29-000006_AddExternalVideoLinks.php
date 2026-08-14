<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExternalVideoLinks extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('video_testimonials', [
            'video' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addColumn('video_testimonials', [
            'external_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'video'],
            'provider'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'external_url'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('video_testimonials', ['external_url', 'provider']);
        $this->forge->modifyColumn('video_testimonials', [
            'video' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
    }
}
