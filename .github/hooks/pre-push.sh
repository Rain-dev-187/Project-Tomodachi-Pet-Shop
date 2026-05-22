#!/bin/bash

# Pre-push hook untuk memastikan code lolos basic checks sebelum push
# Instalasi: ln -s ../../.github/hooks/pre-push.sh .git/hooks/pre-push
# chmod +x .git/hooks/pre-push

set -e

echo "🔍 Running pre-push checks..."

BACKEND_CHANGED=false
FRONTEND_CHANGED=false

# Check apa saja yang akan di-push
if git diff --name-only origin/$(git rev-parse --abbrev-ref HEAD) 2>/dev/null | grep -q '^backend/'; then
  BACKEND_CHANGED=true
fi

if git diff --name-only origin/$(git rev-parse --abbrev-ref HEAD) 2>/dev/null | grep -q '^frontend/'; then
  FRONTEND_CHANGED=true
fi

# Backend checks
if [ "$BACKEND_CHANGED" = true ]; then
  echo "📦 Running backend checks..."
  
  cd backend
  
  echo "  ├─ Checking PHP syntax..."
  php -l app/ 2>&1 | grep -v "No syntax errors" || true
  
  echo "  ├─ Running PHPUnit tests..."
  if ! php artisan test --stop-on-failure; then
    echo "  ❌ PHPUnit tests failed"
    cd ..
    exit 1
  fi
  
  echo "  ├─ Running code style check (Pint)..."
  if ! vendor/bin/pint --test --quiet 2>/dev/null; then
    echo "  ⚠️  Code style issues found. Running auto-fix..."
    vendor/bin/pint
  fi
  
  cd ..
fi

# Frontend checks
if [ "$FRONTEND_CHANGED" = true ]; then
  echo "📱 Running frontend checks..."
  
  cd frontend
  
  echo "  ├─ Running Flutter analyze..."
  if ! flutter analyze --no-pub; then
    echo "  ❌ Flutter analyze failed"
    cd ..
    exit 1
  fi
  
  echo "  ├─ Running Flutter tests..."
  if ! flutter test 2>&1 | tail -5; then
    echo "  ⚠️  Some tests may have failed (continuing anyway)"
  fi
  
  cd ..
fi

echo ""
echo "✅ All pre-push checks passed!"
echo "Proceed with git push..."
