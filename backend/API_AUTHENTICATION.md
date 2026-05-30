# Tomodachi Pet Shop - Authentication API Documentation

## Overview
Three-tier role-based authentication system using Laravel Sanctum with Bearer token authentication.

**Roles:**
- **owner** - Can manage users (register admin/kasir)
- **admin** - Administrative access
- **kasir** - Cashier/staff access

---

## API Endpoints

### 1. Login
**Requirement:** REQ-AUTH-01, REQ-AUTH-02

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "kasir@petshop.local",
    "password": "password"
}
```

**Response (200 OK):**
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
            "role_id": 3
        },
        "token": "1|abc123xyz...",
        "token_type": "Bearer"
    }
}
```

**Response (401 Unauthorized):**
```json
{
    "success": false,
    "message": "Invalid email or password",
    "errors": []
}
```

**Validation Rules:**
- `email` - Required, valid email, must exist in database
- `password` - Required, minimum 6 characters

---

### 2. Public Registration
Creates a new user with **kasir** role.

```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "New Kasir",
    "email": "newkasir@petshop.local",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 4,
            "name": "New Kasir",
            "email": "newkasir@petshop.local",
            "role": "kasir",
            "role_id": 3
        },
        "token": "2|def456uvw...",
        "token_type": "Bearer"
    }
}
```

**Validation Rules:**
- `name` - Required, string, max 255 characters
- `email` - Required, valid email, unique in database
- `password` - Required, minimum 8 characters, must be confirmed

---

### 3. Register User by Owner (Owner Only)
**Requirement:** REQ-AUTH-08 - Only owner can register admin and kasir

```http
POST /api/auth/register-user
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "New Admin",
    "email": "newadmin@petshop.local",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
}
```

**Allowed Roles:**
- `admin` - Administrator account
- `kasir` - Cashier account

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 5,
            "name": "New Admin",
            "email": "newadmin@petshop.local",
            "role": "admin",
            "role_id": 1
        },
        "token": "3|ghi789xyz...",
        "token_type": "Bearer"
    }
}
```

**Response (403 Forbidden - Not Owner):**
```json
{
    "success": false,
    "message": "Forbidden - You do not have permission to access this resource"
}
```

**Response (401 Unauthorized - No Token):**
```json
{
    "success": false,
    "message": "Unauthorized",
    "errors": {
        "authorization": "Authorization header is required"
    }
}
```

---

### 4. Get Current User
**Requirement:** REQ-AUTH-03 - Bearer token validation

```http
GET /api/auth/me
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "Kasir",
        "email": "kasir@petshop.local",
        "role": "kasir",
        "role_id": 3
    }
}
```

---

### 5. Refresh Token
**Requirement:** REQ-AUTH-03 - Bearer token validation

```http
POST /api/auth/refresh-token
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "token": "4|new_token_xyz...",
        "token_type": "Bearer"
    }
}
```

**Note:** Old token is automatically revoked when refreshed.

---

### 6. Logout
**Requirement:** REQ-AUTH-06 - Revoke token on logout

```http
POST /api/auth/logout
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Logout successful",
    "data": null
}
```

---

## Bearer Token Format
**Requirement:** REQ-AUTH-03

All protected endpoints require Bearer token in Authorization header:

```http
Authorization: Bearer 1|abc123xyz...
```

**Format Validation:**
- Header name: `Authorization`
- Token format: `Bearer <token>`
- Token cannot be empty

**Invalid Header Examples:**
```
Authorization: abc123xyz...              ❌ Missing "Bearer" prefix
Authorization: Bearer                    ❌ Empty token
Authorization: Basic abc123...           ❌ Wrong auth type
```

---

## Error Responses

### 401 Unauthorized - Missing/Invalid Token
```json
{
    "success": false,
    "message": "Missing Authorization header",
    "errors": {
        "authorization": "Authorization header is required"
    }
}
```

### 401 Unauthorized - Invalid Token Format
```json
{
    "success": false,
    "message": "Invalid Authorization header format",
    "errors": {
        "authorization": "Authorization header must use Bearer token format: Bearer <token>"
    }
}
```

### 403 Forbidden - Insufficient Permissions
```json
{
    "success": false,
    "message": "Forbidden - You do not have permission to access this resource"
}
```

### 422 Unprocessable Entity - Validation Failed
```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": [
            "The email has already been taken."
        ]
    }
}
```

---

## Middleware Stack

### `auth:sanctum`
- Validates Bearer token
- Attaches authenticated user to `$request->user()`
- Returns 401 if token invalid or missing

### `check.role:role1,role2,...`
- Validates user's role matches allowed roles
- Returns 403 if role doesn't match
- Example: `check.role:owner,admin`

---

## Testing with cURL

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kasir@petshop.local",
    "password": "password"
  }'
```

### Access Protected Endpoint
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Register by Owner
```bash
curl -X POST http://localhost:8000/api/auth/register-user \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer OWNER_TOKEN_HERE" \
  -d '{
    "name": "New Admin",
    "email": "newadmin@petshop.local",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
  }'
```

---

## Database Seeding

### Default Users
Run seeders to create default users:

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

**Default Credentials:**

| Role  | Email                    | Password  |
|-------|--------------------------|-----------|
| Owner | owner@petshop.local      | password  |
| Admin | admin@petshop.local      | password  |
| Kasir | kasir@petshop.local      | password  |

---

## Implementation Requirements (SRS)

- ✅ **REQ-AUTH-01**: Login endpoint with email & password validation
- ✅ **REQ-AUTH-02**: Return Sanctum token on successful login
- ✅ **REQ-AUTH-03**: Bearer token validation on protected endpoints
- ✅ **REQ-AUTH-06**: Logout endpoint to revoke token
- ✅ **REQ-AUTH-08**: Only owner can register admin/kasir accounts

---

## Notes

- Tokens are stored in `personal_access_tokens` table
- One active token per user (previous tokens are revoked on login)
- Tokens do not expire by default but can be configured in Sanctum config
- Password is automatically hashed using bcrypt
- All responses follow consistent JSON format: `{success, message, data, errors}`
