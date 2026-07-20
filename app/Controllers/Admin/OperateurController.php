<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CommissionOperateurService;
use App\Services\OperateurService;
use CodeIgniter\HTTP\RedirectResponse;

class OperateurController extends BaseController
{
    private OperateurService $service;

    public function __construct()
    {
        $this->service = new OperateurService();
    }

    public function index()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        return view('Admin/operateurs', [
            'title'       => 'Gestion des opérateurs',
            'operateurs'  => $this->service->getAllWithConfiguration(),
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        if ($nom === '' || mb_strlen($nom) > 100) {
            return redirect()->back()->withInput()->with('error', 'Le nom de l’opérateur est obligatoire et limité à 100 caractères.');
        }
        if ($this->service->nomExiste($nom)) {
            return redirect()->back()->withInput()->with('error', 'Cet opérateur existe déjà.');
        }

        $id = $this->service->createOperateur($nom, 0);
        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Impossible d’ajouter cet opérateur.');
        }

        (new CommissionOperateurService())->saveCommission((int) $id, 0);

        return redirect()->to(site_url('admin/operateurs'))
            ->with('success', 'L’opérateur ' . $nom . ' a été ajouté. Vous pouvez maintenant lui affecter des préfixes et une commission.');
    }

    public function update(int $id): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $operateur = $this->service->getOperateurById($id);
        if ($operateur === null) {
            return redirect()->to(site_url('admin/operateurs'))->with('error', 'Opérateur introuvable.');
        }

        $nom = trim((string) $this->request->getPost('nom'));
        if ($nom === '' || mb_strlen($nom) > 100) {
            return redirect()->to(site_url('admin/operateurs'))->with('error', 'Le nom saisi est invalide.');
        }
        if ($this->service->nomExiste($nom, $id)) {
            return redirect()->to(site_url('admin/operateurs'))->with('error', 'Un autre opérateur porte déjà ce nom.');
        }

        $this->service->updateOperateur($id, $nom);
        return redirect()->to(site_url('admin/operateurs'))->with('success', 'Le nom de l’opérateur a été modifié.');
    }

    private function requireOperator(): ?RedirectResponse
    {
        if (session()->get('operator_logged_in') === true) {
            return null;
        }
        return redirect()->to(site_url('admin/login'))->with('error', 'Connectez-vous à l’espace opérateur.');
    }
}
