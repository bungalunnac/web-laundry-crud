<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ServiceController extends BaseController
{
    protected $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new \App\Models\ServiceModel();
    }

    public function index()
    {
        $data['services'] = $this->serviceModel->findAll();
        return view('services/index', $data);
    }

    public function create()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'price'       => 'required|numeric',
            'unit'        => 'required',
            'description' => 'required',
        ];

        if ($this->validate($rules)) {
            $this->serviceModel->save([
                'name'        => $this->request->getPost('name'),
                'price'       => $this->request->getPost('price'),
                'unit'        => $this->request->getPost('unit'),
                'description' => $this->request->getPost('description'),
            ]);

            session()->setFlashdata('success', 'Layanan laundry baru berhasil ditambahkan!');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan layanan. Pastikan data valid.');
        }

        return redirect()->to('/');
    }

    public function update($id)
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'price'       => 'required|numeric',
            'unit'        => 'required',
            'description' => 'required',
        ];

        if ($this->validate($rules)) {
            $this->serviceModel->update($id, [
                'name'        => $this->request->getPost('name'),
                'price'       => $this->request->getPost('price'),
                'unit'        => $this->request->getPost('unit'),
                'description' => $this->request->getPost('description'),
            ]);

            session()->setFlashdata('success', 'Layanan laundry berhasil diperbarui!');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui layanan. Pastikan data valid.');
        }

        return redirect()->to('/');
    }

    public function delete($id)
    {
        if ($this->serviceModel->find($id)) {
            $this->serviceModel->delete($id);
            session()->setFlashdata('success', 'Layanan laundry berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Layanan laundry tidak ditemukan.');
        }

        return redirect()->to('/');
    }
}
