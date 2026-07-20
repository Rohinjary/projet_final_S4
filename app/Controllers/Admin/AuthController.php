<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\BaremeFraisService;
use App\Services\OperateurService;
use App\Services\PrefixeValableService;
use App\Services\RapportService;
use App\Services\TypeOperationService;
use App\Services\UserService;

class AuthController extends BaseController
{
    public function operatorForm()
    {
        if (session()->get('operator_logged_in') === true) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('Admin/login', ['title' => 'Connexion opérateur']);
    }

    public function operatorLogin()
    {
        $password = (string) $this->request->getPost('password');

        if (trim($password) === '') {
            return redirect()->to(site_url('admin/login'))
                ->withInput()
                ->with('error', 'Veuillez saisir le mot de passe opérateur.');
        }

        $auth = (new UserService())->authenticate($password);
        if ($auth === null || $auth['operateur'] === null) {
            return redirect()->to(site_url('admin/login'))
                ->withInput()
                ->with('error', 'Mot de passe opérateur incorrect.');
        }

        $operateur = $auth['operateur'];
        session()->regenerate();
        session()->set([
            'operator_logged_in'   => true,
            'operator_user_id'     => (int) $auth['user']['id'],
            'operator_id'          => (int) $operateur['id'],
            'operator_name'        => (string) $operateur['nom'],
            'operator_is_principal'=> (int) $operateur['est_principal'] === 1,
            'operator_login_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/dashboard'));
    }

    public function adminDashboard()
    {
        if (session()->get('operator_logged_in') !== true) {
            return redirect()->to(site_url('admin/login'))
                ->with('error', 'Connectez-vous à l’espace opérateur.');
        }

        $prefixes = (new PrefixeValableService())->getAllWithOperateur();
        $types = (new TypeOperationService())->getAllTypeOperation();
        $latestBaremes = (new BaremeFraisService())->getLatestBaremesGroupedByType();
        $rapportService = new RapportService();
        $clientSummary = $rapportService->getClientSummary();
        $currentGains = $rapportService->getGains((int) date('Y'), (int) date('m'));
        $operateurs = (new OperateurService())->getAllWithConfiguration();

        return view('Admin/dashboard', [
            'title'         => 'Tableau de bord opérateur',
            'prefixes'      => $prefixes,
            'types'         => $types,
            'latestBaremes' => $latestBaremes,
            'clientSummary' => $clientSummary,
            'currentGains'  => $currentGains,
            'operateurs'    => $operateurs,
        ]);
    }

    public function logout()
    {
        session()->remove([
            'operator_logged_in', 'operator_user_id', 'operator_id', 'operator_name',
            'operator_is_principal', 'operator_login_at',
        ]);
        session()->regenerate();

        return redirect()->to(site_url('admin/login'))
            ->with('success', 'Vous êtes déconnecté de l’espace opérateur.');
    }
}
