<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Services\ClientService;
use App\Services\OperationService;
use App\Services\PrefixeValableService;
use App\Services\TypeOperationService;
use App\Services\BaremeFraisService;

use DateTime;

class ClientController extends BaseController
{
    protected ClientService $clientService;
    protected OperationService $operationService;
    protected PrefixeValableService $prefixeValableService;
    protected TypeOperationService $typeOperationService;
    protected BaremeFraisService $baremeFraisService;

    public function __construct()
    {
        $this->clientService         = new ClientService();
        $this->operationService      = new OperationService();
        $this->prefixeValableService = new PrefixeValableService();
        $this->typeOperationService  = new TypeOperationService();
        $this->baremeFraisService    = new BaremeFraisService();
    }

    private function verifierSession()
    {
        $numero = session()->get('client_numero');
        if (!$numero) {
            return redirect()->to('/client/login');
        }
        return $numero;
    }

    private function getTypeOperationIdParLibelle(string $libelle): ?int
    {
        foreach ($this->typeOperationService->getAllTypeOperation() as $t) {
            if ($t['libelle'] === $libelle) {
                return (int) $t['id'];
            }
        }
        return null;
    }

    private function prefixeEstValide(string $prefixe): bool
    {
        foreach ($this->prefixeValableService->getAllPrefixeValable() as $p) {
            if ($p['prefixe'] === $prefixe) {
                return true;
            }
        }
        return false;
    }

    private function traiterConnexion()
    {
        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $suite   = trim((string) $this->request->getPost('suite'));
        $numero  = trim((string) $this->request->getPost('numero'));

        if ($prefixe !== '' || $suite !== '') {
            if (!$this->prefixeEstValide($prefixe)) {
                session()->setFlashdata('erreur', 'Prefixe non valable.');
                session()->setFlashdata('prefixe_saisi', $prefixe);
                session()->setFlashdata('suite_saisie', $suite);
                return redirect()->to('/client/login');
            }

            if (!ctype_digit($suite) || strlen($suite) !== 7) {
                session()->setFlashdata('erreur', 'Le reste du numero doit contenir 7 chiffres.');
                session()->setFlashdata('prefixe_saisi', $prefixe);
                session()->setFlashdata('suite_saisie', $suite);
                return redirect()->to('/client/login');
            }

            $numero = $prefixe . $suite;
        }

        if (!ctype_digit($numero) || strlen($numero) !== 10) {
            session()->setFlashdata('erreur', 'Le numero doit contenir 10 chiffres.');
            session()->setFlashdata('prefixe_saisi', $prefixe);
            session()->setFlashdata('suite_saisie', $suite);
            session()->setFlashdata('numero_saisi', $numero);
            return redirect()->to('/client/login');
        }

        $client = $this->clientService->existeParNumero($numero);

        if (!$client) {
            $this->clientService->creerCompte($numero);
        }

        session()->set('client_numero', $numero);
        return redirect()->to('/client/accueil');
    }

    // ---------- LOGIN ----------

    public function login()
    {
        return view('client/login', [
            'prefixes' => $this->prefixeValableService->getAllPrefixeValable(),
        ]);
    }

    public function authentifier()
    {
        return $this->traiterConnexion();
    }

    // ---------- CREATION DE COMPTE ----------

    public function choixPrefixe()
    {
        return redirect()->to('/client/login');
    }

    public function validerNouveauNumero()
    {
        return $this->traiterConnexion();
    }

    // ---------- INFOS PERSONNELLES ----------

    public function info()
    {
        return view('client/info');
    }

    public function enregistrerInfo()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $nom    = trim($this->request->getPost('nom')) ?: null;
        $prenom = trim($this->request->getPost('prenom')) ?: null;

        $this->clientService->updateInfos($numero, $nom, $prenom);

