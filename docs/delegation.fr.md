## Délégation

Deming permet de déléguer la réalisation des contrôles de sécurité à des utilisateurs qui ont le rôle *audité*.
L'assignation du rôle audité à un utilisateur se fait via l'écran de [gestion des utilisateurs](/deming/config.fr/#users).

Cette délégation respecte les règles suivantes :

- Les audités sont informés régulièrement par mail des contrôles à réaliser ;
- Les audités ne voient que les contrôles qui leur sont assignés et les contrôles qu'ils ont réalisés précédemment ;
- Les utilisateurs peuvent accepter ou refuser un contrôle réalisé par un audité ;
- Lorsqu'un contrôle est refusé, il retourne dans la liste des contrôles à réaliser de l'audité.


### Liste des contrôles à réaliser

Du point de revu de l'audité, la page principale de l'application contient la liste des contrôles qui lui sont assignés.

[<img src="/deming/images/d1.fr.png" width="600">](/deming/images/d1.fr.png)

L'audité peut :

- Effectuer des recherches dans la liste des contrôles
- Filtrer les contrôles par domaine, périmètre, attribut, période.
- trier la liste par chacune des colonnes
- sélectionner un contrôle à réaliser


### Réaliser un contrôle

Lorsqu'un audité réalise un contrôle, il peut :

- Sauver le contrôle

- Faire le contrôle

[<img src="/deming/images/d2.fr.png" width="300">](/deming/images/d2.fr.png)

Lorsqu'il clique sur :

- "Sauver", les modifications qu'il a faites sont sauvées, le contrôle reste dans la liste des contrôles à réaliser

- "Faire", les modifications qu'il a faite sont sauvées et le contrôle passe dans l'état à valider. Du point de vue de l'audité, le contrôle se trouve dans la liste des contrôles réalisés.

- "Annuler", les modifications ne sont pas sauvée, lutilisateur retourne vers la vue du contrôle.


### Accepter / Refuser un contrôle

Une fois qu'un contrôle a été réalisé par un audité, il passe dans l'état "à valider".

Cela se matérialise pour les autres utilisateurs par un sablier à côté de la date de réalisation dans la liste des contrôles à réaliser :

[<img src="/deming/images/d3.fr.png" width="600">](/deming/images/d3.fr.png)

Lorsque l'utilisateur clique sur la date de réalisation à côté du sablier, il arrive sur le contrôle réalisé par l'audité.

Il peut alors accepter ou refuser le contrôle en ajoutant une note dans les observations du contrôle et, selon les résultats du contrôle, proposer un plan d'action et une date de revue du contrôle.

[<img src="/deming/images/d4.fr.png" width="600">](/deming/images/d4.fr.png)

Si l'utilisateur clique sur :

- "Accepter" : les données modifiées sont sauvées et un nouveau contrôle est créé à la date de planification introduite.

- "Rejeté" : les données modifiées sont sauvées et le contrôle est renvoyé dans la liste des contrôles à réaliser de l'audité.

- "Sauvé" : les données sont sauvées et l'utilisateur revient à la vue du contrôle.

- "Annuler" : l'utilisateur revient à la vue du contrôle.
