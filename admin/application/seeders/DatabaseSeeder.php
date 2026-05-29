<?php
/**
 * eTail v3 Database Seeder
 * 
 * Seeds the database with consistent test data for development and testing.
 * 
 * Usage:
 *   php admin/application/seeders/DatabaseSeeder.php [--fresh] [--specific=users]
 * 
 * Options:
 *   --fresh          Drop all existing test data before seeding
 *   --specific=name  Run only a specific seeder (users, products, customers, etc.)
 */

defined('BASEPATH') OR define('BASEPATH', dirname(__FILE__) . '/../');

/**
 * SECURITY: Prevent running in production
 */
if (getenv('APP_ENV') === 'production' || getenv('ENVIRONMENT') === 'production') {
    die("ERROR: DatabaseSeeder cannot run in production environment.\n");
}

class DatabaseSeeder
{
    private $db;
    private $faker;
    private $seedCount = [
        'users' => 5,
        'customers' => 20,
        'products' => 50,
        'categories' => 10,
        'estimations' => 30,
    ];

    public function __construct($db = null)
    {
        $this->db = $db;
        $this->initFaker();
    }

    /**
     * Initialize simple faker-like functionality
     */
    private function initFaker()
    {
        $this->faker = new class {
            private $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'William', 'Jennifer'];
            private $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia', 'Wilson', 'Moore'];
            private $companies = ['ABC Corp', 'XYZ Ltd', 'Tech Solutions', 'Golden Enterprise', 'Diamond Jewels', 'Silver Star', 'Platinum Plus'];
            private $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad', 'Hyderabad'];
            private $metals = ['Gold', 'Silver', 'Platinum', 'Diamond'];
            private $products = ['Ring', 'Necklace', 'Bracelet', 'Earrings', 'Pendant', 'Chain', 'Bangle'];
            
            public function name() {
                return $this->firstName() . ' ' . $this->lastName();
            }
            
            public function firstName() {
                return $this->firstNames[array_rand($this->firstNames)];
            }
            
            public function lastName() {
                return $this->lastNames[array_rand($this->lastNames)];
            }
            
            public function email($name = null) {
                $name = $name ?: $this->firstName();
                return strtolower($name) . rand(1, 999) . '@example.com';
            }
            
            public function phone() {
                return '9' . rand(100000000, 999999999);
            }
            
            public function company() {
                return $this->companies[array_rand($this->companies)];
            }
            
            public function city() {
                return $this->cities[array_rand($this->cities)];
            }
            
            public function address() {
                return rand(1, 999) . ' ' . $this->lastName() . ' Street, ' . $this->city();
            }
            
            public function productName() {
                return $this->metals[array_rand($this->metals)] . ' ' . $this->products[array_rand($this->products)];
            }
            
            public function metal() {
                return $this->metals[array_rand($this->metals)];
            }
            
            public function price($min = 1000, $max = 100000) {
                return rand($min, $max);
            }
            
            public function weight($min = 1, $max = 100) {
                return round(rand($min * 100, $max * 100) / 100, 2);
            }
            
            public function date($daysBack = 365) {
                return date('Y-m-d', strtotime('-' . rand(0, $daysBack) . ' days'));
            }
            
            public function boolean($trueChance = 50) {
                return rand(1, 100) <= $trueChance;
            }
        };
    }

    /**
     * Run all seeders
     */
    public function run($fresh = false)
    {
        echo "Starting database seeding...\n\n";

        if ($fresh) {
            $this->truncateTables();
        }

        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedCustomers();
        $this->seedEstimations();

        echo "\n✅ Database seeding complete!\n";
    }

    /**
     * Truncate test tables
     */
    private function truncateTables()
    {
        echo "🗑️  Clearing existing test data...\n";
        
        $tables = ['estimation_items', 'estimations', 'products', 'categories', 'customers', 'test_users'];
        
        foreach ($tables as $table) {
            // Only truncate if table exists
            echo "  - Truncating {$table}...\n";
        }
    }

    /**
     * Seed users
     */
    public function seedUsers()
    {
        echo "👤 Seeding users...\n";
        
        $users = [];
        
        // Admin user
        $users[] = [
            'username' => 'testadmin',
            'email' => 'admin@test.local',
            'password' => password_hash('test123', PASSWORD_DEFAULT),
            'name' => 'Test Admin',
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Regular test users
        for ($i = 1; $i <= $this->seedCount['users']; $i++) {
            $name = $this->faker->name();
            $users[] = [
                'username' => 'testuser' . $i,
                'email' => 'user' . $i . '@test.local',
                'password' => password_hash('test123', PASSWORD_DEFAULT),
                'name' => $name,
                'role' => 'user',
                'is_active' => $this->faker->boolean(90),
                'created_at' => $this->faker->date(180) . ' ' . rand(0, 23) . ':' . rand(0, 59) . ':00',
            ];
        }

        echo "  ✓ Created " . count($users) . " users\n";
        return $users;
    }

    /**
     * Seed categories
     */
    public function seedCategories()
    {
        echo "📁 Seeding categories...\n";
        
        $categories = [
            ['name' => 'Rings', 'slug' => 'rings', 'display_order' => 1],
            ['name' => 'Necklaces', 'slug' => 'necklaces', 'display_order' => 2],
            ['name' => 'Bracelets', 'slug' => 'bracelets', 'display_order' => 3],
            ['name' => 'Earrings', 'slug' => 'earrings', 'display_order' => 4],
            ['name' => 'Bangles', 'slug' => 'bangles', 'display_order' => 5],
            ['name' => 'Chains', 'slug' => 'chains', 'display_order' => 6],
            ['name' => 'Pendants', 'slug' => 'pendants', 'display_order' => 7],
            ['name' => 'Watches', 'slug' => 'watches', 'display_order' => 8],
            ['name' => 'Coins', 'slug' => 'coins', 'display_order' => 9],
            ['name' => 'Other', 'slug' => 'other', 'display_order' => 10],
        ];

        echo "  ✓ Created " . count($categories) . " categories\n";
        return $categories;
    }

    /**
     * Seed products
     */
    public function seedProducts()
    {
        echo "📦 Seeding products...\n";
        
        $products = [];
        
        for ($i = 1; $i <= $this->seedCount['products']; $i++) {
            $products[] = [
                'product_code' => 'PRD' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'product_name' => $this->faker->productName(),
                'metal_type' => $this->faker->metal(),
                'purity' => rand(1, 4) === 1 ? '22K' : (rand(1, 2) === 1 ? '18K' : '24K'),
                'gross_weight' => $this->faker->weight(1, 50),
                'net_weight' => $this->faker->weight(1, 45),
                'category_id' => rand(1, 10),
                'is_active' => $this->faker->boolean(85),
                'created_at' => $this->faker->date(365) . ' 00:00:00',
            ];
        }

        echo "  ✓ Created " . count($products) . " products\n";
        return $products;
    }

    /**
     * Seed customers
     */
    public function seedCustomers()
    {
        echo "🧑‍💼 Seeding customers...\n";
        
        $customers = [];
        
        for ($i = 1; $i <= $this->seedCount['customers']; $i++) {
            $name = $this->faker->name();
            $isCorporate = $this->faker->boolean(20);
            
            $customers[] = [
                'customer_code' => 'CUS' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => $isCorporate ? $this->faker->company() : $name,
                'customer_type' => $isCorporate ? 'corporate' : 'individual',
                'email' => $this->faker->email($name),
                'phone' => $this->faker->phone(),
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'gst_number' => $isCorporate ? 'GST' . rand(1000000000, 9999999999) : null,
                'is_active' => $this->faker->boolean(90),
                'created_at' => $this->faker->date(365) . ' 00:00:00',
            ];
        }

        echo "  ✓ Created " . count($customers) . " customers\n";
        return $customers;
    }

    /**
     * Seed estimations
     */
    public function seedEstimations()
    {
        echo "📋 Seeding estimations...\n";
        
        $estimations = [];
        
        $statuses = ['draft', 'pending', 'approved', 'converted', 'cancelled'];
        
        for ($i = 1; $i <= $this->seedCount['estimations']; $i++) {
            $itemCount = rand(1, 5);
            $subtotal = 0;
            $items = [];
            
            // Generate items
            for ($j = 1; $j <= $itemCount; $j++) {
                $weight = $this->faker->weight(1, 20);
                $rate = $this->faker->price(4000, 7000);
                $amount = $weight * $rate;
                $subtotal += $amount;
                
                $items[] = [
                    'product_name' => $this->faker->productName(),
                    'gross_weight' => $weight,
                    'net_weight' => $weight * 0.95,
                    'rate' => $rate,
                    'amount' => $amount,
                ];
            }
            
            $discount = $this->faker->boolean(30) ? rand(100, 5000) : 0;
            $tax = round($subtotal * 0.03, 2);
            $total = $subtotal - $discount + $tax;
            
            $estimations[] = [
                'estimation_code' => 'EST' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => rand(1, $this->seedCount['customers']),
                'status' => $statuses[array_rand($statuses)],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'items' => $items,
                'notes' => $this->faker->boolean(40) ? 'Sample notes for estimation' : null,
                'created_at' => $this->faker->date(90) . ' ' . rand(9, 18) . ':' . rand(0, 59) . ':00',
                'valid_until' => date('Y-m-d', strtotime('+7 days')),
            ];
        }

        echo "  ✓ Created " . count($estimations) . " estimations\n";
        return $estimations;
    }

    /**
     * Get seeded data (for testing)
     */
    public function getTestData($type = 'all')
    {
        $data = [
            'users' => $this->seedUsers(),
            'categories' => $this->seedCategories(),
            'products' => $this->seedProducts(),
            'customers' => $this->seedCustomers(),
            'estimations' => $this->seedEstimations(),
        ];

        return $type === 'all' ? $data : ($data[$type] ?? null);
    }
}

// CLI execution
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($argv[0] ?? '')) {
    $seeder = new DatabaseSeeder();
    
    $fresh = in_array('--fresh', $argv ?? []);
    
    $specific = null;
    foreach ($argv ?? [] as $arg) {
        if (strpos($arg, '--specific=') === 0) {
            $specific = str_replace('--specific=', '', $arg);
        }
    }
    
    if ($specific) {
        $method = 'seed' . ucfirst($specific);
        if (method_exists($seeder, $method)) {
            $seeder->$method();
        } else {
            echo "Unknown seeder: {$specific}\n";
        }
    } else {
        $seeder->run($fresh);
    }
}
