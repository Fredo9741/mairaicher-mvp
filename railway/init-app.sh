#!/bin/bash
set -e

echo "🚀 Starting Railway deployment initialization..."

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Always run AdminUserSeeder to ensure admin credentials are up-to-date
echo "🔑 Ensuring admin user exists with correct credentials..."
php artisan db:seed --force --class=AdminUserSeeder
echo "✅ Admin user seeder completed"

# Check if database is empty (no users exist)
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();")

if [ "$USER_COUNT" -eq "1" ]; then
    echo "🌱 Database has only admin, running full seeders..."
    php artisan db:seed --force
    echo "✅ Full seeders completed successfully"
else
    echo "⏭️  Database already contains data, skipping additional seeders"
    echo "   Found $USER_COUNT users in database"
fi

echo "✨ Deployment initialization completed!"
