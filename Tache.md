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
  
   2. COTE CLIENT - - Njary (3966):
      - login avec numero telephone sans mdp:4
      - - login et attribution numero auto si pas encore client
            . Client.php: si num existe dans database, page d'acceuil(solde + action consultation etc ), sinon, choix prefixe + generation numero unique a 10 chiffre et insrtion dans base table client
            . ClientController.php
            . view/Client/login.php
      - page ou l'on met les informations personnelle (nom,     prenom), pas obligatoire puis insertion a la base(donc le seul obligatoire dans insertion table client est numero ):
            .view/Client/info.php

      - situation solde : page d'accueil
        . table "operation"(somme montant + fais mais ca depend si c'est somme ou pas a partir de type_operation)
        . controllers qui calcul le solde final
        . view/Client/accueil.php 
      - faire un depot 
        . insertion montant
        . verification si c'est dans baremeFrais ou pas
        . ajout dans operation(auto)
        .recalcul solde 
        .view/Client/depot.php
      -  faire un retrait
        . insertion montant
        . verification si c'est dans baremeFrais ou pas
        . ajout dans operation(auto)
        .recalcul solde 
        .view/Client/retrait.php
      -  faire un transfert
        . insertion montant a transferer
        . verification si c'est dans baremeFrais ou pas
        . ajout dans operation(auto)
        .recalcul solde 
        .view/Client/depot.php  
      -  voir les historiques des operations faits 