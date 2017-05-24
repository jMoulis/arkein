arkein
======
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2fb40d2f-3560-40e7-9958-d51eec9763b0/mini.png)](https://insight.sensiolabs.com/projects/2fb40d2f-3560-40e7-9958-d51eec9763b0)

#Gestion de centre d'accueil version "rayon gamma"

L'idée globale a été de fonctionnée en 'api', dans le sens où la pluspart des controller renvoi du json... Je trouve ça plus sympa pour travailler une meilleur UX.
Ce qui signifie que le dossier web/js/api renferme toutes les fonctionalités.
J'ai essayé tant bien que mal, de respecter les sensio best practices.


J'ai créé un bundle principal avec le plus part des fonctionalités.
Mais j'ai également crée trois autres bundle, notamment:
- Documentation - l'idée est d'éventuellement de récupérer ce code pour m'amuser à faire un gestionnaire de doc à mes heures perdues
- Blog - un peu la même idée que le premier
- Et user... par habitude

##Les fonctionnalités principales de cette application:
1. Gestion des membres:
- Dans le bundle User
- J'ai choisi Guard pour la sécurité. FOSUSer était surdimensionné et puis marre de me retaper tous les templates

2. Gestionnaires de document
- Ajout/Suppression et modification de dossier avec sortable (jquery)
3. Gestionnaire de rendez-vous/entretien/compte-rendu
- Prise de rdv, confirmation, modification, création de compte rendu et visualisation des compte-rendus en pdf
4. Gestionnaire de ticket
- Création de ticket/Suivi/Priorisation
5. En standBy par l'asso, la gestion d'évènements et blog

Dans un premier temps il  aété décidé par l'asso de faire design la partie site web public access, je me suis donc occupé que de la partie dashboard
##Framework/Library
+ Bootstrap 4
+ Jquery
+ Underscore pour les templates js

##Commentaires
+ La page d'accueil dashboard n'est pas encore définie en terme de données à diffuser
+ Théoriquement le workflow mail fonctionne
+ Les données sont filtrées en fonction des user connectés et des rôles et groupes

