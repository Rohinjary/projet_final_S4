<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Services\BaremeFraisService;
use App\Services\ClientService;
use App\Services\CommissionOperateurService;
use App\Services\OperationService;
use App\Services\OperateurService;
use App\Services\PrefixeValableService;
use App\Services\TypeOperationService;
use DateTime;
use CodeIgniter\HTTP\RedirectResponse;

class ClientController extends BaseController
{
    private ClientService $clientService;
    private OperationService $operationService;
    private PrefixeValableService $prefixeValableService;
    private TypeOperationService $typeOperationService;
    private BaremeFraisService $baremeFraisService;
    private CommissionOperateurService $commissionOperateurService;
    private OperateurService $operateurService;

    public function __construct()
    {
        $this->clientService = new ClientService();
        $this->operationService = new OperationService();
        $this->prefixeValableService = new PrefixeValableService();
        $this->typeOperationService = new TypeOperationService();
        $this->baremeFraisService = new BaremeFraisService();
        $this->commissionOperateurService = new CommissionOperateurService();
        $this->operateurService = new OperateurService();
    }

    private function verifierSession()
    {
        $numero = session()->get('client_numero');
        if (! $numero) {
            return redirect()->to('/client/login');
        }

        return $numero;
    }

    private function getTypeOperationIdParLibelle(string $libelle): ?int
    {
        foreach ($this->typeOperationService->getAllTypeOperation() as $typeOperation) {
            if (($typeOperation['libelle'] ?? null) === $libelle) {
                return (int) $typeOperation['id'];
            }
        }

        return null;
    }

    private function prefixeEstValide(string $prefixe): bool
    {
        foreach ($this->prefixeValableService->getAllPrefixeValable() as $prefixeValable) {
            if (($prefixeValable['prefixe'] ?? null) === $prefixe) {
                return true;
            }
        }

        return false;
    }

    private function getOperateurIdParNumero(string $numero): ?int
    {
        $prefixe = substr($numero, 0, 3);
        $prefixeValable = $this->prefixeValableService->getPrefixeValableByPrefixe($prefixe);

        if (! is_array($prefixeValable) || ! array_key_exists('operateur_id', $prefixeValable) || $prefixeValable['operateur_id'] === null) {
            return null;
        }

        return (int) $prefixeValable['operateur_id'];
    }

    private function memeOperateur(string $numeroA, string $numeroB): bool
    {
        $operateurA = $this->getOperateurIdParNumero($numeroA);
        $operateurB = $this->getOperateurIdParNumero($numeroB);

        if ($operateurA !== null && $operateurB !== null) {
            return $operateurA === $operateurB;
        }

        return substr($numeroA, 0, 3) === substr($numeroB, 0, 3);
    }

    private function extraireDestinataires(string $valeur): array
    {
        $valeur = trim(str_replace(["\r\n", "\r"], "\n", $valeur));

        if ($valeur === '') {
            return [];
        }

        $fragments = preg_split('/[\s,;]+/', $valeur, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $destinataires = [];

        foreach ($fragments as $fragment) {
            $numero = trim($fragment);
            if ($numero !== '') {
                $destinataires[] = $numero;
            }
        }

        return array_values($destinataires);
    }

    private function transfertAvecErreur(string $message)
    {
        session()->setFlashdata('erreur', $message);
        return redirect()->to('/client/transfert')->withInput();
    }

    private function traiterConnexion()
    {
        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $suite   = trim((string) $this->request->getPost('suite'));
        $numero  = trim((string) $this->request->getPost('numero'));

        if ($prefixe !== '' || $suite !== '') {
            if (! $this->prefixeEstValide($prefixe)) {
                session()->setFlashdata('erreur', 'Prefixe non valable.');
                session()->setFlashdata('prefixe_saisi', $prefixe);
                session()->setFlashdata('suite_saisie', $suite);
                return redirect()->to('/client/login');
            }

            if (! ctype_digit($suite) || strlen($suite) !== 7) {
                session()->setFlashdata('erreur', 'Le reste du numero doit contenir 7 chiffres.');
                session()->setFlashdata('prefixe_saisi', $prefixe);
                session()->setFlashdata('suite_saisie', $suite);
                return redirect()->to('/client/login');
            }

            $numero = $prefixe . $suite;
        }

        if (! ctype_digit($numero) || strlen($numero) !== 10) {
            session()->setFlashdata('erreur', 'Le numero doit contenir 10 chiffres.');
            session()->setFlashdata('prefixe_saisi', $prefixe);
            session()->setFlashdata('suite_saisie', $suite);
            session()->setFlashdata('numero_saisi', $numero);
            return redirect()->to('/client/login');
        }

        $client = $this->clientService->existeParNumero($numero);
        if (! $client) {
            $this->clientService->creerCompte($numero);
        }

        session()->set('client_numero', $numero);

        return redirect()->to('/client/accueil');
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
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $nom    = trim((string) $this->request->getPost('nom')) ?: null;
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
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $client     = $this->clientService->existeParNumero($numero);
        $solde      = $this->operationService->calculerSolde($numero);
        $historique = array_slice($this->operationService->getHistorique($numero), 0, 5);

        return view('client/dashboard', [
            'client'     => $this->clientService->existeParNumero($numero),
            'solde'      => $this->operationService->calculerSolde($numero),
            'numero'     => $numero,
            'historique' => array_slice($this->operationService->getHistorique($numero), 0, 5),
        ]);
    }

    public function depot()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $solde = $this->operationService->calculerSolde($numero);
        return view('client/depot', ['solde' => $solde]);
    }

