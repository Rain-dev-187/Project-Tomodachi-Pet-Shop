# Tomodachi Pet Shop - REST API Documentation

## Overview
This is a Laravel REST API backend for the Tomodachi Pet Shop Flutter mobile application. The API implements token-based authentication using Laravel Sanctum and supports role-based access control.

## Architecture

### Technology Stack
- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/MariaDB
- **PHP**: ^8.2

### User Roles
- **Admin**: Full access to the system
- **Kasir (Cashier)**: Handle transactions and sales
- **Owner**: Management and reporting access

## Authentication System

### Token-Based Authentication
The API uses **Bearer Token** authentication via Laravel Sanctum. All requests to protected endpoints must include:

```
Authorization: Bearer {token}
```

### Authentication Flow

1. **Login** → Receive token
2. **Use token** → Include in Authorization header
3. **Logout** → Token is revoked
4. **Refresh** → Get new token when needed

## API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Health Check
```
GET /health
```
**Response:**
```json
{
  "success": true,
  "message": "Tomodachi Pet Shop API connected",
  "data": {
    "app": "Tomodachi Pet Shop",
    "environment": "local",
    "time": "2026-05-22T10:30:00Z"
  }
}
```

---

## Authentication Endpoints

### 1. Login
**Endpoint:**
```
POST /api/auth/login
```

**Request:**
```json
{
  "email": "kasir@petshop.local",
  "password": "password"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Kasir",
      "email": "kasir@petshop.local",
      "role": "kasir",
      "role_id": 2
    },
    "token": "1|abcdef1234567890...",
    "token_type": "Bearer"
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid email or password"
}
```

---

### 2. Register
**Endpoint:**
```
POST /api/auth/register
```

**Request:**
```json
{
  "name": "New User",
  "email": "newuser@petshop.local",
  "password": "password",
  "password_confirmation": "password"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 2,
      "name": "New User",
      "email": "newuser@petshop.local",
      "role": "kasir",
      "role_id": 2
    },
    "token": "2|abcdef1234567890...",
    "token_type": "Bearer"
  }
}
```

**Validation Errors (422):**
```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "email": ["Email already registered"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

---

### 3. Get Current User
**Endpoint:**
```
GET /api/auth/me
```

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "name": "Kasir",
    "email": "kasir@petshop.local",
    "role": "kasir",
    "role_id": 2
  }
}
```

**Unauthorized Response (401):**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

### 4. Logout
**Endpoint:**
```
POST /api/auth/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout successful",
  "data": null
}
```

---

### 5. Refresh Token
**Endpoint:**
```
POST /api/auth/refresh-token
```

**Headers:**
```
Authorization: Bearer {old_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "3|abcdef1234567890...",
    "token_type": "Bearer"
  }
}
```

---

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Success message",
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
    // Optional validation errors
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Success message",
  "data": [
    // Array of items
  ],
  "pagination": {
    "total": 100,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "total_pages": 10
  }
}
```

---

## HTTP Status Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful request |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Missing or invalid token |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation error |
| 500 | Internal Server Error | Server error |

---

## Default Test Credentials

After running migrations and seeders:

### Admin Account
- Email: `admin@petshop.local`
- Password: `password`
- Role: `admin`

### Kasir Account
- Email: `kasir@petshop.local`
- Password: `password`
- Role: `kasir`

### Owner Account
- Email: `owner@petshop.local`
- Password: `password`
- Role: `owner`

---

## Setup Instructions

### 1. Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js and npm (for frontend)

### 2. Backend Setup

**Clone/Open the project:**
```bash
cd backend
```

**Install dependencies:**
```bash
composer install
```

**Create .env file:**
```bash
cp .env.example .env
```

**Configure database in .env:**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tomodachi_petshop
DB_USERNAME=root
DB_PASSWORD=
```

**Generate application key:**
```bash
php artisan key:generate
```

**Run migrations:**
```bash
php artisan migrate
```

**Run seeders:**
```bash
php artisan db:seed
```

**Start development server:**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

---

## Using the API with Flutter

### 1. Installation
Add http package to `pubspec.yaml`:
```yaml
dependencies:
  http: ^1.1.0
```

### 2. Example: Login

```dart
import 'package:http/http.dart' as http;

Future<void> login(String email, String password) async {
  final response = await http.post(
    Uri.parse('http://localhost:8000/api/auth/login'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'email': email,
      'password': password,
    }),
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    String token = data['data']['token'];
    
    // Save token to local storage
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }
}
```

### 3. Example: Authenticated Request

```dart
Future<void> getCurrentUser(String token) async {
  final response = await http.get(
    Uri.parse('http://localhost:8000/api/auth/me'),
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    print(data['data']); // User info
  }
}
```

### 4. Example: Logout

```dart
Future<void> logout(String token) async {
  final response = await http.post(
    Uri.parse('http://localhost:8000/api/auth/logout'),
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );

  if (response.statusCode == 200) {
    // Clear local token
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
}
```

---

## Role-Based Access Control

### Using Middleware in Routes

```php
// Only admin can access
Route::middleware('auth:sanctum', 'check.role:admin')->get('/admin-only', /* ... */);

// Multiple roles allowed
Route::middleware('auth:sanctum', 'check.role:admin,owner')->get('/management', /* ... */);
```

### Checking Role in Controller

```php
public function someAction(Request $request)
{
    if ($request->user()->role->name !== 'admin') {
        return $this->errorResponse('Forbidden', 403);
    }
    
    // Action code here
}
```

---

## Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── AuthController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   └── CheckAuth.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       └── RegisterRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Role.php
│   └── Traits/
│       └── ApiResponse.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── routes/
│   └── api.php
└── config/
    └── sanctum.php
```

---

## Common Issues and Solutions

### Issue: Token expires quickly
**Solution:** Set `SANCTUM_EXPIRATION` in .env (in minutes)
```
SANCTUM_EXPIRATION=10080
```

### Issue: CORS errors from Flutter
**Solution:** Configure CORS in `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
```

### Issue: Authentication fails
**Solution:** 
1. Verify token is included in Authorization header
2. Use format: `Bearer {token}`
3. Check token hasn't been deleted from database

---

## Production Considerations

1. **HTTPS**: Always use HTTPS in production
2. **Token Expiration**: Set appropriate token expiration time
3. **CORS**: Configure specific allowed origins instead of '*'
4. **Rate Limiting**: Implement rate limiting on auth endpoints
5. **Logging**: Enable proper logging for debugging
6. **Environment**: Set `APP_ENV=production` and `APP_DEBUG=false`
7. **Database**: Use strong passwords and proper backup strategy

---

## Support and Troubleshooting

For detailed Laravel documentation: https://laravel.com/docs
For Sanctum documentation: https://laravel.com/docs/sanctum
For API best practices: https://jsonapi.org/
