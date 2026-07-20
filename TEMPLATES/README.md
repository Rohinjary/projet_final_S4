# MobiPay — version HTML / Bootstrap

Ce dossier contient la conversion du template React/TypeScript en pages HTML classiques avec :

- Bootstrap 5 pour la mise en page responsive ;
- un seul fichier CSS personnalisé : `assets/css/style.css` ;
- un seul fichier JavaScript : `assets/js/app.js` ;
- des pages séparées pour l'espace client et l'espace opérateur ;
- une simulation fonctionnelle grâce à `localStorage` (solde, opérations, préfixes et barèmes).

## Lancer le prototype

Ouvrir simplement `index.html` dans un navigateur. Une connexion Internet est nécessaire pour charger Bootstrap et Bootstrap Icons depuis le CDN.

Numéro de démonstration : `034 12 345 67`.

## Pages principales

- `index.html` : connexion automatique par numéro ;
- `client/` : tableau de bord, dépôt, retrait, transfert, historique ;
- `admin/` : tableau de bord, préfixes, types/barèmes, gains, comptes clients.

## Intégration CodeIgniter 4

Ces fichiers représentent uniquement la couche frontend. Pour CodeIgniter 4, les pages peuvent être déplacées dans `app/Views/`, les liens remplacés par `site_url()` et les données JavaScript par des données venant des contrôleurs et de SQLite.

Le sujet d'examen demande aussi un fichier `Taches.md`, un fichier `base.sql`, des livraisons par tags Git (`v1`, `v2`, `v3`) et une branche finale `main`. Ces éléments devront être ajoutés au projet CodeIgniter complet.

## Interface client responsive

- À partir de **768 px**, l'espace client utilise une présentation ordinateur avec une barre latérale verticale.
- En dessous de **768 px**, l'interface mobile d'origine est conservée avec la navigation en bas de l'écran.
