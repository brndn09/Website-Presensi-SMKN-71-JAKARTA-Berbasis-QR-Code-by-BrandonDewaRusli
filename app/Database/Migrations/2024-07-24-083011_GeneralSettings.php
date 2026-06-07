<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GeneralSettings extends Migration
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
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
            ],
            'school_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
                'default'    => 'SMKN 71 Jakarta',
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
                'default'    => '2026/2026',
            ],
            'copyright' => [
                'type'       => 'VARCHAR',
                'constraint' => 225,
                'null'       => true,
                'default'    => '© 2026 All rights reserved.',
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        $this->forge->createTable('general_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('general_settings', true);
    }
}