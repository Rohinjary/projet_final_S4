<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Services\BaremeFraisService;
use App\Services\ClientService;
use App\Services\OperationService;
use App\Services\PrefixeValableService;
use App\Services\TypeOperationService;
use CodeIgniter\HTTP\RedirectResponse;

class ClientController extends BaseController
{
    private ClientService $clientService;
    private OperationService $operationService;
    private PrefixeValableService $prefixeValableService;
    private TypeOperationService $typeOperationService;
    private BaremeFraisService $baremeFraisService;

    public function __construct()
    {
        $this->clientService = new ClientService();
        $this->operationService = new OperationService();
        $this->prefixeValableService = new PrefixeValableService();
        $this->typeOperationService = new TypeOperationService();
        $this->baremeFraisService = new BaremeFraisService();
    }

    public function login()
    {
        if (session()->get('client_numero')) {
            return redirect()->to(site_url('client/accueil'));
        }

        return view('client/login', [
            'prefixes' => $this->prefixeValableService->getAllWithOperateur(),
        ]);
    }

    public function authentifier()
    {
        return $this->traiterConnexion();
    }

    public function choixPrefixe()
    {
        return redirect()->to(site_url('client/login'));
    }

    public function validerNouveauNumero()
    {
        return $this->traiterConnexion();
    }

