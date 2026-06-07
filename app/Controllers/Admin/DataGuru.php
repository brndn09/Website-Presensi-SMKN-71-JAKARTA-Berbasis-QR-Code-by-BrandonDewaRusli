<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataGuru extends BaseController
{
    protected GuruModel $guruModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
    }

    protected function getValidationRules($id = null)
    {
        return [
            'nuptk' => [
                'rules'  => "required|max_length[50]|is_unique[tb_guru.nuptk,id_guru,{$id}]",
                'errors' => [
                    'required'   => 'NUPTK/NIP harus diisi.',
                    'is_unique'  => 'NUPTK/NIP ini telah terdaftar.',
                    'max_length' => 'NUPTK/NIP terlalu panjang.'
                ]
            ],
            'nama' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Nama harus diisi.',
                    'min_length' => 'Nama minimal 3 karakter.'
                ]
            ],
            'jk' => [
                'rules'  => 'required|in_list[1,2,Laki-laki,Perempuan]',
                'errors' => [
                    'required' => 'Jenis kelamin wajib dipilih.',
                    'in_list'  => 'Pilihan tidak valid.'
                ]
            ]
        ];
    }

    public function index()
    {
        if (!is_superadmin()) return redirect()->to('admin');

        return view('admin/data/data-guru', [
            'title' => 'Manajemen Data Guru',
            'ctx'   => 'guru',
        ]);
    }

    public function ambilDataGuru()
    {
        $result = $this->guruModel->getAllGuru();
        return view('admin/data/list-data-guru', [
            'data'  => $result,
            'empty' => empty($result)
        ]);
    }

    public function formTambahGuru()
    {
        return view('admin/data/create/create-data-guru', [
            'ctx'   => 'guru',
            'title' => 'Tambah Data Guru'
        ]);
    }

    public function saveGuru()
    {
        if (!$this->validate($this->getValidationRules())) {
            return redirect()->back()->withInput();
        }

        $save = $this->guruModel->createGuru(
            nuptk:        $this->request->getPost('nuptk'),
            nama:         $this->request->getPost('nama'),
            jenisKelamin: $this->request->getPost('jk')
        );

        if ($save) {
            session()->setFlashdata(['msg' => 'Data guru berhasil ditambahkan', 'error' => false]);
            return redirect()->to('/admin/guru');
        }

        return redirect()->back()->withInput()->with('msg', 'Gagal menambah data');
    }

    public function formEditGuru($id)
    {
        $guru = $this->guruModel->getGuruById($id);
        if (!$guru) throw new PageNotFoundException('Data guru tidak ditemukan');

        return view('admin/data/edit/edit-data-guru', [
            'data'  => $guru,
            'title' => 'Edit Data Guru',
            'ctx'   => 'guru'
        ]);
    }

    public function updateGuru()
    {
        $idGuru = $this->request->getPost('id');
        
        if (!$this->validate($this->getValidationRules($idGuru))) {
            return redirect()->back()->withInput();
        }

        $update = $this->guruModel->updateGuru(
            id:           $idGuru,
            nuptk:        $this->request->getPost('nuptk'),
            nama:         $this->request->getPost('nama'),
            jenisKelamin: $this->request->getPost('jk')
        );

        if ($update) {
            session()->setFlashdata(['msg' => 'Data guru berhasil diperbarui', 'error' => false]);
            return redirect()->to('/admin/guru');
        }

        return redirect()->back()->withInput();
    }

    public function delete($id)
    {
        if ($this->guruModel->delete($id)) {
            session()->setFlashdata(['msg' => 'Data berhasil dihapus', 'error' => false]);
        }
        return redirect()->to('/admin/guru');
    }
}