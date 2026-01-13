#!/bin/bash
set -e

# ====== TEMPORARY: DATABASE REFRESH MODE ======
# This mode will be used ONCE to refresh the database completely
# After running once, restore from init-app.sh.backup

echo "🔄 Starting Railway DATABASE REFRESH (ONE-TIME OPERATION)..."
echo "⚠️  This will delete all data and images!"

# Step 1: Clear all Cloudflare R2 images
echo "🗑️  Clearing all images from Cloudflare R2..."
php artisan tinker --execute="
use Illuminate\Support\Facades\Storage;

\$disk = Storage::disk('r2');

if (\$disk->exists('products')) {
    \$files = \$disk->allFiles('products');
    echo 'Found ' . count(\$files) . ' product images to delete...' . PHP_EOL;
    foreach (\$files as \$file) {
        \$disk->delete(\$file);
    }
    echo 'Product images deleted!' . PHP_EOL;
}

if (\$disk->exists('bundles')) {
    \$files = \$disk->allFiles('bundles');
    echo 'Found ' . count(\$files) . ' bundle images to delete...' . PHP_EOL;
    foreach (\$files as \$file) {
        \$disk->delete(\$file);
    }
    echo 'Bundle images deleted!' . PHP_EOL;
}

if (\$disk->exists('heroes')) {
    \$files = \$disk->allFiles('heroes');
    echo 'Found ' . count(\$files) . ' hero images to delete...' . PHP_EOL;
    foreach (\$files as \$file) {
        \$disk->delete(\$file);
    }
    echo 'Hero images deleted!' . PHP_EOL;
}

echo '✅ All images cleared from Cloudflare R2' . PHP_EOL;
"

# Step 2: Fresh migration
echo "📦 Running fresh migrations..."
php artisan migrate:fresh --force

# Step 3: Run all seeders
echo "🌱 Seeding database..."
php artisan db:seed --force

echo "✨ Railway database refresh completed successfully!"
echo ""
echo "📊 Database Statistics:"
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Products: ' . App\Models\Product::count() . PHP_EOL;
echo 'Bundles: ' . App\Models\Bundle::count() . PHP_EOL;
echo 'Pickup Slots: ' . App\Models\PickupSlot::count() . PHP_EOL;
echo 'Orders: ' . App\Models\Order::count() . PHP_EOL;
"

# ====== ORIGINAL CODE (COMMENTED OUT) ======
# Uncomment this code after the refresh is complete

# echo "🚀 Starting Railway deployment initialization..."

# # Run migrations
# echo "📦 Running database migrations..."
# php artisan migrate --force

# # Always run AdminUserSeeder to ensure admin credentials are up-to-date
# echo "🔑 Ensuring admin user exists with correct credentials..."
# php artisan db:seed --force --class=AdminUserSeeder
# echo "✅ Admin user seeder completed"

# # Check if database needs seeding (check for products, not users)
# PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\Product::count();")

# if [ "$PRODUCT_COUNT" -eq "0" ]; then
#     echo "🌱 Database is empty, running all seeders..."
#     php artisan db:seed --force
#     echo "✅ All seeders completed successfully"
# else
#     echo "⏭️  Database already contains data, skipping additional seeders"
#     echo "   Found $PRODUCT_COUNT products in database"
# fi

# echo "✨ Deployment initialization completed!"
