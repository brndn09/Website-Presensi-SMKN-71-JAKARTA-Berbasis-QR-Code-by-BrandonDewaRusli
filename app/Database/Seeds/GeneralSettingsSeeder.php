<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GeneralSettingsSeeder extends Seeder
{
    public function run()
    {
        // Default general settings
        $data = [
            'school_name' => 'SMKN 71 JAKARTA',
            'school_year' => '2025/2026',
            'copyright'   => '© 2026 All rights reserved.',
            'logo'        => null,
        ];

        // Check if settings already exist
        $existingSettings = $this->db->table('general_settings')
            ->get()
            ->getRow();

        if (!$existingSettings) {
            // Insert default settings
            $this->db->table('general_settings')->insert($data);
            
            echo "\nGeneral settings created successfully!\n";
            echo "School Name: {$data['school_name']}\n";
            echo "School Year: {$data['school_year']}\n";
        } else {
            echo "General settings already exist. Skipping...\n";
        }
    }
}
