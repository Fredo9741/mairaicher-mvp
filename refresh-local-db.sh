#!/bin/bash
set -e

echo "🔄 Starting local database refresh..."

# Step 1: Fresh migration
echo "📦 Running fresh migrations..."
php artisan migrate:fresh --force

# Step 2: Run all seeders
echo "🌱 Seeding database..."
php artisan db:seed --force

echo "✨ Local database refresh completed successfully!"
echo ""
echo "📊 Database Statistics:"
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Products: ' . App\Models\Product::count() . PHP_EOL;
echo 'Bundles: ' . App\Models\Bundle::count() . PHP_EOL;
echo 'Pickup Slots: ' . App\Models\PickupSlot::count() . PHP_EOL;
echo 'Orders: ' . App\Models\Order::count() . PHP_EOL;
"

echo ""
echo "👤 Test Accounts:"
echo "  Developer: developer@maraicher.test / Dev@2026!Secure"
echo "  Admin: admin@maraicher.test / Admin@2026!Secure"
echo "  Customer: customer@maraicher.test / Customer@2026!Secure"
