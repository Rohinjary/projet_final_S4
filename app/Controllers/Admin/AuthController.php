<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\UserService;

class AuthController extends BaseController
{
    /**
     * Affiche la page de connexion sans ouvrir la base de données.
     * Cela permet à la vue de s'afficher même avant les migrations/seeds.
     */
    public function index()
    {
        if (session()->get('operator_logged_in') === true) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        if (session()->get('client_logged_in') === true) {
            return redirect()->to(site_url('client/dashboard'));
        }

        return view('Admin/login', [
            'title' => 'Connexion',
        ]);
    }

    public function operatorLogin()
    {
        $password = trim((string) $this->request->getPost('password'));

        if ($password === '') {
            return redirect()->to(site_url('/'))
                ->withInput()
                ->with('login_mode', 'operator')
                ->with('error', 'Veuillez saisir le mot de passe.');
        }

        // Connexion à la base uniquement au moment de la vérification.
        $userService = new UserService();

        if (! $userService->verifyUser($password)) {
            return redirect()->to(site_url('/'))
                ->withInput()
                ->with('login_mode', 'operator')
                ->with('error', 'Mot de passe opérateur incorrect.');
        }

        session()->regenerate();
        session()->set([
            'operator_logged_in' => true,
            'operator_login_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/dashboard'));
    }

    public function clientLogin()
    {
        $numero = preg_replace('/\D+/', '', (string) $this->request->getPost('numero')) ?? '';

        if (str_starts_with($numero, '261')) {
            $numero = '0' . substr($numero, 3);
        }

        if (! preg_match('/^(033|037)\d{7}$/', $numero)) {
            return redirect()->to(site_url('/'))
                ->withInput()
                ->with('login_mode', 'client')
                ->with('error', 'Le numéro doit contenir 10 chiffres et commencer par 033 ou 037.');
        }

        session()->regenerate();
        session()->set([
            'client_logged_in' => true,
            'client_numero'    => $numero,
        ]);

        return redirect()->to(site_url('client/dashboard'));
    }

    public function adminDashboard()
    {
        if (session()->get('operator_logged_in') !== true) {
            return redirect()->to(site_url('/'))
                ->with('login_mode', 'operator')
                ->with('error', 'Connectez-vous à l’espace opérateur.');
        }

        return view('Admin/dashboard', [
            'title' => 'Tableau de bord opérateur',
        ]);
    }

    public function clientDashboard()
    {
        if (session()->get('client_logged_in') !== true) {
            return redirect()->to(site_url('/'))
                ->with('login_mode', 'client')
                ->with('error', 'Connectez-vous avec votre numéro.');
        }

        return view('Client/dashboard', [
            'title'  => 'Mon compte',
            'numero' => session()->get('client_numero'),
        ]);
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('/'))
            ->with('success', 'Vous êtes déconnecté.');
    }
}
