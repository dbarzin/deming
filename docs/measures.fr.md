## Mesures de sécurité

Cette partie permet de définir, de modifier et de plannifier de nouvelles mesures de sécurité.

### Liste des mesures de sécurité <a name="list"></a>

La liste des mesures de sécurité permet d’afficher la liste des mesures, de les filtrer par domaine ou de recherche une mesure sur base d’une partie de son nom.

[<img src="/images/m1.png" width="600">](/images/m1.png)

En cliquant sur :

* le domaine, vous arrivez sur la [définition du domaine choisi](/deming/config.fr/#domain)

* la clause, vous arrivez sur la [description de la mesure de sécurité](#show)

* la date, vous arrivez l’écran de [planification de la mesure de sécurité](#plan)


### Afficher une mesure de sécurité <a name="show"></a>

Cet écran permet d’afficher une mesure de sécurité.

[<img src="/images/m2.png" width="600">](/images/m2.png)

Une mesure de sécurité est composée :

* d’un domaines ;

* d’un nom ;

* d’un objectif de sécurité ;

* d’attributs ;

* des données de mesures :

* du modèle de vérification ;

* d’un indicateur (vert, orange, rouge)

* d’un plan d’action à appliquer si le contrôle de cette mesure de sécurité est en échec ;

* de responsable ; et

* de la périodicité de la mesure (mesuel, trimestriel, disanuel, annuel).


Lorsque vous cliquez sur :

* « Planifier » : vous arrivez vers l’écran de [planification de la mesure de sécurité](#plan).

* « Modifier » : vous arrivez vers [l’écran de moficiaton de la mesure](#edit)

* « Supprimer »: vous permet de supprimer la mesure et de revenir à [la liste des mesures de sécurité](#list)

* « Annuler » : vous renvoie vers la [liste des mesures de séucurité](#list)


### Modifier une mesure de sécurité <a name="edit"></a>

Cet écran permet de modifier une mesure de sécurité.

[<img src="/images/m3.png" width="600">](/images/m3.png)


Lorsque vous cliquez sur :

* « Sauver », les modification sont sauvées et vous revenez à l’écran d’[affichage de la mesure de sécurité](#show).

* « Annluer », vous revenez à l’écran d’[affichage de la mesure de sécurité](#show).


### Plannifier un contrôle <a name="plan"></a>

Cet écran permet de planifier un contrôle de sécurité à partir d’une mesure.

L’écran contient :

* Le domaine ;

* Le nom de la mesure ;

* L’objectif de sécurité ;

* La périodicité du contrôles ; et

* La date de planification.


[<img src="/images/plan.png" width="600">](/images/plan.png)

Lorsque vous cliquez sur : 

* « Planifier », 

    * s’il n’existe pas de contrôle pour cette mesure, un nouveau contrôle est créé en copiant toutes les données de le mesure et est planifié à la date spécifiée.

    * S’il existe déjà un contrôle non-réalisé pour cette mesure de sécurité, la date de planification est mise à jour.

	Vous revenez ensuite à la [liste des mesures de sécurité](#list).

* « Déplanifier », le contrôle de sécurité associé à cette mesure est supprimé et l’utilisateur revient vers la [liste des mesures de sécurité](#list).

* « Annuler », vous revenez vers la [liste des mesures de sécurité](#list).

