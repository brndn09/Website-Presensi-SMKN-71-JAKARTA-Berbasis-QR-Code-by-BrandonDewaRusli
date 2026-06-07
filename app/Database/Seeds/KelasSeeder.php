<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Kelas X (3 jurusan: )
            ['tingkat' => 'X', 'id_jurusan' => 1, 'index_kelas' => '1'],
            ['tingkat' => 'X', 'id_jurusan' => 1, 'index_kelas' => '2'],
            ['tingkat' => 'X', 'id_jurusan' => 2, 'index_kelas' => '1'],
            ['tingkat' => 'X', 'id_jurusan' => 2, 'index_kelas' => '2'],
            ['tingkat' => 'X', 'id_jurusan' => 3, 'index_kelas' => '1'],
            ['tingkat' => 'X', 'id_jurusan' => 3, 'index_kelas' => '2'],

            // Kelas XI (3 jurusan: )
            ['tingkat' => 'XI', 'id_jurusan' => 1, 'index_kelas' => '1'],
            ['tingkat' => 'XI', 'id_jurusan' => 1, 'index_kelas' => '2'],
            ['tingkat' => 'XI', 'id_jurusan' => 2, 'index_kelas' => '1'],
            ['tingkat' => 'XI', 'id_jurusan' => 2, 'index_kelas' => '2'],
            ['tingkat' => 'XI', 'id_jurusan' => 3, 'index_kelas' => '1'],
            ['tingkat' => 'XI', 'id_jurusan' => 3, 'index_kelas' => '2'],
            
        ];

        // Using Query Builder for batch insert
        $this->db->table('tb_kelas')->insertBatch($data);
    }
}