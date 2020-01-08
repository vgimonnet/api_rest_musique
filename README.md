# api_rest_musique

## Installer composer pour ajouter les dépendances
```
composer install
```

### Pour créer la bdd
```
php bin/console doctrine:database:create
```

### Mettre à jour la bdd
```
php bin/console doctrine:migrations:migrate
```