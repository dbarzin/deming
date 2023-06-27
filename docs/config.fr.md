## Configuration

### Attributs <a name="tags"></a>

Cet écran permet de gérer les attributs associés aux mesures de sécurité. 
Il contient la liste des attributs et permet de créer, supprimer ou modifier des listes d’attributs.

[<img src="/deming/images/tags.png" width="600">](/deming/images/tags.png)

### Domaines <a name="domains"></a>

Cet écran permet de créer, modifier ou supprimer des liste de domaines de sécurité.

[<img src="/deming/images/domains.png" width="600">](/deming/images/domains.png)

L’application est founir avec une base de mesure de sécurité inspirée de la norme ISO 27001:2022 mais il est possible de définir de nouveaux domaines de sécurité inspirés d’autres normes comme PCIDSS, HDS...

### Utilisateurs <a name="users"></a>

Les utilisateurs sont définits dans l’application.

[<img src="/deming/images/users.png" width="600">](/deming/images/users.png)

Il existe trois roles :

* RSSI : le RSSI est l’adminsitrateur de l’application. Il peut créer de nouvelles mesures, de nouveaux attributs, modifier des contrôles déjà réalisés...

* Utilisateurs : les utilisateur peuvent utiliser l’application sans pouvoir modifier les mesures, les attributs et les contrôles déjà réalisés.
   
* Auditeur : l’auditeur a un accès en lecture à l’enseble des informations de l’application.


### Rapports <a name="report"></a>

L’application permet de générer le rapport de pilotage du SMSI et d’exporter dans un fichier Excell la liste des domains, les mesures de sécurités et tous les contrôles réalisés.

[<img src="/deming/images/reports.png" width="500">](/deming/images/reports.png)


Voici le rapport de pilotage du SMSI : 

[<img src="/deming/images/report1.png" width="600">](/deming/images/report1.png)

[<img src="/deming/images/report2.png" width="600">](/deming/images/report2.png)

### Import <a name="import"></a>

Il est possible d'importer des mesures de sécurité depuis un fichier .XLSX.

[<img src="/deming/images/import.png" width="600">](/deming/images/import.png)

### Documents <a name="documents"></a>

Cet écran permet de modifier les modèles de document utilisés pour les fiches de contrôle et le rapport de pilotage du SMSI.
et permet d’avoir une vue sur l’ensemble des documents utilisés comme preuve lors de la réalisation des contrôles de sécurité.

[<img src="/deming/images/documents.png" width="300">](/deming/images/documents.png)

Le bouton « Vérifier » permet de vérifier l’intégrité des documents conservés dans l'application.

### Configration <a name="config"></a>

Cet écran permet de configurer les notifications envoyées aux utilisateurs lorsqu'ils doivent réaliser des contrôles.

L'écran contient :

* Le sujet du mail envoyé à l'utilisateur;

* L'émetteur du mail;

* La périodicité de l'envoi des notifications;

* Le délais de notification.

[<img src="/deming/images/config.png" width="300">](/deming/images/config.png)

Lorsque vous cliquer sur :

* "Sauver" - la configuration est sauvée;

* "Test" - un mail de test est envoyé à l'utilisateur courant ;

* "Cancel" - vous revenez à la page principale.