        return redirect()->to('/client/accueil');
    }

    public function passerInfo()
    {
        return redirect()->to('/client/accueil');
    }

    // ---------- ACCUEIL / SOLDE ----------

    public function accueil()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $client     = $this->clientService->existeParNumero($numero);
        $solde      = $this->operationService->calculerSolde($numero);
        $historique = array_slice($this->operationService->getHistorique($numero), 0, 5);

        return view('client/dashboard', [
            'client'     => $client,
            'solde'      => $solde,
            'numero'     => $numero,
            'historique' => $historique,
        ]);
    }

    // ---------- DEPOT ----------

    public function depot()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $solde = $this->operationService->calculerSolde($numero);
        return view('client/depot', ['solde' => $solde]);
    }

    public function traiterDepot()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $montant = (float) $this->request->getPost('amount');

        if ($montant <= 0) {
            session()->setFlashdata('erreur', 'Montant invalide.');
            return redirect()->to('/client/depot');
        }

        $typeId     = $this->getTypeOperationIdParLibelle('depot');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme     = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);
        $frais      = $bareme ? (float) $bareme->montant_frais : 0;

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);

        session()->setFlashdata('succes', 'Depot de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue avec succes.');
        return redirect()->to('/client/accueil');
    }

    // ---------- RETRAIT ----------

    public function retrait()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $solde = $this->operationService->calculerSolde($numero);
        return view('client/retrait', ['solde' => $solde]);
    }

    public function traiterRetrait()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $montant = (float) $this->request->getPost('amount');

        if ($montant <= 0) {
            session()->setFlashdata('erreur', 'Montant invalide.');
            return redirect()->to('/client/retrait');
        }

        $typeId     = $this->getTypeOperationIdParLibelle('retrait');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme     = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);

        if ($bareme === null) {
            session()->setFlashdata('erreur', 'Aucun bareme de frais ne correspond a ce montant.');
            return redirect()->to('/client/retrait');
        }
        $frais = (float) $bareme->montant_frais;

        $solde = $this->operationService->calculerSolde($numero);

        if ($solde < ($montant + $frais)) {
            session()->setFlashdata('erreur', 'Solde insuffisant pour effectuer ce retrait.');
            return redirect()->to('/client/retrait');
        }

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);

        session()->setFlashdata('succes', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue avec succes.');
        return redirect()->to('/client/accueil');
    }

    // ---------- TRANSFERT ----------

    public function transfert()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $solde = $this->operationService->calculerSolde($numero);
        return view('client/transfert', ['solde' => $solde]);
    }

    public function traiterTransfert()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $numeroDest = trim($this->request->getPost('recipient'));
        $montant    = (float) $this->request->getPost('amount');

        if ($numeroDest === $numero) {
            session()->setFlashdata('erreur', 'Impossible de transferer vers son propre numero.');
            return redirect()->to('/client/transfert');
        }

        if (!$this->clientService->existeParNumero($numeroDest)) {
            session()->setFlashdata('erreur', 'Numero destinataire introuvable.');
            return redirect()->to('/client/transfert');
        }

        if ($montant <= 0) {
            session()->setFlashdata('erreur', 'Montant invalide.');
            return redirect()->to('/client/transfert');
        }

        $typeId     = $this->getTypeOperationIdParLibelle('transfert');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme     = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);

        if ($bareme === null) {
            session()->setFlashdata('erreur', 'Aucun bareme de frais ne correspond a ce montant.');
            return redirect()->to('/client/transfert');
        }
        $frais = (float) $bareme->montant_frais;

        $solde = $this->operationService->calculerSolde($numero);

        if ($solde < ($montant + $frais)) {
            session()->setFlashdata('erreur', 'Solde insuffisant pour effectuer ce transfert.');
            return redirect()->to('/client/transfert');
        }

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais, $numeroDest);

        session()->setFlashdata('succes', 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $numeroDest . ' effectue avec succes.');
        return redirect()->to('/client/accueil');
    }

    // ---------- HISTORIQUE ----------

    public function historique()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $typeIdGet = $this->request->getGet('type') ?: null;

        $historique = $this->operationService->getHistorique($numero, $typeIdGet ? (int) $typeIdGet : null);
        $types      = $this->typeOperationService->getAllTypeOperation();

        return view('client/historique', [
            'historique' => $historique,
            'types'      => $types,
            'numero'     => $numero,
            'typeActif'  => $typeIdGet,
        ]);
    }

    public function deconnexion()
    {
        session()->remove('client_numero');
        return redirect()->to('/client/login');
    }
}