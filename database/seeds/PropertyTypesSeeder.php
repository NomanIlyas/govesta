<?php

use App\Model\Property\Type;
use App\Model\Property\SubType;
use Illuminate\Database\Seeder;

class PropertyTypesSeeder extends Seeder
{
    public function run()
    {
        // Default Currencies
        $types = array(
            array(
                "name" => "Flat",
                "items" => [
                    "Flat",
                    "Ground-floor Flat",
                    "Studio Flat",
                    "Maisonette",
                    "Apartment",
                    "Penthouse",
                    "Serviced Apartment",
                    "Other",
                ],
            ),
            array(
                "name" => "House",
                "items" =>
                [
                    "Terraced House",
                    "Detached House",
                    "Semi-detached House",
                    "Link Detached House",
                    "Mews House",
                    "Town House",
                    "Bungalow",
                    "Cottage",
                    "Chalet",
                    "Other",
                ],
            ),
            array(
                "name" => "Commercial",
                "items" => [
                    "Shops",
                    "Financial and Professional Services",
                    "Restaurants and Cafes",
                    "Drinking Establishments",
                    "Hot Food Take away",
                    "Business",
                    "General Industrial",
                    "Storage and Distribution",
                    "Hotels",
                    "Residential Institutions",
                    "Secure Residential Institution",
                    "Dwelling Houses",
                    "Non-Residential Institutions",
                    "Assembly and Leisure"
                ],
            )
        );
        foreach ($types as $type) {

            $added = Type::create([
                "slug" => str_slug($type['name']),
                "name" => $type['name'],
            ]);

            foreach ($type['items'] as $subType) {
                SubType::create([
                    'type_id' => $added->id,
                    "slug" => str_slug($subType),
                    "name" => $subType,
                ]);
            }
        }
    }
}
