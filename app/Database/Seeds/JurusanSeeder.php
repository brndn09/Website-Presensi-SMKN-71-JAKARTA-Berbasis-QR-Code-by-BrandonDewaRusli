<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('tb_jurusan')->truncate();
        
        $data = [
            ['jurusan' => 'RPL'],
            ['jurusan' => 'ANM'],
            ['jurusan' => 'DKV'],
        ];

        // Using Query Builder for batch insert
        $this->db->table('tb_jurusan')->insertBatch($data);
    }
}