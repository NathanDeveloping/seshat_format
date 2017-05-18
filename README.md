#Seshat Format

Script permettant le formattage des données résultantes du carnet de terrain électronique [Seshat](https://github.com/arnouldpy/seshat).
Le script interroge la base de données PostgreSQL pour récupérer les données puis les met en page au sein
d'un fichier tabulaire Excel (.xlsx), ceci permettant l'édition ultérieure des données.

## Configuration

Pour configurer les différents paramètres, il faut créer le fichier *src/config/config.ini* et spécifier
les champs suivants :

````
host= ## adresse du serveur executant la BDD
port= ## port du serveur
dbname= ## nom de la BDD : à initialiser à postgres
user= ## nom d'utilisateur pour se connecter à la BDD
password=

mongo_host=
mongo_port=
mongo_user=
mongo_password=
mongo_dbname=MOBISED

logsDir=../logs/ ## dossier contenant les fichiers logs
destinationFolder=../generated_form/ ## dossier destination des formulaires formattés
````

## Lancement du script

Après avoir préalablement configuré (section précédente), rien de plus simple.

depuis la racine du dépot :

```
php src/format.php
```
