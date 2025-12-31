# ADS API - Plateforme de Petites Annonces

API RESTful complète pour une plateforme de petites annonces avec authentification JWT, gestion des catégories, annonces, commentaires et commandes.

## Table des matières

- [Technologies](#technologies)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Architecture](#architecture)
- [Base de données](#base-de-données)
- [Endpoints API](#endpoints-api)
- [Exemples de requêtes](#exemples-de-requêtes)
- [Tests avec Postman](#tests-avec-postman)
- [Authentification](#authentification)

---

## Technologies

- **Laravel** 12.43.1
- **PHP** 8.2.0
- **MySQL**
- **JWT Auth** (tymon/jwt-auth) pour l'authentification
- **Eloquent ORM** pour la base de données

---

## Prérequis

- PHP >= 8.2
- Composer
- MySQL >= 5.7
---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/votre-repo/ads-api.git
cd ads-api
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurer la base de données

Éditez le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ads_api
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Créer la base de données

```sql
CREATE DATABASE ads_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Exécuter les migrations

```bash
php artisan migrate
```

**Ordre des migrations** :
1. `create_users_table` - Table des utilisateurs
2. `create_categories_table` - Table des catégories
3. `create_ads_table` - Table des annonces (dépend de users et categories)
4. `create_comments_table` - Table des commentaires (dépend de users et ads)
5. `create_orders_table` - Table des commandes (dépend de users et ads)

**Note importante** : L'ordre des migrations est crucial en raison des clés étrangères. Assurez-vous que les migrations sont nommées correctement pour respecter l'ordre de dépendance.

### 7. Générer la clé secrète JWT

```bash
php artisan jwt:secret
```

### 8. Lancer le serveur

```bash
php artisan serve
```

L'API est maintenant accessible sur `http://127.0.0.1:8000`

---

## Configuration

### JWT Configuration

Dans `config/auth.php` :

```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

### CORS (optionnel)

Si vous utilisez un frontend séparé, configurez CORS dans `config/cors.php` :

```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

---

## Architecture

### Structure du projet

```
ADS-API/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdController.php
│   │   │   ├── AuthController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CommentController.php
│   │   │   ├── Controller.php
│   │   │   ├── OrderController.php
│   │   │   └── SearchController.php
│   │   ├── Resources/
│   │   │   ├── AdResource.php
│   │   │   ├── CategoryResource.php
│   │   │   ├── CommentResource.php
│   │   │   ├── ErrorResource.php
│   │   │   ├── OrderResource.php
│   │   │   └── UserResource.php
│   │   └── Policies/
│   │       ├── AdPolicy.php
│   │       ├── CommentPolicy.php
│   │       └── OrderPolicy.php
│   ├── Models/
│   │   ├── Ad.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── Order.php
│   │   └── User.php
│   ├── Services/
│   │   ├── AdService.php
│   │   ├── AuthService.php
│   │   ├── CategoryService.php
│   │   ├── CommentService.php
│   │   ├── OrderService.php
│   │   └── SearchService.php
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 2024_12_27_000001_create_categories_table.php
│       ├── 2024_12_27_000002_create_ads_table.php
│       ├── 2024_12_27_000003_create_comments_table.php
│       └── 2024_12_27_000004_create_orders_table.php
├── public/
├── resources/
├── routes/
│   └── api.php
├── .env
├── .env.example
├── composer.json
└── README.md
```

### Pattern Architecture

**Service Pattern** : La logique métier est séparée dans des services

```
Request → Controller → Service → Model → Database
                ↓
            Response
```

**Avantages** :
- Code réutilisable
- Testabilité
- Séparation des responsabilités
- Maintenance facilitée

---

## Base de données
Consulter le fichier complet [ads_api.sql](./ads_api.sql).

### Schéma relationnel

#### Table: users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Table: categories
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Table: ads
```sql
CREATE TABLE ads (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

#### Table: comments
```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    content TEXT NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    ad_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE
);
```

#### Table: orders
```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ad_id BIGINT UNSIGNED NOT NULL,
    buyer_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(10,0) NOT NULL,
    status ENUM('PENDING', 'CONFIRMED', 'CANCELLED') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Tables auxiliaires
```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX (user_id),
    INDEX (last_activity)
);
```

### Relations

#### User (1 → N)
- **User** `1 → N` **Ad** : Un utilisateur peut créer plusieurs annonces
- **User** `1 → N` **Comment** : Un utilisateur peut créer plusieurs commentaires
- **User** `1 → N` **Order** (as buyer) : Un utilisateur peut passer plusieurs commandes
- **User** `1 → N` **Order** (as seller) : Un utilisateur peut recevoir plusieurs commandes

#### Category (1 → N)
- **Category** `1 → N` **Ad** : Une catégorie contient plusieurs annonces

#### Ad (1 → N)
- **Ad** `1 → N` **Comment** : Une annonce peut avoir plusieurs commentaires
- **Ad** `1 → N` **Order** : Une annonce peut avoir plusieurs commandes

#### Contraintes de clés étrangères
- **CASCADE ON DELETE** : Suppression en cascade pour maintenir l'intégrité
  - Si un utilisateur est supprimé → ses annonces, commentaires et commandes sont supprimés
  - Si une annonce est supprimée → ses commentaires et commandes sont supprimés
  - Si une catégorie est supprimée → ses annonces sont supprimées (donc vérification nécessaire)

---

## Endpoints API

### Base URL

```
http://127.0.0.1:8000/api
```

### 1. Authentification

| Méthode | Endpoint | Description | Auth requise |
|---------|----------|-------------|--------------|
| POST | `/auth/register` | Inscription utilisateur | Non |
| POST | `/auth/login` | Connexion utilisateur | Non |
| GET | `/auth/me` | Récupérer utilisateur connecté | Oui |
| POST | `/auth/logout` | Déconnexion | Oui |
| POST | `/auth/refresh` | Rafraîchir token JWT | Oui |

### 2. Catégories

| Méthode | Endpoint | Description | Auth requise |
|---------|----------|-------------|--------------|
| GET | `/categories` | Lister toutes les catégories | Non |
| POST | `/categories` | Créer une catégorie | Oui |
| PUT | `/categories/{id}` | Mettre à jour une catégorie | Oui |
| DELETE | `/categories/{id}` | Supprimer une catégorie | Oui |

### 3. Annonces

| Méthode | Endpoint | Description | Auth requise |
|---------|----------|-------------|--------------|
| GET | `/ads` | Lister toutes les annonces | Non |
| GET | `/ads/search` | Recherche intelligente | Non |
| GET | `/ads/{id}` | Détail d'une annonce | Non |
| POST | `/ads` | Créer une annonce | Oui |
| PUT | `/ads/{id}` | Modifier annonce (auteur) | Oui |
| DELETE | `/ads/{id}` | Supprimer annonce (auteur) | Oui |

### 4. Commentaires

| Méthode | Endpoint | Description | Auth requise |
|---------|----------|-------------|--------------|
| GET | `/ads/{adId}/comments` | Lister commentaires d'une annonce | Non |
| POST | `/ads/{adId}/comments` | Ajouter un commentaire | Oui |
| DELETE | `/comments/{id}` | Supprimer son propre commentaire | Oui |

### 5. Commandes

| Méthode | Endpoint | Description | Auth requise |
|---------|----------|-------------|--------------|
| POST | `/orders` | Passer une commande | Oui |
| GET | `/orders/me` | Lister mes commandes | Oui |
| PUT | `/orders/{id}/confirm` | Confirmer commande (vendeur) | Oui |
| PUT | `/orders/{id}/cancel` | Annuler commande | Oui |

---

## Exemples de requêtes

Les responses sont grandement simplifiés. Consultez le fichier [ADS_API_Collection.json](./postman_collection.json).

### 1. Inscription

**Endpoint** : `POST /api/auth/register`

**Body** :
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Réponse** (201) :
```json
{
  "status": "success",
  "message": "User registered successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-27T10:00:00.000000Z"
  }
}
```

### 2. Connexion

**Endpoint** : `POST /api/auth/login`

**Body** :
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse** (200) :
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

### 3. Créer une catégorie

**Endpoint** : `POST /api/categories`

**Headers** :
```
Authorization: Bearer {votre_token}
Content-Type: application/json
```

**Body** :
```json
{
  "name": "Électronique"
}
```

**Réponse** (201) :
```json
{
  "data": {
    "id": 1,
    "name": "Électronique",
    "created_at": "2025-12-27T10:05:00.000000Z"
  }
}
```

### 4. Créer une annonce

**Endpoint** : `POST /api/ads`

**Headers** :
```
Authorization: Bearer {votre_token}
Content-Type: application/json
```

**Body** :
```json
{
  "title": "iPhone 13 Pro",
  "description": "iPhone en excellent état, jamais réparé",
  "price": 699.99,
  "category_id": 1
}
```

**Réponse** (201) :
```json
{
  "data": {
    "id": 1,
    "title": "iPhone 13 Pro",
    "description": "iPhone en excellent état, jamais réparé",
    "price": 699.99,
    "user_id": 1,
    "category_id": 1,
    "created_at": "2025-12-27T10:10:00.000000Z"
  }
}
```

### 5. Lister les annonces avec pagination

**Endpoint** : `GET /api/ads?page=1&per_page=10`

**Réponse** (200) :
```json
{
  "data": [
    {
      "id": 1,
      "title": "iPhone 13 Pro",
      "description": "iPhone en excellent état",
      "price": 699.99,
      "user": {
        "id": 1,
        "name": "John Doe"
      },
      "category": {
        "id": 1,
        "name": "Électronique"
      },
      "created_at": "2025-12-27T10:10:00.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/ads?page=1",
    "last": "http://127.0.0.1:8000/api/ads?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

### 6. Rechercher des annonces

**Endpoint** : `GET /api/ads/search?q=iphone&category_id=1&min_price=500&max_price=1000`

**Réponse** (200) :
```json
{
  "data": [
    {
      "id": 1,
      "title": "iPhone 13 Pro",
      "price": 699.99,
      "category": {
        "name": "Électronique"
      }
    }
  ]
}
```

### 7. Ajouter un commentaire

**Endpoint** : `POST /api/ads/1/comments`

**Headers** :
```
Authorization: Bearer {votre_token}
Content-Type: application/json
```

**Body** :
```json
{
  "content": "Est-ce que le prix est négociable ?"
}
```

**Réponse** (201) :
```json
{
  "data": {
    "id": 1,
    "content": "Est-ce que le prix est négociable ?",
    "user": {
      "id": 2,
      "name": "Jane Smith"
    },
    "ad_id": 1,
    "created_at": "2025-12-27T10:15:00.000000Z"
  }
}
```

### 8. Passer une commande

**Endpoint** : `POST /api/orders`

**Headers** :
```
Authorization: Bearer {votre_token}
Content-Type: application/json
```

**Body** :
```json
{
  "ad_id": 1,
}
```

**Réponse** (201) :
```json
{
  "data": {
    "id": 1,
    "ad": {
      "id": 1,
      "title": "iPhone 13 Pro"
    },
    "buyer": {
      "id": 2,
      "name": "Jane Smith"
    },
    "seller": {
      "id": 1,
      "name": "John Doe"
    },
    "status": "pending",
    "created_at": "2025-12-27T10:20:00.000000Z"
  }
}
```

---

## Tests avec Postman

### Importer la collection

1. Téléchargez le fichier [ADS_API_Collection.json](./postman_collection.json)
2. Dans Postman, cliquez sur **Import**
3. Sélectionnez le fichier JSON
4. La collection complète sera importée

### Configuration des variables

La collection utilise deux variables :
- `base_url` : http://127.0.0.1:8000/api
- `token` : Automatiquement rempli après login

### Workflow recommandé

1. **Register** → Créer un compte
2. **Login** → Se connecter (le token est auto-sauvegardé)
3. **Create Category** → Créer des catégories
4. **Create Ad** → Créer des annonces
5. **List Ads** → Voir toutes les annonces
6. Tester les autres endpoints...

---

## Authentification

### Format du token JWT

Toutes les routes protégées nécessitent un header d'authentification :

```
Authorization: Bearer {votre_token_jwt}
```

### Durée de validité

Le token JWT expire après **60 minutes** par défaut. Utilisez `/api/auth/refresh` pour obtenir un nouveau token.

### Gestion des erreurs d'authentification

**401 Unauthenticated** :
```json
{
  "status": "error",
  "message": "Unauthenticated"
}
```

**403 Forbidden** :
```json
{
  "status": "error",
  "message": "Unauthorized action"
}
```

---

## Gestion des erreurs

### Format standard des erreurs

```json
{
  "status": "error",
  "message": "Description de l'erreur"
}
```

### Codes HTTP utilisés

- **200** : Succès
- **201** : Ressource créée
- **204** : Suppression réussie (pas de contenu)
- **401** : Non authentifié
- **403** : Action non autorisée
- **404** : Ressource non trouvée
- **422** : Erreur de validation
- **500** : Erreur serveur

---

## Règles métier importantes

### Catégories
- Le nom de la catégorie doit être unique
- Impossible de supprimer une catégorie contenant des annonces

### Annonces
- Seul l'auteur peut modifier ou supprimer son annonce
- Une annonce doit obligatoirement avoir une catégorie valide
- Le prix doit être un nombre positif

### Commentaires
- Seul l'auteur peut supprimer son commentaire
- Un commentaire doit être lié à une annonce existante

### Commandes
- Le vendeur peut confirmer une commande
- L'acheteur ou le vendeur peuvent annuler une commande
- Les statuts possibles : `PENDING`, `CONFIRMED`, `CANCELLED` (en majuscules)
- Le prix de la commande est stocké au moment de la création

---

## Sécurité

### Bonnes pratiques implémentées

- Authentification JWT
- Hachage des mots de passe (bcrypt)
- Validation des données entrantes
- Policies pour l'autorisation
- Protection CSRF désactivée pour l'API
- Rate limiting (optionnel)
