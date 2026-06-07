<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        // Run seeders in order
        $this->call('KehadiranSeeder');
        $this->call('JurusanSeeder');
        $this->call('KelasSeeder');
        $this->call('SuperadminSeeder');
        $this->call('GeneralSettingsSeeder');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
        // Optional: Uncomment if you want to seed sample data
        // $this->call('GuruSeeder');
        // $this->call('SiswaSeeder');
    }
}