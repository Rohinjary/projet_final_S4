<?php

namespace App\Controllers;

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

    // Cherche l'id d'un type d'operation a partir de son libelle
    // (le service partage n'a pas de methode dediee, on filtre ici)
    private function getTypeOperationIdParLibelle(string $libelle): ?int
    {
        $types = $this->typeOperationService->getAllTypeOperation();
        foreach ($types as $t) {
            if ($t->libelle === $libelle) {
                return (int) $t->id;
            }
        }
        return null;
    }

    private function prefixeEstValide(string $prefixe): bool
    {
        $prefixes = $this->prefixeValableService->getAllPrefixeValable();
        foreach ($prefixes as $p) {
            if ($p->prefixe === $prefixe) {
                return true;
            }
        }
        return false;
    }

    // ---------- LOGIN ----------

    public function login()
    {
        return view('Client/login');
    }

    public function authentifier()
    {
        $numero = trim($this->request->getPost('numero'));

        $client = $this->clientService->existeParNumero($numero);

        if ($client) {
            session()->set('client_numero', $client['numero']);
            return redirect()->to('/client/accueil');
        }

        session()->setFlashdata('numero_saisi', $numero);
        return redirect()->to('/client/choix-prefixe');
    }

    // ---------- CREATION DE COMPTE ----------

    public function choixPrefixe()
    {
        $data['prefixes']     = $this->prefixeValableService->getAllPrefixeValable();
        $data['numero_saisi'] = session()->getFlashdata('numero_saisi');
        return view('Client/choixPrefixe', $data);
    }

    public function validerNouveauNumero()
    {
        $prefixe = trim($this->request->getPost('prefixe'));
        $suite   = trim($this->request->getPost('suite'));
        $numero  = $prefixe . $suite;

        if (!$this->prefixeEstValide($prefixe)) {
            session()->setFlashdata('erreur', 'Prefixe non valable.');
            return redirect()->to('/client/choix-prefixe');
        }

        if (strlen($numero) !== 10) {
            session()->setFlashdata('erreur', 'Le numero doit contenir 10 chiffres.');
            return redirect()->to('/client/choix-prefixe');
        }

        if ($this->clientService->existeParNumero($numero)) {
            session()->setFlashdata('erreur', 'Ce numero est deja utilise.');
            return redirect()->to('/client/choix-prefixe');
        }

        $this->clientService->creerCompte($numero);
        session()->set('client_numero', $numero);

        return redirect()->to('/client/info');
    }

    // ---------- INFOS PERSONNELLES ----------

    public function info()
    {
        return view('Client/info');
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

        $client = $this->clientService->existeParNumero($numero);
        $solde  = $this->operationService->calculerSolde($numero);

        return view('Client/accueil', ['client' => $client, 'solde' => $solde]);
    }

    // ---------- DEPOT ----------

    public function depot()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        return view('Client/depot');
    }

    public function traiterDepot()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            session()->setFlashdata('erreur', 'Montant invalide.');
            return redirect()->to('/client/depot');
        }

        $typeId = $this->getTypeOperationIdParLibelle('depot');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);
        $frais  = $bareme ? (float) $bareme->montant_frais : 0;

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);

        session()->setFlashdata('succes', 'Depot effectue avec succes.');
        return redirect()->to('/client/accueil');
    }

    // ---------- RETRAIT ----------

    public function retrait()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        return view('Client/retrait');
    }

    public function traiterRetrait()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            session()->setFlashdata('erreur', 'Montant invalide.');
            return redirect()->to('/client/retrait');
        }

        $typeId = $this->getTypeOperationIdParLibelle('retrait');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);

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

        session()->setFlashdata('succes', 'Retrait effectue avec succes.');
        return redirect()->to('/client/accueil');
    }

    // ---------- TRANSFERT ----------

    public function transfert()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        return view('Client/transfert');
    }

    public function traiterTransfert()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) return $numero;

        $numeroDest = trim($this->request->getPost('numero_destinataire'));
        $montant    = (float) $this->request->getPost('montant');

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

        $typeId = $this->getTypeOperationIdParLibelle('transfert');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);

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

        session()->setFlashdata('succes', 'Transfert effectue avec succes.');
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

        return view('Client/historique', [
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