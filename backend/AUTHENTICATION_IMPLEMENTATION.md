# Authentication Implementation Summary

## ✅ Completed Requirements

### #20 - Backend: Implementasi endpoint login dengan email dan password
- **Status:** ✅ IMPLEMENTED
- **Endpoint:** `POST /api/auth/login`
- **Features:**
  - Email & password validation
  - Returns Sanctum Bearer token
  - Single active token per user (revokes previous)
  - REQ-AUTH-01 & REQ-AUTH-02 compliant

### #21 - Backend: Implementasi middleware autentikasi token pada semua endpoint
- **Status:** ✅ IMPLEMENTED
- **Features:**
  - `auth:sanctum` middleware protects all endpoints
  - Bearer token validation middleware added
  - ValidateBearerToken checks format before Sanctum validation
  - REQ-AUTH-03 compliant

### #22 - Backend: Implementasi endpoint logout dan revoke token
- **Status:** ✅ IMPLEMENTED
- **Endpoint:** `POST /api/auth/logout`
- **Features:**
  - Revokes current access token
  - Deletes token from database
  - REQ-AUTH-06 compliant

### #23 - Backend: Hanya owner yang bisa mendaftarkan akun kasir dan admin
- **Status:** ✅ IMPLEMENTED
- **Endpoint:** `POST /api/auth/register-user` (authenticated)
- **Features:**
  - Only owner role can access
  - Can specify `role` parameter (admin|kasir)
  - CheckRole middleware enforces access control
  - REQ-AUTH-08 compliant

---

## Architecture Overview

### 3-Level Role System

| Role  | Capabilities |
|-------|---|
| **Owner** | Register new users (admin/kasir), Access all endpoints, Manage system |
| **Admin** | Access all endpoints, Administrative tasks |
| **Kasir** | Access assigned resources, Perform transactions |

### Authentication Flow

```
1. Public Registration (Optional)
   POST /api/auth/register
   → Creates user with 'kasir' role
   → Returns token

2. Login
   POST /api/auth/login (email + password)
   → Validates credentials
   → Returns Bearer token

3. Access Protected Resources
   Authorization: Bearer <token>
   → middleware validates token
   → Attaches user to request

4. Owner Registers New Users
   POST /api/auth/register-user (authenticated, owner only)
   → Specifies role (admin|kasir)
   → Creates user with chosen role
   → Returns token

5. Logout
   POST /api/auth/logout
   → Revokes Bearer token
```

---

## File Changes

### New/Modified Files

1. **[app/Http/Requests/RegisterRequest.php](app/Http/Requests/RegisterRequest.php)**
   - Added role-based authorization
   - Role parameter validation for owner registration
   - Conditional validation rules

2. **[app/Http/Controllers/Api/AuthController.php](app/Http/Controllers/Api/AuthController.php)**
   - Updated register() method with role assignment logic
   - Supports both public and owner-authenticated registration
   - Added detailed documentation with requirement references

3. **[routes/api.php](routes/api.php)**
   - Separated public and protected auth routes
   - Added `/api/auth/register-user` for owner-only registration
   - Added CheckRole middleware for authorization

4. **[app/Http/Kernel.php](app/Http/Kernel.php)**
   - Added 'bearer' middleware alias

5. **[app/Http/Middleware/ValidateBearerToken.php](app/Http/Middleware/ValidateBearerToken.php)**
   - Already complete - validates Bearer token format

6. **[app/Http/Middleware/CheckRole.php](app/Http/Middleware/CheckRole.php)**
   - Already exists - enables role-based access control

7. **[API_AUTHENTICATION.md](API_AUTHENTICATION.md)** (NEW)
   - Comprehensive API documentation
   - cURL examples for testing
   - Error response formats
   - Database seeding instructions

---

## API Endpoints Reference

### Public Endpoints (No Authentication)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/api/auth/login` | User login with email/password |
| POST | `/api/auth/register` | Public registration (creates kasir) |
| GET | `/api/health` | Health check |

### Protected Endpoints (Requires Bearer Token)

| Method | Endpoint | Purpose | Role |
|--------|----------|---------|------|
| GET | `/api/auth/me` | Get current user | Any |
| POST | `/api/auth/logout` | Logout and revoke token | Any |
| POST | `/api/auth/refresh-token` | Refresh access token | Any |
| POST | `/api/auth/register-user` | Register new user | Owner only |