    public function info()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        return view('client/info');
    }

    public function enregistrerInfo()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        $nom = trim((string) $this->request->getPost('nom')) ?: null;
        $prenom = trim((string) $this->request->getPost('prenom')) ?: null;
        $this->clientService->updateInfos($numero, $nom, $prenom);
        return redirect()->to(site_url('client/accueil'));
    }

    public function passerInfo()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        return redirect()->to(site_url('client/accueil'));
    }

    public function accueil()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');

        return view('client/dashboard', [
            'client'     => $this->clientService->existeParNumero($numero),
            'solde'      => $this->operationService->calculerSolde($numero),
            'numero'     => $numero,
            'historique' => array_slice($this->operationService->getHistorique($numero), 0, 5),
        ]);
    }

    public function depot()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        return view('client/depot', ['solde' => $this->operationService->calculerSolde($numero)]);
    }

    public function traiterDepot()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        $montant = $this->normalizeAmount($this->request->getPost('amount'));
        if ($montant <= 0) {
            return $this->clientError('client/depot', 'Montant invalide.');
        }

        $typeId = $this->getTypeOperationIdParLibelle('depot');
        if ($typeId === null) {
            return $this->clientError('client/depot', 'Le type d’opération dépôt n’est pas configuré.');
        }
        $bareme = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, date('Y-m-d H:i:s'));
        if ($bareme === null) {
            return $this->clientError('client/depot', 'Aucun barème de frais ne correspond à ce montant.');
        }

        $frais = (float) $bareme['montant_frais'];
        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);
        session()->setFlashdata('succes', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');
        return redirect()->to(site_url('client/accueil'));
    }

    public function retrait()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        return view('client/retrait', ['solde' => $this->operationService->calculerSolde($numero)]);
    }

    public function traiterRetrait()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        $montant = $this->normalizeAmount($this->request->getPost('amount'));
        if ($montant <= 0) {
            return $this->clientError('client/retrait', 'Montant invalide.');
        }

        $typeId = $this->getTypeOperationIdParLibelle('retrait');
        if ($typeId === null) {
            return $this->clientError('client/retrait', 'Le type d’opération retrait n’est pas configuré.');
        }
        $bareme = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, date('Y-m-d H:i:s'));
        if ($bareme === null) {
            return $this->clientError('client/retrait', 'Aucun barème de frais ne correspond à ce montant.');
        }

        $frais = (float) $bareme['montant_frais'];
        if ($this->operationService->calculerSolde($numero) < ($montant + $frais)) {
            return $this->clientError('client/retrait', 'Solde insuffisant pour effectuer ce retrait.');
        }

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);
        session()->setFlashdata('succes', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');
        return redirect()->to(site_url('client/accueil'));
    }

    public function transfert()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        return view('client/transfert', ['solde' => $this->operationService->calculerSolde($numero)]);
    }

    public function traiterTransfert()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        $numeroDest = $this->normalizePhone((string) $this->request->getPost('recipient'));
        $montant = $this->normalizeAmount($this->request->getPost('amount'));

        if (! preg_match('/^0\d{9}$/', $numeroDest) || ! $this->prefixeEstValide(substr($numeroDest, 0, 3))) {
            return $this->clientError('client/transfert', 'Le numéro destinataire ou son préfixe est invalide.');
        }
        if ($numeroDest === $numero) {
            return $this->clientError('client/transfert', 'Impossible de transférer vers votre propre numéro.');
        }
        if (! $this->clientService->existeParNumero($numeroDest)) {
            return $this->clientError('client/transfert', 'Numéro destinataire introuvable.');
        }
        if ($montant <= 0) {
            return $this->clientError('client/transfert', 'Montant invalide.');
        }

        $typeId = $this->getTypeOperationIdParLibelle('transfert');
        if ($typeId === null) {
            return $this->clientError('client/transfert', 'Le type d’opération transfert n’est pas configuré.');
        }
        $bareme = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, date('Y-m-d H:i:s'));
        if ($bareme === null) {
            return $this->clientError('client/transfert', 'Aucun barème de frais ne correspond à ce montant.');
        }

        $frais = (float) $bareme['montant_frais'];
        if ($this->operationService->calculerSolde($numero) < ($montant + $frais)) {
            return $this->clientError('client/transfert', 'Solde insuffisant pour effectuer ce transfert.');
        }

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais, $numeroDest);
        session()->setFlashdata('succes', 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $numeroDest . ' effectué avec succès.');
        return redirect()->to(site_url('client/accueil'));
    }

    public function historique()
    {
        if ($redirect = $this->verifierSession()) {
            return $redirect;
        }
        $numero = (string) session()->get('client_numero');
        $typeIdGet = $this->request->getGet('type') ?: null;

        return view('client/historique', [
            'historique' => $this->operationService->getHistorique($numero, $typeIdGet ? (int) $typeIdGet : null),
            'types'      => $this->typeOperationService->getAllTypeOperation(),
            'numero'     => $numero,
            'typeActif'  => $typeIdGet,
        ]);
    }

    public function deconnexion()
    {
        session()->remove(['client_numero', 'client_logged_in']);
        session()->regenerate();
        return redirect()->to(site_url('client/login'))->with('success', 'Vous êtes déconnecté.');
    }

    private function traiterConnexion()
    {
        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $suite = preg_replace('/\D+/', '', (string) $this->request->getPost('suite')) ?? '';
        $numero = $this->normalizePhone((string) $this->request->getPost('numero'));

        if ($prefixe !== '' || $suite !== '') {
            if (! $this->prefixeEstValide($prefixe)) {
                return $this->loginError('Préfixe non valable.', $prefixe, $suite, $numero);
            }
            if (! preg_match('/^\d{7}$/', $suite)) {
                return $this->loginError('Le reste du numéro doit contenir 7 chiffres.', $prefixe, $suite, $numero);
            }
            $numero = $prefixe . $suite;
        }

        if (! preg_match('/^0\d{9}$/', $numero)) {
            return $this->loginError('Le numéro doit contenir 10 chiffres.', $prefixe, $suite, $numero);
        }
        if (! $this->prefixeEstValide(substr($numero, 0, 3))) {
            return $this->loginError('Le préfixe de ce numéro n’est pas configuré.', substr($numero, 0, 3), substr($numero, 3), $numero);
        }

        $nouveau = ! $this->clientService->existeParNumero($numero);
        if ($nouveau && $this->clientService->creerCompte($numero) === false) {
            return $this->loginError('Impossible de créer le compte client.', substr($numero, 0, 3), substr($numero, 3), $numero);
        }

        session()->regenerate();
        session()->set(['client_numero' => $numero, 'client_logged_in' => true]);
        return redirect()->to(site_url($nouveau ? 'client/info' : 'client/accueil'));
    }

    private function verifierSession(): ?RedirectResponse
    {
        if (session()->get('client_numero')) {
            return null;
        }
        return redirect()->to(site_url('client/login'))->with('erreur', 'Connectez-vous avec votre numéro.');
    }

    private function getTypeOperationIdParLibelle(string $libelle): ?int
    {
        foreach ($this->typeOperationService->getAllTypeOperation() as $type) {
            if (strtolower((string) $type['libelle']) === strtolower($libelle)) {
                return (int) $type['id'];
            }
        }
        return null;
    }

    private function prefixeEstValide(string $prefixe): bool
    {
        return $this->prefixeValableService->getPrefixeValableByPrefixe($prefixe) !== null;
    }

    private function normalizePhone(string $value): string
    {
        $numero = preg_replace('/\D+/', '', $value) ?? '';
        if (str_starts_with($numero, '261')) {
            $numero = '0' . substr($numero, 3);
        }
        return $numero;
    }

    private function normalizeAmount($value): float
    {
        return (float) str_replace([' ', ','], ['', '.'], trim((string) $value));
    }

    private function clientError(string $route, string $message): RedirectResponse
    {
        session()->setFlashdata('erreur', $message);
        return redirect()->to(site_url($route))->withInput();
    }

    private function loginError(string $message, string $prefixe, string $suite, string $numero): RedirectResponse
    {
        session()->setFlashdata('erreur', $message);
        session()->setFlashdata('prefixe_saisi', $prefixe);
        session()->setFlashdata('suite_saisie', $suite);
        session()->setFlashdata('numero_saisi', $numero);
        return redirect()->to(site_url('client/login'))->withInput();
    }
}
