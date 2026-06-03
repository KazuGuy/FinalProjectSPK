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

    public function edit(int $id)
    {
        $criteria = $this->model->find($id);
        if (!$criteria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('admin/criteria/form', ['criteria' => $criteria]);
    }

    public function update(int $id)
    {
        if (!$this->validate([
            'default_weight' => 'required|numeric|greater_than[0]',
            'type'           => 'required|in_list[cost,benefit]',
        ])) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost(['default_weight', 'type']);
        $data['default_weight'] = (float) str_replace(',', '.', $data['default_weight']);

        $this->model->update($id, $data);
        return redirect()->to('/admin/criteria')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }
}