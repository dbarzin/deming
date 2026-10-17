TODO
----

Améliorations réalisées :
x filtre sur les clauses dans la liste des contrôles
x liste des contrôles en dessous d'une mesure
x contrôles suivants dans le calendrier
x amélioration du champ search dans la liste des contrôles
x Ajouter les observation en cours dans le formulaire de contrôle
  https://github.com/dbarzin/deming/discussions/309
x Supprimer site de la table controls
x Cleanup controls table
x Journalisation des actions réalisées
x Control / make -> click on checked radio
x Deming suivi d'une note de le temps
x Ajout Linux pratique sur siteweb
- OW2 - LinuxFR - Pépites de la tech - Hackaton.lu - Le comptoir du Libre
x Packaging avec Vite et Composer
x Automatisation du numéro de Version -> voir Mercator
x Ajouter plan d'action comme paramètre de la fiche de contrôle  
x Migration vers Metro 5.1
x Groupes d'utilisateur responsable des contrôles
  (un utilisateur peut faire partie de plusieurs groupes)
x Ajouter la liste des frameworks disponibles dans la présentation de Deming
x Autoremove controls - actions et documents
x Action - ajouter % - rapport idem tableau de bord avec scope (???)
x Documenter cleanup
x Distinction environement de test
  https://github.com/dbarzin/deming/discussions/387
x Ajouter la liste des référentiels sur le site
x Bug file upload in Metro 5.1
  https://github.com/olton/metroui/issues/1978
x Bug import/export des framework
x Manque état d'avancement dans l'export des plans d'action
x Fixer affichage des logs
x Fixer Docker
x Test et suppression depuis l'interface
x Filtre début-fin sur les actions
x Indicateur par mesure - choix clause - plusieurs contrôles
x Pagination des logs
  https://v5.metroui.org.ua/components/remote-table/#dependencies
x Ajouter bouton d'exécution du cleanup des controles
  https://github.com/dbarzin/deming/discussions/417
x APP_ENV et FORCE_HTTPS
x Note avec deux décimales
  https://github.com/dbarzin/deming/discussions/390
  https://chatgpt.com/g/g-p-67ee2fb242588191a38f354011cf8700-deming/c/683d5eee-02a0-8011-b7cd-9f7463dd1cc0
x Rapport des indicateurs clés
  https://github.com/dbarzin/deming/discussions/308
x message dans README pour AI
x fix messages d'erreur sur la page +  aide lien ouverture pulsar + doc
x Update LDAPRecord version
  - chat gpt - LDAP + Active Directory
  - KeyCloack - environnement de test

* logs des risques
* export des risques
* documentation
  * migrer la documentation (idem Mercator)
  * captures d'écrans
* tester Docker dans une VM

* modification de la formule (model) lors de la modification d'un contrôle
* tableau de bord par mesure 
  -> afficher uniquement les clauses qui ont un contrôle réalisé 
  -> ajouter le périmètre comme filtre
* planifier un contrôle - largeur du tag trop petite
* liste des contrôles 
  -> prendre tout les périmètres qui existent (pas uniquement les actifs)
* Liste des exceptions à la politique de sécurité
* couleur du risque - mettre le texte en noir si trop clair
* sélection des clauses avec search et nom du contrôle dans
    - les plans actions
    - la gestion des utilisateurs
    - création d'un contrôle
* inverser Controls <-> Measures
* Configuration du scoring des contrôles (1-10)
  -> changer les smiles en boules de couleur -> généraliser le code -> méthode dans contrôle
  -> sauvegarde de la couleur, valeur et nom dans le contrôle

=======

Améliorations 
----

- Drop documents on edit control page

- Deming rapport custom avec plus de champs

- Passer à Bootstrap 5

- Modifier une exception fermée

- Ajouter des valeurs par défaut pour les scoring BSI....
- Revoir la matrice en fonction des scoring

- Export des contrôles pour l'audit
  -> améliorer la sélection de contrôles (mesures ?)

- Améliorer calendrier et homepage avec bleu pâle (contrôles à valider)

- Activer/Désactiver les risques et les exceptions

- Groupes de contrôles pour les audits

- Sankey
    - framework - controls
    - framework - framework

- OSCAL - Open Security Control Assment Language

- Code Quality with PHPStan
  ./vendor/bin/phpstan analyze

- Contribution LaraStan - Select and Pluck


==============
Docker
==============

List all images

docker images

Build the image from Dockerfile

docker build --no-cache -t deming .

Lancer la version docker

docker run -it --rm -p 80:8080 --name deming ghcr.io/dbarzin/deming:latest

docker run -it --rm -p 80:8080 --name deming deming

Se connecter au container pour exécuter un shell

docker exec -it deming bash

-----

J'ai pensé à une évolution possible de Deming, en y ajoutant un menu d'audit.

Celui-ci serait une liste de sujets, les questions de l'audit, et d'une période qui correspond aux dates des mesurements effectuées.

Ces sujets sont à lier à des contrôles, puis automatiquement, on aurait un dashboard de toutes les mesurements en place, avec leur résultat, et surtout, on pourrait choisir de télécharger ces résultats ainsi que les preuves en tant que pièces jointes.

De cette façon, l'audit se fait en montrant les mesurements dans Deming (on pourrait même avoir sur chaque control ou mesurements une option pour "ajouter à l'audit"), puis on n'aurait qu'à demander le rapport pour fournir les mesurements, leur résultat ainsi que leurs évidences.

Je pense que ça permettrait de gagner énormément de temps, premièrement dans un audit interne, mais aussi pour tous les autres audits.

=====

use LdapRecord\Container;

$dn = 'cn=jean.dupont,dc=example,dc=org';
Container::getConnection()->auth()->attempt($dn, '123456', true);
