## Contrôles

### Liste des contrôles <a name="list"></a>

Cet écran permet d’afficher la liste des contrôles et de les filtrer par :

* domaine,

* attribut,

* période de planification,

* état du contrôle : tous, fait et à faire,

* texte libre sur le nom, l’objectif, les observations.

[<img src="/deming/images/c1.png" width="600">](/deming/images/c1.png)

Lorsque vous cliquez sur :

* Le domaine, vous arrivez l'écran d'[affichage du domaine de sécurité](/deming/config/#domains)

* La clause, vous arrivez l'écran d'[affichage de la mesure de sécurité](/deming/measures/#show).

* La date de réalisation, de planification ou la date du contrôle suivant, vous arrivez l'écran d'[affichage du contrôle de sécurité](#show).

### Afficher un contrôle <a name="show"></a>

Cet écran contient les informations d’un contrôle :

* Le nom d’un contrôles ;

* L’objectif du contrôle ;

* Les attributs ;

* Les données ;

* Le modèle ;

* La dates de planification, de réalisation et la date du contrôle suivant ;

* La note attributée au contrôle ; et

* Le score attributé au contrôle (vert, orange ou rouge).

Les boutons « Faire » et « Planifier » sont présents si ce contôle n’a pas encore été réalisé.

Les boutons « Modifier » et « Supprimer » sont présents si l'utilisateur est administrateur.

[<img src="/deming/images/c2.fr.png" width="600">](/deming/images/c2.fr.png)

Lorsque vous cliquez sur :

* « Faire », vous êtes envoyé vers l’[écran de réalisation d’un contrôle](#make)

* « Planifier », vous êtes envoyé vers l’[écran de planification d’un contrôles](#plan)

* « Modifier », vous êtes envoyé vers l’[écran de modification du contrôles](#edit)

* « Supprimer », le contrôle est supprimer et vous êtes envoyé vers la [liste des contrôles](#list)

* « Annuler », vous êtes envoyé vers la [liste des contrôles](#list)

### Planifier un contrôle<a name="plan"></a>

Cet écran permet de planifier un contrôle.

Cet écran contient les informations d’un contrôle :

* Le nom d’un contrôles ;

* L’objectif du contrôle ;

* La date de palnification ;

* La périodicité ; et

* Les responsables de la réalisation du contrôles.

Lorsque vous cliquez sur :

* « Plan », la date de planifierion, la récurence et les responsables sont mis à jour et vous êtres renvoyés vers l’[écran d'affichage 
du contrôles](#show)

* « Annuler », Vous êtres renvoyés vers l’[écran d'affichage du contrôles](#show)


### Réaliser un contrôles <a name="make"></a>

Cet écran permet de réaliser un contrôle de sécurité.

Cet écran contient :

* Le nom du contrôle

* L’objectif

* Les données

* La date de réalisation, la date de planification

* Les observation du contrôle

* Une zone pour sauvegarder les preuves (**CTRL+V** permet de coller une capture d'écran)

* Un lien permettant de télécharger la fiche de contrôles

* Le modèle de calcul appliqué

* La note

* Le score

* Le plan d’action

* La date du prochaine contrôle

 [<img src="/deming/images/c3.png" width="600">](/deming/images/c3.png)
 [<img src="/deming/images/c4.png" width="600">](/deming/images/c4.png)

Lorsque vous cliquez sur :

* « Faire », le contrôle est sauvé et un nouveau contrôle est créé à la date planifiée

* « Sauver », le contrôle est sauvé


et vous revenez vers la [liste des contrôles](#list).


### Fiche de contrôle <a name="sheet"></a>


La fiche de contrôle est un document word généré par l'application sur base des données du contrôle.

Cette fiche permet de décrire les observations réalisés, d'ajouter des captures d'écran ou des tableaux et de __signer électroniquement__ les observations.


[<img src="/deming/images/report3.png" width="600">](/deming/images/report3.png)


La fiche de contrôle peut être [adaptée](/deming/config.fr/#models) afin de respecter le modèle de document de votre organisation.






