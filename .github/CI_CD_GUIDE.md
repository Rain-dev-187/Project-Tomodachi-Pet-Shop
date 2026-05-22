# CI/CD Workflows Documentation

Proyek ini menggunakan GitHub Actions untuk Continuous Integration dan testing otomatis.

## 📋 Workflows

### 1. **Backend CI** (`backend.yml`)
Menjalankan testing dan quality checks untuk backend Laravel.

**Fitur:**
- ✅ PHP Unit Tests (PHPUnit)
- ✅ Static Analysis (PHPStan)
- ✅ Code Style Check (Laravel Pint)
- ✅ MySQL Service untuk testing database
- ✅ Code Coverage reports

**Trigger:**
- Push ke branch `main` atau `develop`
- Pull request ke branch `main` atau `develop`
- Hanya jika ada perubahan di folder `backend/`

**Requirements:**
- PHP 8.2+
- MySQL 8.0+
- Composer dependencies sudah di-install

### 2. **Frontend CI** (`frontend.yml`)
Menjalankan testing dan build untuk frontend Flutter.

**Fitur:**
- ✅ Flutter Analyze
- ✅ Flutter Unit Tests
- ✅ Android APK Build
- ✅ Web Build
- ✅ Artifact uploads untuk testing

**Trigger:**
- Push ke branch `main` atau `develop`
- Pull request ke branch `main` atau `develop`
- Hanya jika ada perubahan di folder `frontend/`

**Requirements:**
- Flutter SDK 3.11.1+
- Android SDK (untuk build APK)

### 3. **Main CI** (`ci.yml`)
Orchestrator workflow yang menjalankan backend dan frontend secara parallel.

**Fitur:**
- Menjalankan backend dan frontend testing secara bersamaan
- Final status check untuk memastikan semua checks lolos
- Fail-fast jika ada test yang gagal

## 🚀 Quick Start

### Setup lokal

1. **Backend:**
   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan test
   ```

2. **Frontend:**
   ```bash
   cd frontend
   flutter pub get
   flutter analyze
   flutter test
   ```

### Pre-push hooks (Optional)

Untuk memastikan code lulus CI sebelum di-push:

```bash
# Install pre-commit hooks
# Backend
cd backend && composer install && cd ..

# Run tests sebelum push
git hook add pre-push './.github/hooks/pre-push.sh'
```

## 📊 Monitoring

Semua workflow runs dapat dipantau di:
- **GitHub Web:** Repository → Actions tab
- **Local:** Gunakan `gh` CLI

```bash
# Install GitHub CLI
# Check status
gh run list

# View specific run
gh run view <RUN_ID>
```

## ⚙️ Configuration

### Backend Adjustments

**PHPUnit Configuration** (`backend/phpunit.xml`):
- Ubah database connection jika diperlukan
- Adjust environment variables sesuai kebutuhan

**PHPStan Configuration** (`backend/phpstan.neon` atau di composer.json):
```json
{
  "scripts": {
    "phpstan": "phpstan analyse app --memory-limit=512M"
  }
}
```

**Pint Configuration** (`backend/pint.json`):
```json
{
  "preset": "laravel"
}
```

### Frontend Adjustments

**Flutter Test Configuration** (`frontend/test/`):
- Tambahkan test files sesuai kebutuhan
- Gunakan `flutter test --coverage` untuk code coverage

## 📝 Best Practices

1. **Commit Messages:**
   ```
   [backend] Fix authentication issue
   [frontend] Update UI for login screen
   ```

2. **Branch Naming:**
   ```
   feature/user-auth
   bugfix/login-crash
   docs/api-docs
   ```

3. **Pull Requests:**
   - Ensure semua CI checks lolos
   - Request review sebelum merge
   - Squash commits saat merge

4. **Code Quality:**
   - Tulis tests untuk fitur baru
   - Run `pint` dan `phpstan` sebelum push (backend)
   - Run `flutter analyze` dan `flutter test` sebelum push (frontend)

## 🔧 Troubleshooting

### Backend CI gagal

**PHPUnit tests failed:**
```bash
cd backend
php artisan migrate --force
php artisan test
```

**PHPStan errors:**
```bash
cd backend
vendor/bin/phpstan analyse app --memory-limit=512M
```

**Pint style errors:**
```bash
cd backend
vendor/bin/pint  # Auto-fix
vendor/bin/pint --test  # Only check
```

### Frontend CI gagal

**Flutter analyze issues:**
```bash
cd frontend
flutter analyze
```

**Flutter tests failing:**
```bash
cd frontend
flutter test --verbose
```

**Build failures:**
```bash
cd frontend
flutter clean
flutter pub get
flutter build apk  # or web
```

## 📚 Useful Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Testing](https://laravel.com/docs/11.x/testing)
- [Flutter Testing](https://flutter.dev/docs/testing)
- [PHPStan Documentation](https://phpstan.org/)
- [Laravel Pint](https://laravel.com/docs/11.x/pint)

## 🤝 Contributing

Saat berkontribusi:

1. Fork repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m '[backend/frontend] description'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request
6. Pastikan semua CI checks lolos

---

**Last Updated:** 2024
**Maintained by:** Development Team
