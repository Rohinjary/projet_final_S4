<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrefixeValableModel;
use App\Services\OperateurService;
use App\Services\PrefixeValableService;
use CodeIgniter\HTTP\RedirectResponse;

class PrefixeController extends BaseController
{
    private PrefixeValableService $prefixeService;
    private OperateurService $operateurService;
    private PrefixeValableModel $prefixeModel;

    public function __construct()
    {
        $this->prefixeService = new PrefixeValableService();
        $this->operateurService = new OperateurService();
        $this->prefixeModel = new PrefixeValableModel();
    }

    public function index()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        return view('Admin/prefixes', [
            'title'      => 'Configuration des préfixes',
            'prefixes'   => $this->prefixeService->getAllWithOperateur(),
            'operateurs' => $this->operateurService->getAllOperateurs(),
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $operateurId = (int) $this->request->getPost('operateur_id');

        if ($error = $this->validateData($prefixe, $operateurId)) {
            return redirect()->back()->withInput()->with('error', $error);
        }
        if ($this->prefixeService->prefixeExiste($prefixe)) {
            return redirect()->back()->withInput()->with('error', 'Le préfixe ' . $prefixe . ' existe déjà.');
        }

        if ($this->prefixeService->createPrefixeValable($prefixe, $operateurId) === false) {
            return redirect()->back()->withInput()->with('error', 'Impossible d’ajouter le préfixe.');
        }

        return redirect()->to(site_url('admin/prefixes'))
            ->with('success', 'Le préfixe ' . $prefixe . ' a été ajouté et affecté à son opérateur.');
    }

    public function update(int $id): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $ancienPrefixe = $this->prefixeService->getPrefixeValableById($id);
        if ($ancienPrefixe === null) {
            return redirect()->to(site_url('admin/prefixes'))->with('error', 'Préfixe introuvable.');
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $operateurId = (int) $this->request->getPost('operateur_id');
        if ($error = $this->validateData($prefixe, $operateurId)) {
            return redirect()->to(site_url('admin/prefixes'))->with('error', $error);
        }
        if ($this->prefixeService->prefixeExiste($prefixe, $id)) {
            return redirect()->to(site_url('admin/prefixes'))->with('error', 'Un autre préfixe possède déjà la valeur ' . $prefixe . '.');
        }

        if ($prefixe !== (string) $ancienPrefixe['prefixe']) {
            $nombreClients = db_connect()->table('client')
                ->like('numero', (string) $ancienPrefixe['prefixe'], 'after')
                ->countAllResults();
            if ($nombreClients > 0) {
                return redirect()->to(site_url('admin/prefixes'))
                    ->with('error', 'La valeur de ce préfixe ne peut pas changer car elle est utilisée par ' . $nombreClients . ' compte(s). Vous pouvez cependant modifier son opérateur.');
            }
        }

        $this->prefixeService->updatePrefixeValable($id, $prefixe, $operateurId);
        return redirect()->to(site_url('admin/prefixes'))->with('success', 'Le préfixe a été modifié.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $prefixe = $this->prefixeService->getPrefixeValableById($id);
        if ($prefixe === null) {
            return redirect()->to(site_url('admin/prefixes'))->with('error', 'Préfixe introuvable.');
        }

        $nombreClients = db_connect()->table('client')
            ->like('numero', (string) $prefixe['prefixe'], 'after')
            ->countAllResults();
        if ($nombreClients > 0) {
            return redirect()->to(site_url('admin/prefixes'))
                ->with('error', 'Ce préfixe est utilisé par ' . $nombreClients . ' compte(s) client. Modifiez son opérateur au lieu de le supprimer.');
        }

        $this->prefixeModel->delete($id);
        return redirect()->to(site_url('admin/prefixes'))
            ->with('success', 'Le préfixe ' . $prefixe['prefixe'] . ' a été supprimé.');
    }

    private function validateData(string $prefixe, int $operateurId): ?string
    {
        if (! preg_match('/^0\d{2}$/', $prefixe)) {
            return 'Le préfixe doit contenir exactement 3 chiffres et commencer par 0.';
        }
        if ($this->operateurService->getOperateurById($operateurId) === null) {
            return 'Sélectionnez un opérateur valide.';
        }
        return null;
    }

    private function requireOperator(): ?RedirectResponse
    {
        if (session()->get('operator_logged_in') === true) {
            return null;
        }
        return redirect()->to(site_url('admin/login'))->with('error', 'Connectez-vous à l’espace opérateur.');
    }
}
