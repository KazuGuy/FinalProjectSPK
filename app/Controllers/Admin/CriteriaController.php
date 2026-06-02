<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CriteriaModel;

class CriteriaController extends BaseController
{
    protected CriteriaModel $model;

    public function __construct()
    {
        $this->model = new CriteriaModel();
    }

    public function index()
    {
        return view('admin/criteria/index', [
            'criterias' => $this->model->orderBy('id')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/criteria/form');
    }

    public function store()
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->insert($this->request->getPost());
        return redirect()->to('/admin/criteria')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $criteria = $this->model->find($id);

        if (!$criteria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kriteria tidak ditemukan.');
        }

        return view('admin/criteria/form', ['criteria' => $criteria]);
    }

    public function update(int $id)
    {
        $rules = $this->model->validationRules;
        $rules['code'] = "required|max_length[5]|is_unique[criterias.code,id,{$id}]";

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, $this->request->getPost());
        return redirect()->to('/admin/criteria')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/admin/criteria')->with('success', 'Kriteria berhasil dihapus.');
    }
}