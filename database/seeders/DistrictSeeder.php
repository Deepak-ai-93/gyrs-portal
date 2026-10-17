<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * The 33 districts of Gujarat with their administrative region.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    public const DISTRICTS = [
        ['Ahmedabad', 'Central Gujarat'],
        ['Amreli', 'Saurashtra'],
        ['Anand', 'Central Gujarat'],
        ['Aravalli', 'North Gujarat'],
        ['Banaskantha', 'North Gujarat'],
        ['Bharuch', 'South Gujarat'],
        ['Bhavnagar', 'Saurashtra'],
        ['Botad', 'Saurashtra'],
        ['Chhota Udaipur', 'Central Gujarat'],
        ['Dahod', 'Central Gujarat'],
        ['Dang', 'South Gujarat'],
        ['Devbhoomi Dwarka', 'Saurashtra'],
        ['Gandhinagar', 'North Gujarat'],
        ['Gir Somnath', 'Saurashtra'],
        ['Jamnagar', 'Saurashtra'],
        ['Junagadh', 'Saurashtra'],
        ['Kutch', 'Kutch'],
        ['Kheda', 'Central Gujarat'],
        ['Mahisagar', 'Central Gujarat'],
        ['Mehsana', 'North Gujarat'],
        ['Morbi', 'Saurashtra'],
        ['Narmada', 'South Gujarat'],
        ['Navsari', 'South Gujarat'],
        ['Panchmahal', 'Central Gujarat'],
        ['Patan', 'North Gujarat'],
        ['Porbandar', 'Saurashtra'],
        ['Rajkot', 'Saurashtra'],
        ['Sabarkantha', 'North Gujarat'],
        ['Surat', 'South Gujarat'],
        ['Surendranagar', 'Saurashtra'],
        ['Tapi', 'South Gujarat'],
        ['Vadodara', 'Central Gujarat'],
        ['Valsad', 'South Gujarat'],
    ];

    public function run(): void
    {
        foreach (self::DISTRICTS as $index => [$name, $region]) {
            District::updateOrCreate(
                ['slug' => str($name)->slug()->value()],
                [
                    'name' => $name,
                    'region' => $region,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
