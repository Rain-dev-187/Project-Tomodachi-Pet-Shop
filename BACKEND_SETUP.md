# Backend Setup Guide - Tomodachi Pet Shop

## Quick Start

### 1. Install Dependencies
```bash
cd backend
composer install
```

### 2. Environment Setup
```bash
# Copy .env template (if exists)
cp .env.example .env

# Or create a new .env file with database config
```

### 3. Configure .env
Edit `backend/.env` and set these values:

```env
APP_NAME="Tomodachi Pet Shop"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tomodachi_petshop
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:8000
SANCTUM_EXPIRATION=10080
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Database
```bash
php artisan db:seed
```

### 7. Start Development Server
```bash
php artisan serve
```

The API will be available at: **http://localhost:8000/api**

---

## Database Schema

### Users Table
```sql
users:
  - id (primary key)
  - role_id (foreign key)
  - name (string)
  - email (unique string)
  - password (hashed)
  - email_verified_at (nullable timestamp)
  - remember_token (nullable)
  - created_at (timestamp)
  - updated_at (timestamp)
```

### Roles Table
```sql
roles:
  - id (primary key)
  - name (string, unique)
  - created_at (timestamp)
  - updated_at (timestamp)
```

### Personal Access Tokens Table (Sanctum)
```sql
personal_access_tokens:
  - id (primary key)
  - tokenable_id
  - tokenable_type
  - name
  - token (hashed)
  - abilities (json)
  - last_used_at (nullable)
  - expires_at (nullable)
  - created_at
  - updated_at
```

---

## Testing the API

### Using cURL

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kasir@petshop.local",
    "password": "password"
  }'
```

**Get Current User (with token):**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Logout:**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Using Postman

1. Import endpoints from documentation
2. Create "Bearer Token" authentication
3. Add token to Authorization header
4. Test each endpoint

---

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@petshop.local | password |
| Kasir | kasir@petshop.local | password |
| Owner | owner@petshop.local | password |

---

## File Locations

- **Controllers**: `backend/app/Http/Controllers/Api/`
- **Models**: `backend/app/Models/`
- **Migrations**: `backend/database/migrations/`
- **Routes**: `backend/routes/api.php`
- **Config**: `backend/config/`
- **Middleware**: `backend/app/Http/Middleware/`

---

## Useful Artisan Commands

```bash
# List all routes
php artisan route:list

# Create a new controller
php artisan make:controller Api/YourController --api

# Create a migration
php artisan make:migration create_table_name

# Create a model with migration
php artisan make:model ModelName -m

# Run specific migration
php artisan migrate --path=database/migrations/2026_05_19_012300_create_roles_table.php

# Reset database
php artisan migrate:reset

# Rollback last migration
php artisan migrate:rollback

# Check all migrations status
php artisan migrate:status

# Run specific seeder
php artisan db:seed --class=RoleSeeder

# Clear all cache
php artisan cache:clear
```

---

## Development Tips

### Enable Query Logging
Add to routes or controller:
```php
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### Debug Mode
Enable in .env:
```env
APP_DEBUG=true
```

### Clear Cache Between Changes
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### View Environment
```bash
php artisan tinker
>>> config('app.env')
```

---

## Docker Setup (Optional)

If using Docker:

```bash
# Start containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed
```

---

## Next Steps

1. ✅ Set up database
2. ✅ Run migrations and seeders  
3. ✅ Test authentication endpoints
4. 📝 Extend API with more endpoints
5. 🔒 Implement role-based access control
6. 📊 Add product management endpoints
7. 💳 Add transaction endpoints

---

## Troubleshooting

### "SQLSTATE[HY000]: General error: 1030 Got error..."
- Check database connection in .env
- Ensure database exists and is accessible

### "Class not found" errors
```bash
composer dumpautoload
```

### Token errors in Flutter
- Ensure token format is correct: `Bearer {token}`
- Check token hasn't expired
- Verify token is from current user

### CORS errors
- Check `SANCTUM_STATEFUL_DOMAINS` in .env
- Verify Flutter app is making requests from allowed origin

---

## Support

For Laravel docs: https://laravel.com/docs
For Sanctum docs: https://laravel.com/docs/sanctum
