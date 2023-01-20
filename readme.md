# Wikima

Un projet de wiki développé en symfony pour gérer du contenu textuel et des images.

## Environnement de développement
### Prérequis
* Docker
* Docker Compose

### Installation
```bash
docker compose up -d
```

### Initialisation du projet en environnement de dev
Dans le container de l'appli
```bash
sh deploy.sh
```

Création d'un compte administrateur
```bash
php bin/console app:create-user
```

## Consommer les messages avec Messenger de Symfony
```bash
$ php bin/console messenger:consume async
```

Pour voir les détails :
```bash
$ php bin/console messenger:consume async -vv
```