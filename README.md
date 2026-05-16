# WebMobUi53 — Application de sondage

Application de sondage fullstack développée dans le cadre du cours WebMobUI à la
[Haute Ecole d'Ingénierie et de Gestion du Canton de Vaud (HEIG-VD)](https://heig-vd.ch),
Suisse. Permet de créer des sondages, de les partager via un lien token, de voter
et de consulter les résultats en temps réel.

## Pré-requis

Afin de lancer ce projet, une stack compatible avec Laravel est requise.

Voici les pré-requis nécessaires :

- PHP >= 8.0.
- Composer.
- Node.js et npm.
- Une base de données (MySQL, PostgreSQL, SQLite, etc.).
- Un serveur web (Apache, Nginx, etc.).

[Laravel Herd](https://herd.laravel.com/) est recommandé pour une installation facile de Laravel et de ses dépendances.

## Développement local

Pour développer et tester le projet en local, voici les étapes à suivre :

1. Forker ce dépôt.

2. Installer les dépendances :

    ```bash
    npm install
    composer install
    ```

3. Copier le fichier `.env.example` en `.env`.
4. Modifier les variables d'environnement si nécessaire (optionnel).
5. Générer la clé d'application Laravel :

    ```bash
    php artisan key:generate
    ```

6. Créer la base de données et exécuter les migrations avec les seeders :

    ```bash
    php artisan migrate --seed
    ```

    S'il est nécessaire de réinitialiser la base de données, utiliser la commande `php artisan migrate:fresh --seed`.

7. Démarrer les serveurs de développement :

    ```bash
    # Terminal 1 — backend
    php artisan serve

    # Terminal 2 — frontend
    npm run dev
    ```

L'application sera accessible à l'adresse <http://localhost:8000>.

## Compte de test

| Champ | Valeur |
|---|---|
| Email | john.doe@example.com |
| Mot de passe | password |

## Fonctionnalités

- Dashboard des sondages de l'utilisateur connecté
- Création, édition et suppression d'un sondage
- Gestion des options de réponse (ajout, modification, suppression)
- Paramètres : brouillon, lancement immédiat, choix multiple, résultats publics, durée
- Lien de partage via token (bouton "Copier le lien" dans le dashboard)
- Page de vote accessible via token dans l'URL
- Vote authentifié avec unicité garantie (frontend + backend)
- Accès conditionnel : résultats visibles si publics, message de connexion pour les non-authentifiés
- Résultats en temps réel via polling toutes les 5 secondes
- Graphique en barres (Chart.js) des résultats

## Architecture et choix techniques

- **Backend** : Laravel 12, SQLite, Laravel Sanctum (session-based)
- **Frontend** : Vue.js 3.4 (Composition API), Vite, Chart.js
- **Store** : Pinia via `usePollStore` — état global des sondages
- **Composables** : `useFetchApi` (requêtes HTTP), `usePolling` (polling toutes les 5s)
- **Intégration Blade → Vue** : données passées via `data-*` attributes et `JSON.parse`
- **GitHub Flow** : une branche par issue, Pull Request vers `main`
