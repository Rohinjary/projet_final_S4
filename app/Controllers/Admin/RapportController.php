<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\RapportService;

class RapportController extends BaseController
{
    public function gains()
    {
        if (($redirect = $this->requireOperator()) !== null) {
            return $redirect;
        }
        [$year, $month] = $this->period();
        $service = new RapportService();

        return view('Admin/gains', [
            'title'        => 'Situation des gains',
            'annee'        => $year,
            'mois'         => $month,
            'gains'        => $service->getGains($year, $month),
            'gainsAnnuels' => $service->getAnnualGains($year),
        ]);
    }

    public function reversements()
    {
        if (($redirect = $this->requireOperator()) !== null) {
            return $redirect;
        }
        [$year, $month] = $this->period();
        $service = new RapportService();
        $gains = $service->getGains($year, $month);

        return view('Admin/reversements', [
            'title'         => 'Montants à envoyer',
            'annee'         => $year,
            'mois'          => $month,
            'reversements'  => $service->getReversements($year, $month),
            'totalAEnvoyer' => (float) $gains['a_reverser'],
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
            'title'      => 'Situation des comptes clients',
            'comptes'    => $service->getClientAccounts($search, $status),
            'resume'     => $service->getClientSummary(),
            'recherche'  => $search,
            'statut'     => $status,
        ]);
    }

    private function period(): array
    {
        $currentYear = (int) date('Y');
        $year = (int) ($this->request->getGet('annee') ?: $currentYear);
        $month = (int) ($this->request->getGet('mois') ?: date('n'));
        if ($year < 2000 || $year > 2100) {
            $year = $currentYear;
        }
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        return [$year, $month];
    }

    private function requireOperator()
    {
        if (session()->get('operator_logged_in') !== true) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Connectez-vous à l’espace opérateur.');
        }
        return null;
    }
}