    public function traiterDepot()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $montant = (float) $this->request->getPost('amount');

        if ($montant <= 0) {
            return $this->clientError('client/depot', 'Montant invalide.');
        }

        $typeId     = $this->getTypeOperationIdParLibelle('depot');
        $maintenant = (new DateTime())->format('Y-m-d H:i:s');
        $bareme     = $this->baremeFraisService->getBaremeFraisMontant($typeId, $montant, $maintenant);
        $frais      = $bareme ? (float) $bareme['montant_frais'] : 0.0;

        $frais = (float) $bareme['montant_frais'];
        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);
        session()->setFlashdata('succes', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');
        return redirect()->to(site_url('client/accueil'));
    }

    public function retrait()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $solde = $this->operationService->calculerSolde($numero);
        return view('client/retrait', ['solde' => $solde]);
    }

    public function traiterRetrait()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $montant = (float) $this->request->getPost('amount');

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
        $solde = $this->operationService->calculerSolde($numero);

        if ($solde < ($montant + $frais)) {
            session()->setFlashdata('erreur', 'Solde insuffisant pour effectuer ce retrait.');
            return redirect()->to('/client/retrait');
        }

        $this->operationService->enregistrer($numero, $typeId, $montant, $frais);
        session()->setFlashdata('succes', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');
        return redirect()->to(site_url('client/accueil'));
    }

    public function transfert()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $solde = $this->operationService->calculerSolde($numero);
        return view('client/transfert', ['solde' => $solde]);
    }

    public function traiterTransfert()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

        $recipientsInput      = (string) ($this->request->getPost('recipients') ?? $this->request->getPost('recipient') ?? '');
        $montantBrut          = $this->request->getPost('amount');
        $inclureFraisRetrait  = $this->request->getPost('inclure_frais_retrait') ? true : false;
        $destinataires        = $this->extraireDestinataires($recipientsInput);
        $destinatairesUnique  = array_values(array_unique($destinataires));

        if ($destinataires === []) {
            return $this->transfertAvecErreur('Veuillez saisir au moins un numero destinataire.');
        }

        if (count($destinatairesUnique) !== count($destinataires)) {
            return $this->transfertAvecErreur('Chaque numero destinataire doit etre unique.');
        }

        if (! is_numeric($montantBrut) || (float) $montantBrut <= 0) {
            return $this->transfertAvecErreur('Montant invalide.');
        }

        $montant = (int) round((float) $montantBrut);
        $nombreDestinataires = count($destinataires);

        if ($nombreDestinataires > 1 && ($montant % $nombreDestinataires) !== 0) {
            return $this->transfertAvecErreur('Le montant doit etre divisible par le nombre de destinataires.');
        }

        $montantParDestinataire = (float) ($montant / $nombreDestinataires);
        $typeIdTransfert        = $this->getTypeOperationIdParLibelle('transfert');
        if ($typeIdTransfert === null) {
            return $this->transfertAvecErreur('Le type d operation transfert n est pas configure.');
        }

        $maintenant             = (new DateTime())->format('Y-m-d H:i:s');
        // $baremeTransfert        = $this->baremeFraisService->promotionBareme($typeIdTransfert, $montantParDestinataire, $maintenant);

        if ($baremeTransfert === null) {
            return $this->transfertAvecErreur('Aucun bareme de frais ne correspond a ce montant.');
        }

        $fraisTransfert = (float) $baremeTransfert['montant_frais'];
        $fraisRetrait   = 0.0;
        $memeOperateur  = true;
        $commissionsParDestinataire = [];
        $commissionTotale = 0.0;

        foreach ($destinataires as $destinataire) {
            if ($destinataire === $numero) {
                return $this->transfertAvecErreur('Impossible de transferer vers son propre numero.');
            }

            if (! ctype_digit($destinataire) || strlen($destinataire) !== 10) {
                return $this->transfertAvecErreur('Chaque numero destinataire doit contenir 10 chiffres.');
            }

            $prefixeDestinataireValide = $this->prefixeEstValide(substr($destinataire, 0, 3));

            if (! $prefixeDestinataireValide) {
                return $this->transfertAvecErreur(
                    'Le prefixe du numero ' . $destinataire . ' n est rattache a aucun operateur configure.'
                );
            }

            if (! $this->clientService->existeParNumero($destinataire)) {
                return $this->transfertAvecErreur('Numero destinataire introuvable : ' . $destinataire . '.');
            }

            $operateurDestinataireId = $this->getOperateurIdParNumero($destinataire);
            $estMemeOperateur = $this->memeOperateur($numero, $destinataire);
            $memeOperateur = $memeOperateur && $estMemeOperateur;
            $commissionOperateur = 0.0;

            if (! $estMemeOperateur) {
                if ($operateurDestinataireId === null) {
                    return $this->transfertAvecErreur('Aucun operateur ni taux de commission n est configure pour le numero ' . $destinataire . '.');
                }

                $operateurDestinataire = $this->operateurService->getOperateurById($operateurDestinataireId);
                $estOperateurPrincipal = (int) ($operateurDestinataire['est_principal'] ?? 0) === 1;

                if (! $estOperateurPrincipal) {
                    $configurationCommission = $this->commissionOperateurService
                        ->getCommissionByOperateurId($operateurDestinataireId);

                    if ($configurationCommission === null) {
                        return $this->transfertAvecErreur(
                            'Aucun taux de commission n est configure pour l operateur du numero ' . $destinataire . '.'
                        );
                    }

                    $tauxCommission = max(0.0, (float) $configurationCommission['pourcentage']);
                    $commissionOperateur = round($montantParDestinataire * $tauxCommission / 100, 2);
                }
            }

            $commissionsParDestinataire[$destinataire] = $commissionOperateur;
            $commissionTotale += $commissionOperateur;
        }

        if ($nombreDestinataires > 1 && ! $memeOperateur) {
            return $this->transfertAvecErreur('L envoi multiple est reserve aux numeros du meme operateur.');
        }

        if ($inclureFraisRetrait && ! $memeOperateur) {
            return $this->transfertAvecErreur('Les frais de retrait ne peuvent etre inclus que pour un envoi vers le meme operateur.');
        }

        if ($inclureFraisRetrait) {
            $typeIdRetrait = $this->getTypeOperationIdParLibelle('retrait');
            if ($typeIdRetrait === null) {
                return $this->transfertAvecErreur('Le type d operation retrait n est pas configure.');
            }

            $baremeRetrait = $this->baremeFraisService->getBaremeFraisMontant($typeIdRetrait, $montantParDestinataire, $maintenant);

            if ($baremeRetrait === null) {
                return $this->transfertAvecErreur('Aucun bareme de frais de retrait ne correspond a ce montant.');
            }

            $fraisRetrait = (float) $baremeRetrait['montant_frais'];
        }

        // Débit client : montant transféré + frais MobiPay + commission partenaire.
        // Les frais et la commission restent deux flux distincts.
        $soldeTotal = $nombreDestinataires * ($montantParDestinataire + $fraisTransfert + $fraisRetrait)
            + $commissionTotale;
        $solde = $this->operationService->calculerSolde($numero);

        if ($solde < $soldeTotal) {
            return $this->transfertAvecErreur('Solde insuffisant pour effectuer ce transfert.');
        }

        $referenceTransfert = 'TR-' . date('YmdHis') . '-' . bin2hex(random_bytes(3));

        foreach ($destinataires as $destinataire) {
            $this->operationService->enregistrer(
                $numero,
                $typeIdTransfert,
                $montantParDestinataire,
                $fraisTransfert,
                $destinataire,
                $fraisRetrait,
                $referenceTransfert,
                $nombreDestinataires,
                (float) ($commissionsParDestinataire[$destinataire] ?? 0)
            );
        }

        $messageSucces = 'Transfert de ' . number_format($montant, 0, ',', ' ')
            . ' Ar vers ' . $nombreDestinataires . ' destinataire(s) effectue avec succes.';
        if ($commissionTotale > 0) {
            $messageSucces .= ' Commission partenaire : '
                . number_format($commissionTotale, 0, ',', ' ')
                . ' Ar. Total debite : '
                . number_format($soldeTotal, 0, ',', ' ')
                . ' Ar.';
        }

        session()->setFlashdata('succes', $messageSucces);

        return redirect()->to('/client/accueil');
    }

    public function historique()
    {
        $numero = $this->verifierSession();
        if ($numero instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $numero;
        }

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
}
