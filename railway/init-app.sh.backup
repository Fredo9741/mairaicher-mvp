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

# Check if database needs seeding (check for products, not users)
PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\Product::count();")

if [ "$PRODUCT_COUNT" -eq "0" ]; then
    echo "🌱 Database is empty, running all seeders..."
    php artisan db:seed --force
    echo "✅ All seeders completed successfully"
else
    echo "⏭️  Database already contains data, skipping additional seeders"
    echo "   Found $PRODUCT_COUNT products in database"
fi

echo "✨ Deployment initialization completed!"