### Resource Endpoints (Requires Bearer Token)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/categories` | List categories |
| POST | `/api/categories` | Create category |
| GET | `/api/products` | List products |
| POST | `/api/products` | Create product |
| ... | ... | ... (other CRUD operations) |

---

## Token Usage

### Getting a Token
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kasir@petshop.local",
    "password": "password"
  }'
```

### Using Token
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer 1|abc123xyz..."
```

### Invalid Token Examples
```
Authorization: Bearer            ❌ Empty token
Authorization: abc123xyz...      ❌ Missing Bearer prefix
Authorization: Basic abc123...   ❌ Wrong auth type
```

---

## Middleware Stack

### Route: Protected API Resources
```
1. api (throttle, SubstituteBindings)
2. auth:sanctum (validate token, attach user)
→ Request reaches controller with authenticated user
```

### Route: Owner-Only Registration
```
1. api (throttle, SubstituteBindings)
2. auth:sanctum (validate token, attach user)
3. check.role:owner (validate role)
→ Request reaches controller with authorized owner
```

---

## Database Schema

### Relevant Tables

#### `users`
```
id, role_id, name, email, password, email_verified_at, remember_token, created_at, updated_at
```

#### `roles`
```
id, name, created_at, updated_at
```
**Roles:** admin, kasir, owner

#### `personal_access_tokens` (Sanctum)
```
id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at
```

---

## Testing Guide

### Prerequisites
- Database seeded with roles and default users
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### Default Credentials
```
Owner: owner@petshop.local / password
Admin: admin@petshop.local / password
Kasir: kasir@petshop.local / password
```

### Test Scenarios

#### Scenario 1: Public Registration → Login → Access Resource
```bash
# 1. Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
# Response includes token

# 2. Login with same credentials
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
# Response includes token

# 3. Access protected resource
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer {token}"
```

#### Scenario 2: Owner Registers Admin
```bash
# 1. Login as owner
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "owner@petshop.local",
    "password": "password"
  }'
# Copy token from response

# 2. Register new admin
curl -X POST http://localhost:8000/api/auth/register-user \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {owner_token}" \
  -d '{
    "name": "New Admin",
    "email": "newadmin@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
  }'
```

#### Scenario 3: Non-Owner Cannot Register User
```bash
# Login as kasir
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kasir@petshop.local",
    "password": "password"
  }'
# Copy token

# Try to register user (should fail)
curl -X POST http://localhost:8000/api/auth/register-user \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {kasir_token}" \
  -d '{
    "name": "Test",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
  }'
# Response: 403 Forbidden
```

---

## Implementation Notes

1. **Token Management**
   - Sanctum uses database tokens (not JWT)
   - Tokens stored in `personal_access_tokens` table
   - One active token per user (previous tokens revoked on login)

2. **Password Security**
   - Passwords hashed using Laravel's bcrypt hasher
   - Never returned in API responses

3. **Error Handling**
   - Consistent JSON error format
   - Proper HTTP status codes (401, 403, 422, etc.)
   - Validation errors returned with field-level details

4. **Role Checking**
   - `check.role` middleware supports multiple roles
   - Example: `check.role:owner,admin`
   - Role name checked from `users.roles.name`

5. **Token Format Validation**
   - ValidateBearerToken middleware checks format before Sanctum
   - Ensures Bearer prefix and non-empty token
   - Better error messages for clients

---

## Next Steps (Optional Enhancements)

1. Add token expiration configuration
2. Implement refresh token strategy
3. Add rate limiting on login attempts
4. Add email verification workflow
5. Add two-factor authentication
6. Add activity logging for audit trail
7. Add permission system beyond roles

---

## References

- [Laravel Sanctum Documentation](https://laravel.com/docs/11.x/sanctum)
- [Laravel Middleware](https://laravel.com/docs/11.x/middleware)
- [Laravel Authorization (Policies)](https://laravel.com/docs/11.x/authorization)
- SRS Requirements: REQ-AUTH-01, REQ-AUTH-02, REQ-AUTH-03, REQ-AUTH-06, REQ-AUTH-08
