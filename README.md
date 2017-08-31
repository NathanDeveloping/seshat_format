#Seshat Format

Script permettant le formattage des données résultantes du carnet de terrain électronique [Seshat](https://github.com/arnouldpy/seshat).
Le script interroge la base de données PostgreSQL pour récupérer les données puis les met en page au sein
d'un fichier tabulaire Excel (.xlsx), ceci permettant l'édition ultérieure des données.

La base de données MongoDB de MOBISED est nécessaire pour l'auto-completion des formulaires (email des operateurs
et informations sur les stations.) 
## Installation

pour installer le conteneur seshat_format :

`git clone https://github.com/arnouldpy/seshat_format.git`

aller dans le repertoire : seshat-format

entrer la commande : `docker build .`

recuperer l'id de l'image avec la commande : docker image ls

executer : `docker run -i -t id_image /bin/bash`

quitter le conteneur `ctrl+d`

recuperer l'id du conteneur avec la commande : `docker ps -a`

demarrer le conteneur avec la commande : `docker start id_conteneur`

## Configuration

Pour configurer les différents paramètres, il faut créer le fichier *src/config/config.ini* et spécifier
les champs suivants :

````
host= ## adresse du serveur executant la BDD
port= ## port du serveur
dbname= ## nom de la BDD : à initialiser à postgres
user= ## nom d'utilisateur pour se connecter à la BDD
password=

mongo_host= # adresse serveur mongoDB de MOBISED
mongo_port= # port
mongo_user= # nom d'utilisateur avec lequel se connecter 
mongo_password= # mot de passe
mongo_dbname=MOBISED # base de donnée

logsDir=../logs/ ## dossier contenant les fichiers logs
destinationFolder=../generated_form/ ## dossier destination des formulaires formattés
````

## Lancement du script

Après avoir préalablement configuré (section précédente), rien de plus simple.

depuis la racine du dépot :

```
php src/format.php
```
