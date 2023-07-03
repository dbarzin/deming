-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: deming
-- ------------------------------------------------------
-- Server version	8.0.32-0ubuntu0.22.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` (`id`, `title`, `description`, `created_at`, `updated_at`) VALUES 
(7,'27001:2013','SMSI','2021-03-08 05:42:12','2023-04-06 17:30:50'),
(8,'27001:2013-A5','Politique de Sécurité','2019-08-08 03:40:27','2023-04-06 17:31:03'),
(9,'27001:2013-A6','Organisation de la sécurité de l\'information','2019-08-08 03:41:07','2023-04-06 17:31:18'),
(10,'27001:2013-A7','Sécurité des ressources humaines','2019-08-08 03:41:25','2023-04-06 17:31:55'),
(11,'27001:2013-A8','Gestion des actifs','2019-08-08 03:41:36','2023-04-06 17:32:10'),
(12,'27001:2013-A9','Contrôle d\'accès','2019-08-08 03:41:55','2023-04-06 17:32:38'),
(13,'27001:2013-A10','Cryptographie','2019-08-08 03:42:08','2023-04-06 17:32:22'),
(14,'27001:2013-A11','Sécurité physique et environnementale','2019-08-08 03:42:52','2023-04-06 17:32:51'),
(15,'27001:2013-A12','Sécurité liée à l’exploitation','2019-08-08 03:43:08','2023-04-06 17:33:03'),
(16,'27001:2013-A13','Sécurité des communications','2019-08-08 03:43:22','2023-04-06 17:33:15'),
(17,'27001:2013-A14','Acquisition, développement et maintenance','2019-08-08 03:43:50','2023-04-06 17:33:37'),
(18,'27001:2013-A15','Relation avec les fournisseurs','2019-08-08 03:44:05','2023-04-06 17:33:46'),
(19,'27001:2013-A16','Gestion des incidents de sécurité','2019-08-08 03:44:19','2023-04-06 17:34:15'),
(20,'27001:2013-A17','Gestion de la continuité d\'activité','2019-08-08 03:44:35','2023-04-06 17:34:23'),
(21,'27001:2013-A18','Conformité','2019-08-08 03:44:57','2023-04-06 17:34:31');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `measures`
--

LOCK TABLES `measures` WRITE;
/*!40000 ALTER TABLE `measures` DISABLE KEYS */;
INSERT INTO `measures` (`id`, `domain_id`, `name`, `clause`, `objective`, `input`, `model`, `indicator`, `action_plan`, `created_at`, `updated_at`, `standard`, `attributes`) VALUES 
(7,8,'Revue des Politiques de Sécurité','A.5.1.2','S’assurer que les politiques de sécurité sont revues à intervalles programmés ou en cas de changements majeurs pour garantir leur pertinence, leur adéquation et leur effectivité dans le temps.','• Périodicité de la revue de la politique de sécurité ;\r\n• Date de la dernière version validée de la politique de sécurité.','Nombre de politique de sécurité à jour par rapport nombre de politiques de sécurité.','Vert : 100%\r\nOrange : >= 90%\r\nRouge : < 90%','Lorsque le contrôle est orange ou rouge, il faut démarrer un processus de revue des politiques de sécurité.','2019-08-12 06:48:26','2021-07-12 05:09:01',NULL,NULL),
(8,7,'Audit Interne','9.2','S\'assurer qu\'un audit interne a été réalisé','La date du dernier audit interne\r\nLa date du contrôle','Comparer la date du contrôle avec la date du dernier audit interne','Vert : < 1 an\r\nOrange : => 1 an\r\nRouge : > 1 an et 3 mois','Effectuer l\'audit interne','2019-08-12 06:49:44','2021-03-08 05:57:57',NULL,NULL),
(9,7,'Revue de Direction','9.3','S’assurer que la revue de directions est tenue annuellement','• La date de la dernière revue de direction ;\r\n• La date du contrôle.','La date de la dernière revue de direction par rapport à la date du contrôle.','Vert : < 1 an\r\nOrange : => 1 an\r\nRouge : > 1 an et 3 mois','Planifer une revue de direction','2019-08-12 06:50:41','2022-03-08 12:27:09',NULL,NULL),
(10,7,'Non-conformité et actions correctives','10.1','S’assurer que les non-conformités et les actions correctives sont suivies régulièrement.','• Le plan de suivi des non-confrmités et d\'actions correctives\r\n• La date du contrôle','Compter le nombre de non-conformité qui dont le plan d\'action n\'a pas de suivi','Vert : = 0\r\nOrange : >=1\r\nRouge : >=3','Si le contrôle est orange ou rouge, il faut lancer le processus de suivi des non-conformités','2019-08-12 06:52:10','2023-02-05 10:42:03',NULL,NULL),
(12,8,'Politique de sécurité de l\'information','A.5.1.1','Un ensemble de politiques de sécurité de l\'information est défini, approuvé par la direction, diffusé et communiqué aux salariés et aux tiers concernés.','- ensemble de politique de sécurité de l\'information\r\n- preuves d\'approbation\r\n- preuves de diffusion','Existence de l\'ensemble des politiques de sécurité validé, diffusé et communiqué aux salariés et aux tiers concernés.','Vert: conforme\r\nOrange: manquements mineurs\r\nRouge: non-conforme','Faire valider l\'ensemble des politiques de sécurité de l\'information, le faire approuver par la direction, le diffuser et le communiquer aux salariés et aux tiers concernés.','2020-04-13 02:06:52','2021-01-19 07:20:08',NULL,NULL),
(13,9,'Fonctions et responsabilités liées à la sécurité de l’information','A.6.1.1','S\'assurer que toutes les responsabilités en matière de sécurité de l’information sont définies et attribuées.','- Politique d\'organisation de la sécurité\r\n- Preuve d\'attribution par la direction des responsabilités en matière de sécurité de l\'information','Présence des preuves','Vert : présence des preuves\r\nOrange : manquement mineur\r\nRouge : absence de preuves','Définir et attribuer toutes les responsabilités en matière de sécurité de l’information.','2020-04-13 02:32:22','2020-04-13 02:33:22',NULL,NULL),
(14,9,'Séparation des tâches','A.6.1.2','Les tâches et les domaines de responsabilité incompatibles doivent être cloisonnés pour limiter les possibilités de modification ou de mauvais usage, non autorisé(e) ou involontaire, des actifs de l’organisation.','- Définition des tâches et domaines de responsabilités incompatibles\r\n- Validation par la direction de l\'application de la mesure','Présence de la définition et validation par la direction de la mesure','Vert: présence des preuves\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Définir et faire valider par la direction les tâches et les domaines de responsabilité incompatibles, les cloisonner pour limiter les possibilités de modification ou de mauvais usage, non autorisé(e) ou involontaire, des actifs de l’organisation.','2020-04-13 03:05:05','2021-01-19 07:21:48',NULL,NULL),
(15,9,'Relations avec les  autorités','A.6.1.3','Des relations appropriées avec les autorités compétentes doivent être entretenues.','- procédure d\'organisation de la sécurité\r\n- inventaire des autorités compétentes\r\n- preuve de relation avec les autorités','La procédure et l\'inventaire sont à jour\r\nil existe des preuves de relation avec les autorités','Vert : présence des preuves\r\nOrange : non-conformité mineure\r\nRouge: absence de preuves','Des relations appropriées avec les autorités compétentes doivent être entretenues.','2020-04-13 03:10:32','2021-01-19 07:24:07',NULL,NULL),
(16,9,'Relations avec des groupes de travail spécialisés','A.6.1.4','Des relations appropriées avec des groupes d’intérêt, des forums spécialisés dans la sécurité et des associations professionnelles doivent être entretenues.','- procédure d\'organisation de la sécurité\r\n- inventaire des groupes de spécialistes\r\n- preuves de relation avec les groupes de spécialistes','La procédure et l\'inventaire des groupes de spécialistes sont à jour.\r\nIl existe des preuves de relation avec les groupes de spécialistes.','Vert : présence des preuves\r\nOrange : non-conformité mineure\r\nRouge: preuves manquantes','Revoir la procédure, mettre à jour l\'inventaire et entretenir des relations avec les groupes de spécialistes.','2020-04-13 03:15:12','2021-01-19 07:25:25',NULL,NULL),
(17,9,'La sécurité de l’information dans la gestion de projet','A.6.1.5','La sécurité de l’information est considérée dans la gestion de projet, quel que soit le type de projet concerné.','- procédure d\'organisation de la sécurité\r\n- preuves de la prise en considération de la sécurité de l\'information dans la gestion des projets','La procédure existe et est à et jour\r\nprésence de preuves de considération de la sécurité de l\'information','Vert : présence des preuves\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Revoir la procédure et prendre en considération la sécurité de l’information dans la gestion de projet, quel que soit le type de projet concerné.','2020-04-13 03:21:53','2021-01-19 07:25:33',NULL,NULL),
(18,9,'Politique en matière d’appareils mobiles','A.6.2.1','Une politique et des mesures de sécurité complémentaires doivent être adoptées pour gérer les risques découlant de l’utilisation des appareils mobiles.','- Politique et mesure de sécurité complémentaires\r\n- Preuves d\'application des mesures complémentaires','- Existence d\'une politique et de mesures complémentaires\r\n- preuves d\'application des mesures complémentaires','Vert : présence de preuves\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Définir ou mettre à jour une politique et des mesures de sécurité complémentaires, et la faire adopter adoptées pour gérer les risques découlant de l’utilisation des appareils mobiles.','2020-04-13 03:27:31','2021-07-12 10:33:19',NULL,NULL),
(19,9,'Télétravail','A.6.2.2','Une politique et des mesures de sécurité complémentaires doivent être mises en œuvre pour protéger les informations consultées, traitées ou stockées sur des sites de télétravail.','- Politique et mesures de sécurité complémentaires \r\n- preuves d\'application des mesures complémentaires','- Existance et mise à jour de la politique de sécurité\r\n- Existance de preuves d\'application des mesures','Vert : présence des preuves\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Définir et mettre en oeuvre une politique et des mesures de sécurité complémentaires pour protéger les informations consultées, traitées ou stockées sur des sites de télétravail.','2020-04-13 03:31:33','2021-01-19 07:26:49',NULL,NULL),
(20,7,'Amélioration continue','10.2','L’organisation doit continuellement améliorer la pertinence, l’adéquation et l’efficacité du système de management de la sécurité de l’information.','- plan d\'amélioration continue\r\n- date du contrôle','Un plan d\'amélioration continue existe et est suivi','Vert : presence de preuves\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Définir un plan d\'amélioration de la pertinence, l’adéquation et l’efficacité du système de management de la sécurité de l’information.','2020-04-13 04:15:30','2021-03-08 05:58:22',NULL,NULL),
(21,10,'Des vérifications doivent être effectuées sur tous les candidats à l’embauche','A.7.1.1','Des vérifications doivent être effectuées sur tous les candidats à l’embauche conformément aux lois, aux règlements et à l’éthique et être proportionnées aux exigences métier, à la classification des informations accessibles et aux risques identifiés.','- Procédure de sélection des candidats\r\n- Preuves d\'exécution du processus de sélection','- Existence d\'une procédure à jour\r\n- Existence de preuves d’exécution du processus de sélection','Vert : conforme\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Mettre à jour la procédure et effectuer des vérifications sur tous les candidats à l’embauche.','2020-04-13 04:41:20','2023-03-09 21:19:47',NULL,'#Confidentialité #Intégrité #Disponibilité'),
(22,10,'Termes et conditions d’embauche','A.7.1.2','Les accords contractuels entre les salariés et les sous-traitants doivent préciser leurs responsabilités et celles de l’organisation en matière de sécurité de l’information.','- définitions des termes\r\n- preuves d\'existence des termes dans les accords','Existence des termes et preuves de présence des termes les accords','Vert : présence de pruves\r\nOrange : Non-conformité mineure\r\nRouge : absence de pruves','Définir les responsabilités et celles de l’organisation en matière de sécurité de l’information dans les accords contractuels entre les salariés et les sous-traitants.','2020-04-13 04:52:01','2021-01-19 07:28:14',NULL,NULL),
(23,10,'Sensibilisation, apprentissage et formation à la sécurité de l’information','A.7.2.2','L’ensemble des salariés de l’organisation et, quand cela est pertinent, des sous-traitants, doit bénéficier d’une sensibilisation et de formations adaptées et recevoir régulièrement les mises à jour des politiques et procédures de l’organisation s’appliquant à leurs fonctions.','- plan de sensibilisation à la sécurité de l\'information\r\n- feuilles de présence','Existence d\'un plan de sensibilisation à la sécurité\r\nPreuve de présence de personnel à ces réunions','Vert : existence d\'un plan et présence du personnel\r\nOrange : non-conformité mineure\r\nRouge : Absence de sensibilisation','Définir un plan de sensibilisation et de formations adaptées','2020-04-13 04:57:19','2021-08-30 11:17:41',NULL,NULL),
(24,10,'Processus disciplinaire','A.7.2.3','Un processus disciplinaire formel et connu de tous doit exister pour prendre des mesures à l’encontre des salariés ayant enfreint les règles liées à la sécurité de l\'information.','- Processus disciplinaire\r\n- Communication du process','Existence du processus\r\nPreuve de communication du processus','Vert : existe\r\nOrange : non-conformité mineure\r\nRouge : absence de preuves','Définir un processus disciplinaire formel et le communiquer au personnel.','2020-04-13 05:01:31','2021-01-19 07:31:29',NULL,NULL),
(25,10,'Achèvement ou modification des responsabilités associées au contrat de travail','A.7.3.1','Les responsabilités et les missions liées à la sécurité de l’information qui restent valables à l’issue de la rupture, du terme ou de la modification du contrat de travail, doivent être définies, communiquées au salarié ou au sous-traitant, et appliquées.','- définition des termes\r\n- preuves d\'application','Existence des termes et des preuves d\'application','Vert : existe\r\nOrange : non-conformités mineures\r\nRouge : absence de preuves','Définir les responsabilités et les missions liées à la sécurité de l’information qui restent valables à l’issue de la rupture, du terme ou de la modification du contrat de travail.','2020-04-13 05:04:30','2021-01-19 07:32:25',NULL,NULL),
(26,11,'Inventaire des actifs','A.8.1.1','Les actifs associés à l’information et aux moyens de traitement de l’information doivent être identifiés et un inventaire de ces actifs doit être dressé et tenu à jour.','- procédure de mise à jour\r\n- inventaire des actifs','Vérifier la procédure est à jour\r\nPrendre un échantillon de l\'inventaire et vérifier qu\'il est conforme','Vert : conforme\r\nOrange : non-conformités mineures\r\nRouge : non-conforme','Revoir la procédure de gestion de l\'inventaire\r\nMettre à jour l\'inventaire des assets','2020-04-15 02:10:56','2020-04-15 02:10:56',NULL,NULL),
(27,11,'Propriété des actifs','A.8.1.2','Les actifs figurant à l’inventaire doivent être attribués à un propriétaire.','- propriétaire des assets\r\n- échantillon de l\'inventaire','Vérifier que les assets sont attribués aux bons propriétaires.','Vert : pas d\'erreur\r\nOrange : > 1 erreur\r\nRouge : > 3 erreurs','Effectuer une revue de l\'inventaire','2020-04-15 02:23:01','2021-01-19 07:32:54',NULL,NULL),
(28,11,'Utilisation correcte des actifs','A.8.1.3','Vérifier que des règles d’utilisation correcte de l’information, des actifs associés à l’information et des moyens de traitement de l’information sont identifiées, documentées et mises en œuvre.','Vérifier sur un échantillon de l\'inventaire, l\'application des règles d\'utilisation correcte de l’information, des actifs associés à l’information et des moyens de traitement','Compter le nombre d\'anomalies','Vert : aucune anomalie\r\nOrange : <= 3 anomalies\r\nRouge : > 3 anomalies','Revoir les règles d\'utilisation et les faire appliquer','2020-04-15 02:49:50','2021-01-19 07:34:12',NULL,NULL),
(29,11,'Restitution des actifs','A.8.1.4','Vérifier que tous les salariés et les utilisateurs tiers ont restitué la totalité des actifs de l’organisation qu’ils ont en leur possession au terme de leur période d’emploi, du contrat ou de l’accord.','- échantillon des employés ayant quitté l\'organisation sur la période\r\n- preuves de restitution des assets','compter le nombre d\'anomalies','Vert : aucune anomalie\r\nOrange : <= 3 anomalies\r\nRouge : > 3 anomalies','Revoir le processus de restitution des assets et refaire le contrôle','2020-04-15 03:02:14','2021-01-19 07:34:47',NULL,NULL),
(30,11,'Classification des informations','A.8.2.1','Vérifier que les informations sont classifiées en termes d’exigences légales, de valeur, de caractère critique et de sensibilité au regard d’une divulgation ou modification non autorisée.','- vérifier la date de mise à jour de la procédure\r\n- vérifier avec le DPO et juriste la conformité de la procédure au vu des changements du contexte de l\'organisation','compter le nombre d\'anomalies','Vert : pas d\'anomalies\r\nOrange > 1 anomalie\r\nRouge : > 3 anomalies','Revoir la procédure de classification de l\'information','2020-04-15 03:05:16','2021-02-01 07:56:58',NULL,NULL),
(31,11,'Marquage des informations','A.8.2.2','Vérifier qu\'un ensemble de procédures pour le marquage de l’information est élaboré et mis en œuvre conformément au plan de classification adopté par l’organisation.','- procédure de marquage\r\n- échantillon d’information (procédure, formulaire ...)','compter le nombre d\'anomalies de marquage de l\'information','Vert : pas d\'anomalie\r\nOrange : <=3 anomalies\r\nRouge : >3 anomalies','Revoir la procédure de marquage de l\'information et son application','2020-04-15 03:13:38','2021-01-19 07:35:52',NULL,NULL),
(32,11,'Gestion des supports  amovibles','A.8.3.1','Vérifier que des procédures de gestion des supports amovibles sont mises en œuvre conformément au plan de classification adopté par l’organisation.','- procédure de gestion des supports amovibles\r\n- échantillons de supports\r\n- preuves de gestion des supports (conservation, classification ...)','Compter le nombre d\'anomalies','Vert : pas d\'anomalies\r\nOrange : <= 3 anomalies\r\nRouge: > 3 anomalies','Revoir la procédure de gestion des supports amovibles et son application','2020-04-15 03:35:59','2021-01-19 07:36:33',NULL,NULL),
(33,11,'Mise au rebut des  supports','A.8.3.2','Vérifier que les supports qui ne sont plus nécessaires sont mis au rebut de manière sécurisée en suivant des procédures formelles.','- procédure de mise au rebut des supports\r\n- preuves de mise au rebut','Vérifier sur base d\'un échantillon des supports mis au rebut que ceux-ci ont été détruits de manière sécurisée.','Vert : présence des preuves\r\nOrange : non-conformités mineures\r\nRouge : absence de preuves, non-conformité','Vérifier la procédure de mise au rebut des supports et son application','2020-04-15 03:40:39','2021-01-19 07:37:39',NULL,NULL),
(34,11,'Transfert physique des supports','A.8.3.3','Vérifier que les supports contenant de l’information sont protégés contre les accès non autorisés, les erreurs d’utilisation et l’altération lors du transport.','Procédure de transport physique des supports\r\nEchantillon de transports effectués\r\nPreuve de transport','Compter le nombre d\'anomalies','Vert : pas d\'anomalies\r\nOrange <= 3 anomalies\r\nRouge : > 3 anomalies','Revoir la procédure de transport physique des supports et son application.','2020-04-15 03:52:23','2020-04-15 03:52:23',NULL,NULL),
(35,12,'Politique de contrôle d’accès','A.9.1.1','Une politique de contrôle d’accès est établie, documentée et revue sur base des exigences métier et de sécurité de l’information.','- Politique de contrôle d\'accès\r\n- Date de revue de la politique\r\n- Date du contrôle','Existence de la politique\r\nNombres de jours depuis la revue de la politique de contrôle d\'accès','Vert : =< 6 mois\r\nOrange : > 6 mois\r\nRouge : > 12 mois','Etablir, documenter et revoir la politique de contrôle d\'accès','2020-04-16 04:20:13','2021-08-30 11:16:48',NULL,NULL),
(36,12,'Accès aux réseaux et aux services réseau','A.9.1.2','Les utilisateurs doivent avoir uniquement accès au réseau et aux services réseau pour lesquels ils ont spécifiquement reçu une autorisation.','- inventaire des services réseau\r\n- liste des utilisateurs\r\n- liste des autorisations','Compter le nombre d\'anomalies.','Vert : pas d\'anomalies\r\nOrange <= 3 anomalies\r\nRouge : > 3 anomalies','Vérifier les accès au réseau et aux services réseau des utilisateurs.','2020-04-16 04:22:02','2022-03-08 12:41:53',NULL,NULL),
(37,12,'Enregistrement et désinscription des utilisateurs','A.9.2.1','Un processus formel d’enregistrement et de désinscription des utilisateurs est mis en œuvre pour permettre l’attribution des droits d’accès.','- processus d\'enregistrement et de désinscription des utilisateurs\r\n- date du contrôle','Le processus existe','Vert : oui\r\nRouge : non','Mettre en oeuvre un processus formel d’enregistrement et de désinscription des utilisateurs.','2020-04-16 04:58:24','2020-04-16 04:58:24',NULL,NULL),
(38,12,'Maîtrise de la gestion des accès utilisateur','A.9.2.2','Un processus formel de maîtrise de la gestion des accès aux utilisateurs est mis en œuvre pour attribuer et retirer des droits d’accès à tous types d’utilisateurs sur l’ensemble des services et des systèmes.','- Processus de maîtrise de la gestion des accès aux utilisateurs\r\n- Preuves d\'application du processus de maîtrise de la gestion des accès aux utilisateurs','Existance du processus de maîtrise de la gestion des accès aux utilisateurs\r\nValidité des preuves d\'application du processus','Vert : conforme\r\nOrange :  preuves faibles\r\nRouge : processus inexistant ou pas à jour','Mettre en oeuvre un processus formel de maîtrise de la gestion des accès aux utilisateurs','2020-04-16 05:00:45','2021-08-02 05:53:52',NULL,NULL),
(39,12,'Gestion des droits d’accès à privilèges','A.9.2.3','L’allocation et l’utilisation des droits d’accès à privilèges sont restreintes et contrôlées.','- restrictions des droits d\'accès à privilèges\r\n- contrôle des droits d\'accès à privilèges','- existence des restrictions de droits d\'accès à privilège\r\n- contrôle des doits d\'accès à privilèges','Vert : conforme\r\nOrange: non conformités mineures\r\nRouge: non conforme','Restreindre et contrôler l’allocation et l’utilisation des droits d’accès à privilèges','2020-04-16 05:04:48','2022-03-08 12:42:13',NULL,NULL),
(40,12,'Gestion des informations secrètes d’authentification des utilisateurs','A.9.2.4','L’attribution des informations secrètes d’authentification doit être réalisée dans le cadre d’un processus de gestion formel.','- processus de gestion de l\'attribution des informations secrètes d\'authentification','Vérifier l\'existance et l\'application du processus de gestion de l\'attribution des informations secrètes d\'authentification','Vert : conforme\r\nOrange : non-conformités mineures\r\nRouge : non conforme','Mettre en place un processus de gestion formel de l’attribution des informations secrètes d’authentification.','2020-04-16 05:07:56','2020-04-16 05:07:56',NULL,NULL),
(41,12,'Revue des droits d’accès utilisateur','A.9.2.5','Les propriétaires d’actifs vérifient les droits d’accès des utilisateurs à intervalles réguliers.','- inventaire des actifs concernés\r\n- vérification des droits d\'accès des utilisateurs\r\n- date de la vérification\r\n- date du contrôle','- existence de l\'inventaire\r\n- existence de la vérification\r\n- différence entre la date du contrôle et la date de la vérification','Vert : conforme\r\nOrange : non-conformités mineures\r\nRouge :  non-conforme','Faire vérifier les droits d’accès des utilisateurs par les propriétaires des actifs.','2020-04-16 05:20:30','2021-08-30 11:15:27',NULL,NULL),
(42,12,'Suppression ou adaptation des droits d’accès','A.9.2.6','Les droits d’accès aux informations et aux moyens de traitement des informations de l’ensemble des salariés et utilisateurs tiers doivent être supprimés à la fin de leur période d’emploi, ou adaptés en cas de modification du contrat ou de l’accord.','- liste des salariés et utilisateurs tiers ayant changé de poste ou ayant quitté l\'organisation\r\n- suppression ou modification des droits d\'accès correspondants','compter le nombre d\'anomalies',NULL,'Revoir les droits d\'accès de l\'ensemble des salariés et utilisateurs tiers','2020-04-16 05:22:42','2022-03-08 12:43:00',NULL,NULL),
(43,12,'Utilisation d’informations secrètes d’authentification','A.9.3.1','Les utilisateurs suivent les pratiques de l’organisation pour l’utilisation des informations secrètes d’authentification.','- pratiques de l’organisation pour l’utilisation des informations secrètes d’authentification.\r\n- application des pratiques','- vérification de l\'application des pratiques de l’organisation pour l’utilisation des informations secrètes d’authentification.','Vert : conforme\r\nOrange : non-conformité mineure\r\nRouge : non-conforme','Sensibiliser les utilisateurs aux pratiques de l’organisation pour l’utilisation des informations secrètes d’authentification.','2020-04-16 05:51:24','2021-01-19 07:40:00',NULL,NULL),
(44,12,'Restriction d’accès à l’information','A.9.4.1','L’accès à l’information et aux fonctions d’application système est restreint conformément à la politique de contrôle d’accès.','- restrictions d\'accès à l\'information \r\n- application de ces restrictions','compter le nombre de différences','Vert : conforme\r\nOrange : conconformité mineure\r\nRouge : non-conforme','Revoir les restrictions d\'accès à l\'information et leur application','2020-04-17 01:50:04','2021-08-30 11:16:22',NULL,NULL),
(45,12,'Sécuriser les procédures de connexion','A.9.4.2','Lorsque la politique de contrôle d’accès l’exige, l’accès aux systèmes et aux applications est contrôlé par une procédure de connexion sécurisée.','- procédures de connexion sécurisée\r\n- application de la procédure de connexion','Vérifier l\'application des procédures de connexion sécurisée','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Lorsque la politique de contrôle d’accès l’exige, contrôler par une procédure de connexion sécurisée l\'accès aux systèmes et aux applications.','2020-04-17 01:54:56','2020-04-17 01:54:56',NULL,NULL),
(46,12,'Système de gestion des mots de passe','A.9.4.3','Les systèmes qui gèrent les mots de passe doivent être interactifs et doivent garantir la qualité des mots de passe.','- inventaire des systèmes qui gèrent des mots  de passe\r\n- qualité des mots de passe\r\n- application de l\'objectif','vérifier que l\'objectif est atteint','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Faire l\'inventaire des systèmes qui gèrent les mots de passe, les configurer pour qu\'ils soient interactifs et qu\'ils garantissent la qualité des mots de passe.','2020-04-17 02:00:31','2021-08-30 11:16:06',NULL,NULL),
(47,12,'Utilisation de programmes utilitaires à privilèges','A.9.4.4','L’utilisation des programmes utilitaires permettant de contourner les mesures de sécurité d’un système ou d’une application doit être limitée et étroitement contrôlée.','- inventaire des programmes utilitaires\r\n- contrôles mis en place','Vérifier l\'application des contrôles sur l\'utilisation des programmes utilitaires','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Faire l\'inventaire des programmes utilitaires permettant de contourner les mesures de sécurité d’un système ou d’une application, limiter et étroitement contrôler leur utilisation.','2020-04-17 02:07:47','2020-04-17 02:07:47',NULL,NULL),
(48,12,'Contrôle d’accès au code source des programmes','A.9.4.5','L’accès au code source des programmes est restreint.','- inventaire du code source des programmes\r\n- restriction d\'accès au code source','Vérifier l\'effectivité de la restriction de l\'accès au code source des programmes.','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Restreindre l’accès au code source des programmes.','2020-04-17 02:10:43','2020-04-17 02:10:43',NULL,NULL),
(49,13,'Politique d’utilisation des mesures cryptographiques','A.10.1.1','Une politique d’utilisation des mesures cryptographiques en vue de protéger l’information est élaborée et mise en œuvre.','- Politique d\'utilisation des mesures de cryptographiques\r\n- Preuves d\'élaboration et de mise en œuvre de la politique','Compter le nombre non conformités','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Elaborer et mettre en œuvre une politique d’utilisation des mesures cryptographiques en vue de protéger l’information.','2020-04-17 02:21:00','2021-01-19 05:51:38',NULL,NULL),
(50,13,'Gestion des clés','A.10.1.2','Une politique sur l’utilisation, la protection et la durée de vie des clés cryptographiques est élaborée et mise en œuvre tout au long de leur cycle de vie.','- Politique sur l’utilisation, la protection et la durée de vie des clés cryptographiques\r\n- Preuve de mise en œuvre de politique','Vérifier l\'application de la politique','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Elaborer et mettre en œuvre une politique sur l’utilisation, la protection et la durée de vie des clés cryptographiques.','2020-04-17 02:24:15','2021-01-19 05:52:04',NULL,NULL),
(51,14,'Périmètre de sécurité physique','A.11.1.1','Des périmètres de sécurité sont définis et utilisés pour protéger les zones contenant l’information sensible ou critique et les moyens de traitement de l’information.','- définition des périmètres de sécurité\r\n- inventaire des zones contenant de l\'information sensible','Compter le nombre d\'anomalies','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Définir des périmètres de sécurité pour protéger les zones contenant l’information sensible ou critique et les moyens de traitement de l’information.','2020-04-17 02:50:43','2020-04-17 02:51:19',NULL,NULL),
(52,14,'Contrôle d’accès physique','A.11.1.2','Les zones sécurisées sont protégées par des contrôles adéquats à l’entrée pour s’assurer que seul le personnel autorisé est admis.','- inventaire des zones sécurisées\r\n- contrôles adéquats à l\'entrée\r\n- application des contrôles','Vérification l\'application des contrôles. \r\nCompter le nombre d\'anomalies.','Vert : conforme\r\nOrange : anomalie\r\nRouge : non-conforme','Mettre en place des contrôles adéquats à l’entrée des zones sécurisées pour s’assurer que seul le personnel autorisé est admis.','2020-04-17 03:05:14','2021-01-19 05:53:11',NULL,NULL),
(53,14,'Sécurisation des bureaux, des salles et des équipements','A.11.1.3','Des mesures de sécurité physique aux bureaux, aux salles et aux équipements sont conçues et appliquées.','- inventaire des bureaux, salles et équipements\r\n- mesures de sécurité physique\r\n- application des mesures','Vérifier l\'inventaire, les mesures de sécurité et l\'application des mesures.','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Concevoir et appliquer des mesures de sécurité physique aux bureaux, aux salles et aux équipements.','2020-04-17 03:14:06','2020-04-17 03:14:06',NULL,NULL),
(54,14,'Protection contre les menaces extérieures et environnementales','A.11.1.4','Des mesures de protection physique contre les désastres naturels, les attaques malveillantes ou les accidents sont conçues et appliquées.','- inventaire des désastres naturels attaques malveillantes ou les accidents pris en compte\r\n- mesures de protection physique\r\n- application de ces mesures','Vérifier l\'inventaire et l\'application des mesures. Compter le nombre d\'anomalies.','Vert : conforme\r\nOrange : anomalie\r\nRouge : non-conforme','Concevoir et appliquer des mesures de protection physique contre les désastres naturels, les attaques malveillantes ou les accidents.','2020-04-17 03:17:18','2021-01-19 06:06:30',NULL,NULL),
(55,14,'Travail dans les zones  sécurisées','A.11.1.5','Des procédures pour le travail dans les zones sécurisées sont conçues et appliquées.','- procédure pour le travail dans les zones sécurisées \r\n- zones sécurisées\r\n- application de la procédure',NULL,'-','Concevoir et appliquer des procédures pour le travail dans les zones sécurisées.','2020-04-17 03:20:05','2020-10-05 06:06:53',NULL,NULL),
(56,14,'Zones de livraison et de chargement','A.11.1.6','Les points d’accès tels que les zones de livraison et de chargement et les autres points par lesquels des personnes non autorisées peuvent pénétrer dans les locaux sont contrôlés et isolés des moyens de traitement de l’information, de façon à éviter les accès non autorisés.','- inventaire des zones de livraison et de chargement\r\n- mesures de protection et d\'isolement\r\n- application des mesures','Vérifier l\'application des mesures d\'isolement des zones de livraison et de chargement.','Vert : conforme\r\nOrange : anomalies\r\nRouge: non-conforme','Isoler les points d’accès tels que les zones de livraison et de chargement et les autres points par lesquels des personnes non autorisées peuvent pénétrer dans les locaux.','2020-04-17 03:42:29','2021-01-19 06:07:00',NULL,NULL),
(57,14,'Emplacement et protection des matériels','A.11.2.1','Les matériels sont localisés et protégés de manière à réduire les risques liés à des menaces et des dangers environnementaux et les possibilités d’accès non autorisé.','- inventaire du matériel concerné\r\n- menaces et dangers environnementaux retenus\r\n- emplacements\r\n- mesures mises en œuvre','Vérifier l\'inventaire du matériel concerné et leurs emplacements.\r\nVérifier l\'application des mesures de protection.','-','Protéger les matériels contre les risques liés à des menaces et des dangers environnementaux et les possibilités d’accès non autorisé.','2020-04-17 04:10:43','2021-01-19 06:07:47',NULL,NULL),
(58,14,'Services généraux','A.11.2.2','Les matériels sont protégés des coupures de courant et autres perturbations dues à une défaillance des services généraux.','- inventaire du matériel concerné\r\n- mesures de protection','Vérifier l\'inventaire du matériel et l\'application des mesures de protection.','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Protéger les matériels des coupures de courant et autres perturbations dues à une défaillance des services généraux.','2020-04-17 04:15:13','2020-04-17 04:15:13',NULL,NULL),
(59,14,'Sécurité du câblage','A.11.2.3','Les câbles électriques ou de télécommunication transportant des données\r\nou supportant les services d’information sont protégés contre toute interception ou tout dommage.','- inventaire du câblage concerné \r\n- mesures de protection contre les interceptions et les dommages','Vérifier l\'inventaire du câblage et l\'application des mesures','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Protégés contre toute interception ou tout dommage les câbles électriques ou de télécommunication transportant des données ou supportant les services d’information.','2020-04-17 05:03:40','2022-03-08 12:30:15',NULL,NULL),
(60,14,'Maintenance des matériels','A.11.2.4','Les matériels sont entretenus correctement pour garantir leur disponibilité permanente et leur intégrité.','- inventaire du matériel concerné\r\n- mesures d’entretiens\r\n- preuves d\'application des mesures d\'entretien','Vérifier l\'inventaire du matériel concerné, les mesures d\'entretien et l\'application des mesures','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','Entretenir correctement les matériels pour garantir leur disponibilité permanente et leur intégrité.','2020-04-17 05:08:27','2021-01-19 06:08:42',NULL,NULL),
(61,14,'Sortie des actifs','A.11.2.5','Les matériels, les informations ou les logiciels des locaux de l’organisation ne doivent pas sortir sans autorisation préalable.','- matériel, information ou logiciels dont la sortie est soumis à autorisation préalable\r\n- preuves de demandes d\'autorisation','Vérifier l\'inventaire du matériel.\r\nVérifier les preuves d\'autorisation de sortie du matériel.','Vert : conforme\r\nOrange : anomalie\r\nRouge : non-conforme','Mettre en place et faire appliquer une procédure de sortie des matériels, informations et/ou logiciels des locaux de l’organisation.','2020-04-17 06:00:33','2022-03-08 12:30:54',NULL,NULL),
(62,14,'Sécurité des matériels et des actifs hors des locaux','A.11.2.6','Des mesures de sécurité sont appliquées aux matériels utilisés hors des locaux de l’organisation en tenant compte des différents risques associés au travail hors site.','- mesures de sécurité appliquées\r\n- preuves d\'application des mesures de sécurité','Vérifier l\'application des mesures de sécurité','Vert : conforme\r\nOrange : non-conformité mineure\r\nRouge : non-conforme','Appliquer des mesures de sécurité aux matériels utilisés hors des locaux de l’organisation en tenant compte des différents risques associés au travail hors site.','2020-04-21 02:36:52','2020-04-21 02:36:52',NULL,NULL),
(63,14,'Mise au rebut ou recyclage sécurisé(e) des matériels','A.11.2.7','Tous les composants des matériels contenant des supports de stockage sont vérifiés pour s’assurer que toute donnée sensible a bien été supprimée et que tout logiciel sous licence a bien été désinstallé ou écrasé de façon sécurisée, avant leur mise au rebut ou leur réutilisation.','- inventaire du matériel contenant des données sensibles\r\n- preuves d\'effacement des données','Vérifier que l\'inventaire est à jour.\r\nVérifier la présence des preuves d\'effacement des données','Vert : conforme\r\nOrange : anomalies\r\nRouge : non-conforme','S’assurer que toute donnée sensible a bien été supprimée et que tout logiciel sous licence a bien été désinstallé ou écrasé de façon sécurisée  du matériel contenant des données sensibles avant leur mise au rebut ou leur réutilisation.','2020-04-21 02:44:40','2021-01-28 08:39:33',NULL,NULL);
/*!40000 ALTER TABLE `measures` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-06 21:43:21