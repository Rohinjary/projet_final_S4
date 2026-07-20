1. COMPREHENSION DU SUJET ET CONCEPETION DATABASE
   - Travail demande 
   - depot gitHub
   - conception database:
   - donnee pour SQLite
   - creation models mecessaires
   - recherche template
  
2. REPARTITION DE TACHE
   1. COTE OPERATEUR - Aaron(3886):
      - login choix operateur  
      - Configuration des prefixes valabe de l'operateur 
      - Creation type operations(depot, retait,transfert)
      - tranche de montant modifiable
      - situation gain(filtrable par type operation, prefixe)
      - situation des comptes clients (filtrable)
  
   2. COTE CLIENT - Njary (3966):

    - login avec numero telephone sans mdp:
        . Client.php (model):
            - existeParNumero($numero)
            - creerCompte($numero, $nom, $prenom)
        . ClientController.php:
            - login() → affiche formulaire numero
            - authentifier() → verifie existeParNumero(), redirige vers accueil() si existe
        . view/Client/login.php

    - si numero pas trouve → choix prefixe valable + saisie 10 chiffres:
        . Prefixe.php (model, lecture seule):
            - getPrefixesActifs()
        . ClientController.php:
            - choixPrefixe() → affiche formulaire prefixe + numero
            - validerNouveauNumero() → verifie prefixe valide (getPrefixesActifs) + numero pas deja utilise (existeParNumero) → appelle creerCompte()
        . view/Client/choixPrefixe.php

    - page infos personnelles (nom, prenom) pas obligatoire:
        . Client.php (model):
            - updateInfos($clientId, $nom, $prenom)
        . ClientController.php:
            - info() → affiche formulaire
            - enregistrerInfo() → appelle updateInfos() ou skip() si passe
        . view/Client/info.php

    - situation solde / page d'accueil:
        . Operation.php (model):
            - calculerSolde($clientId)
        . ClientController.php:
            - accueil() → recupere client_id session, appelle calculerSolde()
        . view/Client/accueil.php

    - faire un depot:
        . BaremeFrais.php (model, lecture seule):
            - getFrais($typeOperationId, $montant)
        . Operation.php (model):
            - enregistrer($clientId, $typeId, $montant, $frais, $clientDestId = null)
        . ClientController.php:
            - depot() → affiche formulaire
            - traiterDepot() → getFrais() + enregistrer() + recalcul solde (calculerSolde())
        . view/Client/depot.php

    - faire un retrait:
        . Operation.php (model):
            - soldeSuffisant($clientId, $montant, $frais) [ou verif directe dans controller via calculerSolde()]
        . ClientController.php:
            - retrait() → affiche formulaire
            - traiterRetrait() → getFrais() + verif solde suffisant + enregistrer() + recalcul solde
        . view/Client/retrait.php

    - faire un transfert:
        . Client.php (model):
            - existeParNumero($numeroDestinataire) [reutilisation]
        . ClientController.php:
            - transfert() → affiche formulaire
            - traiterTransfert() → verif numero destinataire existe + getFrais() + verif solde suffisant + enregistrer() (client_id=expediteur, client_dest_id=destinataire) + recalcul solde
        . view/Client/transfert.php

    - voir historiques des operations:
        . Operation.php (model):
            - getHistorique($clientId, $typeId = null)
        . ClientController.php:
            - historique() → appelle getHistorique(), filtre via param GET type
        . view/Client/historique.php

    - gestion d'operation (regle transverse, pas une page a part):
        . appliquee dans traiterRetrait() et traiterTransfert()
        . si calculerSolde($clientId) < montant + frais → return erreur, pas d'appel a enregistrer()