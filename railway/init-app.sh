#!/bin/bash
set -e

echo "🚀 Starting Railway deployment initialization..."

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Check if database is empty (no users exist)
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();")

if [ "$USER_COUNT" -eq "0" ]; then
    echo "🌱 Database is empty, running seeders..."
    php artisan db:seed --force
    echo "✅ Seeders completed successfully"
else
    echo "⏭️  Database already contains data, skipping seeders"
    echo "   Found $USER_COUNT users in database"
fi

echo "✨ Deployment initialization completed!"
