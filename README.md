# Application Météo

Une application web simple pour consulter la météo actuelle et gérer vos emplacements favoris. Cette application est multilingue (français et anglais) et utilise l'API d'OpenStreetMap pour la géolocalisation et Open-Meteo pour les données météorologiques.

## Fonctionnalités

- Recherche de la météo par localisation
- Affichage des conditions météorologiques actuelles
- Gestion des emplacements favoris
- Support multilingue (français et anglais)
- Mise en cache des données météo pour réduire les appels API
- Interface responsive et accessible

## Prérequis

- PHP 7.4 ou supérieur
- Serveur web (Apache, Nginx, etc.)
- SQLite3 ou autre base de données compatible
- Extension PHP PDO

## Installation

1. Clonez le dépôt ou téléchargez les fichiers source
   ```
   git clone https://github.com/votre-username/meteo-app.git
   ```

2. Placez les fichiers dans le répertoire de votre serveur web

3. Assurez-vous que le dossier `cache` est accessible en écriture
   ```
   chmod 755 cache
   ```

4. Créez ou vérifiez que la base de données SQLite est accessible en écriture
   ```
   chmod 755 db/weather.db
   ```

5. Accédez à l'application via votre navigateur
   ```
   http://localhost/meteo-app
   ```

## Structure du projet

```
meteo-app/
├── css/
│   └── style.css
├── db/
│   ├── Database.php
│   ├── WeatherLocation.php
│   └── weather.db
├── js/
│   └── script.js
├── lang/
│   ├── fr.php
│   └── en.php
├── cache/
├── index.php
├── search.php
├── favorites.php
└── README.md
```

## Utilisation

### Recherche de météo

1. Saisissez un nom de ville ou d'emplacement dans la barre de recherche
2. Cliquez sur le bouton "Rechercher" ou appuyez sur Entrée
3. Les données météorologiques actuelles s'afficheront

### Gestion des favoris

- **Ajouter un favori**: Entrez le nom d'un emplacement dans le formulaire d'ajout de favoris, ajoutez éventuellement des notes, puis cliquez sur "Ajouter"
- **Consulter un favori**: Cliquez sur le bouton "Voir" à côté d'un emplacement favori pour afficher sa météo
- **Supprimer un favori**: Cliquez sur le bouton "Supprimer" à côté d'un emplacement favori

### Changer de langue

- Cliquez sur "Français" ou "English" dans l'en-tête pour basculer entre les langues

## APIs utilisées

- **Nominatim (OpenStreetMap)**: Pour convertir les noms d'emplacements en coordonnées géographiques
- **Open-Meteo**: Pour obtenir les données météorologiques actuelles basées sur les coordonnées

## Sécurité

L'application implémente plusieurs mesures de sécurité :
- Protection CSRF pour les formulaires
- Filtrage et assainissement des entrées utilisateur
- Gestion des sessions sécurisée
- Échappement des données affichées pour prévenir les attaques XSS

## Développement

### Ajouter une nouvelle langue

1. Dupliquez un des fichiers de langue existants (fr.php ou en.php) dans le dossier `lang/`
2. Renommez-le selon le code de langue ISO (ex: `es.php` pour l'espagnol)
3. Traduisez toutes les chaînes de caractères
4. Ajoutez la nouvelle langue à la liste des langues supportées dans `index.php`

### Personnaliser l'interface

Modifiez le fichier `css/style.css` pour personnaliser l'apparence de l'application.

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## Auteur

Votre Nom - [votre-email@example.com](adamkilanix@gmail.com)

## Remerciements

- [OpenStreetMap](https://www.openstreetmap.org/) pour l'API de géocodage
- [Open-Meteo](https://open-meteo.com/) pour l'API météo gratuite
