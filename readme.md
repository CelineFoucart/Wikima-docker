# Wikima

Wikima est un projet développé en symfony pour organiser des informations sur un univers fictif (de type romanesque, jeu de rôle, série). Il permet de gérer du contenu textuel sous forme de Wiki, des images, mais aussi une galerie, une chronologie et des fiches personnages. On peut créer des pages personnalisées et laisser des commentaires sur les articles.

## Environnement de développement

### Prérequis

* Docker
* Docker Compose

### Installation

```bash
docker compose up -d
```

### Initialisation du projet en environnement de dev

Dans le container de l'appli, installer les dépendances et les migrations :

```bash
composer install
npm install
npm run build
```

Se rendre sur la page d'installation à l'url suivante : **/installation**. Suivre le processus d'installation pour configurer
la base de données, le compte du super administrateur, les informations du site et le système d'envoi de mail.

### Données de test

Installation de données de test :

```bash
php bin/console hautelook:fixtures:load
```

## Licence

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'information.