<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CommissionOperateurService;
use App\Services\OperateurService;
use CodeIgniter\HTTP\RedirectResponse;

class CommissionController extends BaseController
{
    private OperateurService $operateurService;
    private CommissionOperateurService $commissionService;

    public function __construct()
    {
        $this->operateurService = new OperateurService();
        $this->commissionService = new CommissionOperateurService();
    }

    public function index()
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        return view('Admin/commissions', [
            'title'      => 'Commissions par opérateur',
            'operateurs' => $this->operateurService->getAllWithConfiguration(),
        ]);
    }

    public function save(int $operateurId): RedirectResponse
    {
        if ($redirect = $this->requireOperator()) {
            return $redirect;
        }

        $operateur = $this->operateurService->getOperateurById($operateurId);
        if ($operateur === null) {
            return redirect()->to(site_url('admin/commissions'))->with('error', 'Opérateur introuvable.');
        }

        $raw = str_replace(',', '.', trim((string) $this->request->getPost('pourcentage')));
        if ($raw === '' || ! is_numeric($raw)) {
            return redirect()->to(site_url('admin/commissions'))->with('error', 'Saisissez un pourcentage numérique.');
        }

        $pourcentage = (float) $raw;
        if ($pourcentage < 0 || $pourcentage > 100) {
            return redirect()->to(site_url('admin/commissions'))->with('error', 'La commission doit être comprise entre 0 % et 100 %.');
        }

        if ((int) $operateur['est_principal'] === 1) {
            $pourcentage = 100.0;
        }

        if (! $this->commissionService->saveCommission($operateurId, $pourcentage)) {
            return redirect()->to(site_url('admin/commissions'))->with('error', 'Impossible d’enregistrer la commission.');
        }

        return redirect()->to(site_url('admin/commissions'))->with('success', 'La commission de ' . $operateur['nom'] . ' a été mise à jour.');
    }

    private function requireOperator(): ?RedirectResponse
    {
        if (session()->get('operator_logged_in') === true) {
            return null;
        }
        return redirect()->to(site_url('admin/login'))->with('error', 'Connectez-vous à l’espace opérateur.');
    }
}
