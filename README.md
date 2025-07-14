# Laravel Todo App API

Une API RESTful développée avec Laravel pour la gestion de tâches en équipe.  
Cette application permet aux utilisateurs de gérer leurs tâches

## 🧰 Fonctionnalités principales

- Authentification avec Laravel Sanctum
- Gestion CRUD des projets, tâches, tags, utilisateurs
- Attribution de tâches à des utilisateurs
- Gestion du statut des tâches
- Recherche & filtres des tâches
- Tests fonctionnels
- Documentation API

---

## 🚀 Installation du projet

### 1. Cloner le dépôt

```bash
git clone https://github.com/ArthurBrgn/laravel-todo-api.git
cd laravel-todo-api
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Copier le fichier d'environnement

```bash
cp .env.example .env
```

### 4. Générer la clé de l'application

```bash
php artisan key:generate
```

### 5. Configurer la base de données

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=secret
```

### 6. Lancer les migrations et seeders

```bash
php artisan migrate --seed
```

### 7. Lancer le serveur de développement

```bash
php artisan serve
```

### 8. Générer la documentation API (optionnel)

```bash
php artisan scribe:generate
```

La documentation API sera disponible ici: http://127.0.0.1:8000/docs

## 🧪 Tests

```bash
php artisan test
```


