1. COMPREHENSION DU SUJET ET CONCEPETION DATABASE
   - Travail demande 
   - depot gitHub
   - conception database:
   - donnee pour SQLite
   - creation models mecessaires
   - recherche template
  
2. REPARTITION DE TACHE
   VERSION 1:
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

VERSION 2
   1. COTE OPERATEUR(Aaron):
   2. COTE CLIENT(Njary):
      - Inclure frais de retrait lors de l'envoi
            . modifier view/client/transfert.php: ajout choix si inclure frais de retrait
            .  alter table operation: creer colonne fraisRetrais ou l'on doit inserer le frais de retrait avec le transfert si on choisit de l'inclure
            . fonction modifiee : + fraisRetrait (pour toutes les calculs de solde)
            . fonction modifiee pour l'insertion de fraisRetrait si choisi comme option
            . modifier view/client/historique.php : ajout de fraisRetrait s'il y en a 
            . option indisponible pour inclure fraisRetrait si autre operateur
       -  Envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)
            . modifier  view/client/transfert.php: choix multiples d'envoi d'argent a plusieurs numero(dire que ce n'est pas possible de faire cette operation avec un autre operateur) -> gestion d'erreur: garder les valeurs de numero sur l'interface meme apres l'action avec erreur
            . fonction divisant le montant pour le nombre de numero destine
            . c'est cette somme divisee qu'on va chercher son frais de transfert et frais de retrait, donc pour le calcul on fiat juste cela *nombre de destinataire
            . historique devient les transactions avec les nombre de destinataires
            . gestion de montant non divisble(je ne sais pas)