# api_rest_musique

## Installer composer pour ajouter les dépendances
```
composer install
```

### Configurer connexion bdd
```
Modifier la ligne DATABASE_URL du fichier .env et ajouter vos propres paramétres de connexion
```


### Pour créer la bdd
```
php bin/console doctrine:database:create
```

### Mettre à jour la bdd
```
php bin/console doctrine:migrations:migrate
```

### Démarrer le serveur
```
php -S localhost:8000 -t public -d upload_max_filesize=128M
```

[Lien vers la vue](https://github.com/c-noblet/musik)
