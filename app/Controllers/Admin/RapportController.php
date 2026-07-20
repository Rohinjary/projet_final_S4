<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\RapportService;

class RapportController extends BaseController
{
    private function requireOperator()
    {
        if (session()->get('operator_logged_in') !== true) {
            return redirect()->to(site_url('admin/login'))
                ->with('login_mode', 'operator')
                ->with('error', 'Connectez-vous à l’espace opérateur.');
        }

        return null;
    }

    public function gains()
    {
        if (($redirect = $this->requireOperator()) !== null) {
            return $redirect;
        }

        $currentYear = (int) date('Y');
        $year = (int) ($this->request->getGet('annee') ?: $currentYear);
        $month = (int) ($this->request->getGet('mois') ?: date('n'));

        if ($year < 2000 || $year > 2100) {
            $year = $currentYear;
        }
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }

        $service = new RapportService();

        return view('Admin/gains', [
            'title' => 'Situation des gains',
            'annee' => $year,
            'mois' => $month,
            'gains' => $service->getGains($year, $month),
            'gainsAnnuels' => $service->getAnnualGains($year),
        ]);
    }

    public function comptes()
    {
        if (($redirect = $this->requireOperator()) !== null) {
            return $redirect;
        }

        $search = trim((string) $this->request->getGet('recherche'));
        $status = (string) ($this->request->getGet('statut') ?: 'tous');
        if (! in_array($status, ['tous', 'actif', 'inactif'], true)) {
            $status = 'tous';
        }

        $service = new RapportService();

        return view('Admin/comptes', [
            'title' => 'Situation des comptes clients',
            'comptes' => $service->getClientAccounts($search, $status),
            'resume' => $service->getClientSummary(),
            'recherche' => $search,
            'statut' => $status,
        ]);
    }
}
