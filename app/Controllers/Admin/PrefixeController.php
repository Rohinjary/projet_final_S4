<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrefixeValableModel;
use CodeIgniter\HTTP\RedirectResponse;

class PrefixeController extends BaseController
{
    private PrefixeValableModel $prefixeModel;

    public function __construct()
    {
        $this->prefixeModel = new PrefixeValableModel();
    }

    public function index()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        return view('Admin/prefixes', [
            'title'    => 'Configuration des préfixes',
            'prefixes' => $this->prefixeModel
                ->orderBy('date_ajout', 'DESC')
                ->orderBy('id', 'DESC')
                ->findAll(),
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));

        if (! preg_match('/^0\d{2}$/', $prefixe)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le préfixe doit contenir exactement 3 chiffres et commencer par 0.');
        }

        $dejaExistant = $this->prefixeModel
            ->where('prefixe', $prefixe)
            ->first();

        if ($dejaExistant !== null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le préfixe ' . $prefixe . ' existe déjà.');
        }

        $inserted = $this->prefixeModel->insert([
            'prefixe'    => $prefixe,
            'date_ajout' => date('Y-m-d H:i:s'),
        ]);

        if ($inserted === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Impossible d’ajouter le préfixe.');
        }

        return redirect()->to(site_url('admin/prefixes'))
            ->with('success', 'Le préfixe ' . $prefixe . ' a été ajouté avec succès.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $prefixe = $this->prefixeModel->find($id);

        if ($prefixe === null) {
            return redirect()->to(site_url('admin/prefixes'))
                ->with('error', 'Préfixe introuvable.');
        }

        $this->prefixeModel->delete($id);

        $valeur = is_array($prefixe) ? ($prefixe['prefixe'] ?? '') : ($prefixe->prefixe ?? '');

        return redirect()->to(site_url('admin/prefixes'))
            ->with('success', 'Le préfixe ' . $valeur . ' a été supprimé.');
    }

    private function requireOperator(): ?RedirectResponse
    {
        if (session()->get('operator_logged_in') === true) {
            return null;
        }

        return redirect()->to(site_url('admin/login'))
            ->with('login_mode', 'operator')
            ->with('error', 'Connectez-vous à l’espace opérateur.');
    }
}
