# 🛍️ Tomodachi Pet Shop - Backend API

Complete Laravel REST API backend for the Tomodachi Pet Shop Flutter mobile application.

## ✨ Features

### Authentication & Authorization
- ✅ Token-based authentication (Laravel Sanctum)
- ✅ User registration and login
- ✅ Role-based access control (Admin, Kasir, Owner)
- ✅ Secure password hashing (bcrypt)
- ✅ Token refresh mechanism
- ✅ Logout with token revocation

### API Response Format
- ✅ Consistent JSON response structure
- ✅ Clean, Flutter-friendly responses
- ✅ Proper HTTP status codes
- ✅ Validation error messages
- ✅ Pagination support

### Security
- ✅ CORS middleware configuration
- ✅ Request validation
- ✅ Role-based middleware
- ✅ Sanctum token protection
- ✅ Password hashing (bcrypt)

---

## 📋 API Endpoints

### Authentication
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/login` | Login user | ❌ |
| POST | `/api/auth/register` | Register new user | ❌ |
| GET | `/api/auth/me` | Get current user | ✅ |
| POST | `/api/auth/logout` | Logout user | ✅ |
| POST | `/api/auth/refresh-token` | Refresh token | ✅ |
| GET | `/api/health` | Health check | ❌ |

### Protected Resources (Coming Soon)
- Categories
- Products
- Transactions
- Inventory
- Orders

---

## 🚀 Quick Start

### 1. Prerequisites
- PHP 8.2+
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js (optional)

### 2. Installation

```bash
# Navigate to backend folder
cd backend

# Install dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database with roles and users
php artisan db:seed

# Start development server
php artisan serve
```

### 3. Testing

```bash
# Check API health
curl http://localhost:8000/api/health

# Login as kasir
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kasir@petshop.local",
    "password": "password"
  }'
```

---

## 🔐 Default Credentials

```
Admin:
  Email: admin@petshop.local
  Password: password

Kasir (Cashier):
  Email: kasir@petshop.local
  Password: password

Owner:
  Email: owner@petshop.local
  Password: password
```

---

## 📁 Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php        (Authentication logic)
│   │   │       ├── CategoryController.php
│   │   │       └── ProductController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php                (Role authorization)
│   │   │   └── CheckAuth.php                (Authentication check)
│   │   └── Requests/
│   │       ├── LoginRequest.php             (Login validation)
│   │       └── RegisterRequest.php          (Registration validation)
│   ├── Models/
│   │   ├── User.php                         (User model with Sanctum)
│   │   └── Role.php                         (Role model)
│   └── Traits/
│       └── ApiResponse.php                  (Reusable response methods)
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2026_05_19_012300_create_roles_table.php
│   │   └── 2026_05_19_012301_add_role_id_to_users_table.php
│   └── seeders/
│       ├── RoleSeeder.php                   (Seed roles)
│       └── UserSeeder.php                   (Seed test users)
├── routes/
│   └── api.php                              (API routes configuration)
├── config/
│   ├── app.php
│   ├── database.php
│   └── sanctum.php                          (Sanctum configuration)
└── .env.example                             (Environment template)
```

---

## 🔑 Authentication Implementation

### Using in Flutter

```dart
// Login
final response = await http.post(
  Uri.parse('http://localhost:8000/api/auth/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': email,
    'password': password,
  }),
);

// Extract token
String token = jsonDecode(response.body)['data']['token'];

// Save token (e.g., SharedPreferences)
await prefs.setString('auth_token', token);

// Use in authenticated requests
final meResponse = await http.get(
  Uri.parse('http://localhost:8000/api/auth/me'),
  headers: {
    'Authorization': 'Bearer $token',
  },
);
```

---

## 📊 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error details"]
  }
}
```

---

## 🛡️ Role-Based Access Control

### Middleware Usage

```php
// Admin only
Route::middleware('auth:sanctum', 'check.role:admin')->get('/admin', /* ... */);

// Multiple roles
Route::middleware('auth:sanctum', 'check.role:admin,owner')->get('/reports', /* ... */);
```

### Example Controller

```php
public function adminAction(Request $request)
{
    if ($request->user()->role->name !== 'admin') {
        return $this->errorResponse('Forbidden', 403);
    }
    
    return $this->successResponse($data);
}
```

---

## 🛠️ Configuration

### Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tomodachi_petshop
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:8000
SANCTUM_EXPIRATION=10080

# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

---

## 📚 Useful Commands

```bash
# Create new controller
php artisan make:controller Api/YourController --api

# Create new model with migration
php artisan make:model YourModel -m

# Create new request class
php artisan make:request YourRequest

# Create new migration
php artisan make:migration migration_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Run seeders
php artisan db:seed

# List all routes
php artisan route:list

# Clear all cache
php artisan cache:clear

# Start interactive shell
php artisan tinker
```

---

## 🐛 Troubleshooting

### "SQLSTATE[HY000]: General error"
**Solution**: Check database connection in `.env`
```bash
php artisan migrate
```

### "Class not found" errors
**Solution**: Refresh autoloader
```bash
composer dumpautoload
```

### Token authentication fails
**Solution**: Ensure header format
```
Authorization: Bearer {token}
```

### CORS errors in Flutter
**Solution**: Check `SANCTUM_STATEFUL_DOMAINS` in `.env`

---

## 📖 Documentation

- **Full API Docs**: See [API_AUTHENTICATION.md](../docs/API_AUTHENTICATION.md)
- **Setup Guide**: See [BACKEND_SETUP.md](../BACKEND_SETUP.md)
- **Laravel Docs**: https://laravel.com/docs
- **Sanctum Docs**: https://laravel.com/docs/sanctum

---

## 🚀 Next Steps

1. ✅ Authentication system
2. 📝 Extended user management
3. 📦 Product management API
4. 💳 Transaction handling
5. 📊 Reports and analytics
6. 🔔 Notifications system
7. 📸 Image upload handling

---

## 📝 License

This project is part of the Tomodachi Pet Shop application.

---

## 👤 Author

Tomodachi Pet Shop Development Team

---

## 🤝 Contributing

For contributions, please ensure:
1. Code follows PSR-12 standards
2. All tests pass
3. Documentation is updated
4. Commit messages are clear

---

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review API documentation
3. Check Laravel and Sanctum official docs

---

**Made with ❤️ for the Tomodachi Pet Shop Team**
