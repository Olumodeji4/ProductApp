<?php

namespace classes;

use app\BaseQuery;
use models\ProductAttribute;
use models\ProductCategory;

class ProductClass extends BaseQuery
{
// Making it easy to indicate the types of product we have and types mapping.
    private $product_types = [
        'dvd' => [
            'attribute_key' => 'size',
            'attribute_value' => ['size']
        ],
        'furniture' => ['attribute_key' => 'dimension',
            'attribute_value' => ['height', 'width', 'length']
        ],
        'book' => [
            'attribute_key' => 'weight',
            'attribute_value' => ['weight']
        ]


    ];

    public function __construct()
    {
        parent::__construct();
        $this->set_table();
        $this->set_unique_columns(); // specifying unique columns in your database for product table
        $this->relationship_id = 'product_id';
        $this->relationship_with = ['belongsTo' => 'product_categories', 'hasMany' => 'product_attributes'];

    }

    /**
     * Returns all the list of products in the database
     * TODO Open to redesign and convert to database relationship query for each db table
     */
    public function get_product_list()
    {
        $select = " DISTINCT products.*, product_attributes.attribute_key AS attrib_key,product_attributes.attribute_value AS attrib_value,product_categories.name AS category_name";
        return $this->get_all([ProductCategory::class, ProductAttribute::class], $select);
    }

    /**
     * Return a single product information
     */
    public function store_products($data)
    {
        $category = $this->get_product_category($data['relationType']);

        $product_data = [
            'product_category_id' => $category['id'] ?? null,
            'sku' => $this->sanitize($data['sku']),
            'name' => $this->sanitize($data['name']),
            'price' => $data['price'] ?? 0,
        ];
        $product = $this->first_or_create($product_data);
        if ($product) {
            $this->store_product_attributes($product, $data);
        }
        return $product;

    }

    /**
     * @param $product_object
     * @param $data
     * $relation = [
     * 'attribute_key' => 'dimension',
     * 'attribute_value' => '23x39x49'
     * ];
     */

    private function store_product_attributes($product_object, $data)
    {
//        switch (strtolower($data['relationType'])) {
//            case "dvd":
//                $prepare = [
//                    'attribute_key' => 'size',
//                    'attribute_value' => $data['productType']['size']
//                ];
//                return $this->add_related_details($product_object, ProductAttribute::class, $prepare);
//
//            case "furniture":
//                $prepare = [
//                    'attribute_key' => 'dimension',
//                    'attribute_value' => ($data['productType']['height'] ?? '') . 'x' . ($data['productType']['width'] ?? '') . 'x' . ($data['productType']['length'] ?? '')
//                ];
//
//                return $this->add_related_details($product_object, ProductAttribute::class, $prepare);
//
//            default:
//                //Book
//                $prepare = [
//                    'attribute_key' => 'weight',
//                    'attribute_value' => $data['productType']['weight'] ?? ''
//                ];
//        }
        // removing switch statement
        $prepare = $this->get_prepared_attributes($data);

        return $this->add_related_details($product_object, ProductAttribute::class, $prepare);

    }

    private function get_prepared_attributes($data)
    {
        try {
            $prepared = new \stdClass();
            $name = strtolower(trim($data['relationType'])); // get product type from frontend
            $fetched = $this->product_types[$name]; // use it to get the right type for it
            $prepared->attribute_key = ucwords($fetched['attribute_key']);
            $attribute_values = $fetched['attribute_value'];
            $loop_count = 1;
            foreach ($attribute_values as $value) {
                $current_count = count($attribute_values);
                // loop is not ended
                $end = $current_count !== $loop_count ? 'x' : '';
                $prepared->attribute_value .= $data['productType'][$value] . $end;
                $loop_count++;
            }
            return (array)$prepared;
        } catch (\Exception $e) {

        }
    }


    private function get_product_category($slug)
    {
        return $this->find_by_slug($slug, 'product_categories');
    }

    public function set_table()
    {
        $db = $this->db;
        $this->table = 'products';
    }

    public function set_unique_columns()
    {
        $this->unique_columns = ['sku'];
    }

    /**
     * This method creates demo products and attributes for testing
     */
    public function add_demo_data()
    {
        $seeders = $this->demo_products();
        if ($seeders) {
            foreach ($seeders as $seed) {
                $product = $this->first_or_create([
                    'product_category_id' => $seed['product_category_id'] ?? null,
                    'name' => $seed['name'] ?? null,
                    'sku' => $seed['sku'] ?? null,
                    'price' => $seed['price'] ?? null,
                    'symbol' => '&#36;'
                ]);

                if ($seed['attribute_key']) {
                    var_dump($product);
                    $this->add_related_details($product, ProductAttribute::class, ['attribute_key' => $seed['attribute_key'], 'attribute_value' => $seed['attribute_value']]);
                }
            }
        }
    }

    /**
     * Array of Products to be seeded
     * @return array[]
     */
    private function demo_products()
    {
        return [
            [
                'product_category_id' => 2,
                'name' => 'Soft Cuisine',
                'sku' => 'chair-1230',
                'price' => 3490323.43,
                'attribute_key' => 'dimension',
                'attribute_value' => '23x39x49'
            ],
            [
                'product_category_id' => 3,
                'name' => 'Step Up',
                'sku' => 'dvd-100',
                'price' => 4930.49,
                'attribute_key' => 'size',
                'attribute_value' => 10002
            ],
            [
                'product_category_id' => 3,
                'name' => 'Panama',
                'sku' => 'dvd-221',
                'price' => 38493.34,
                'attribute_key' => 'size',
                'attribute_value' => 10002
            ],
            [
                'product_category_id' => 3,
                'name' => 'Jeans Up',
                'sku' => 'dvd-2389',
                'price' => 8903.28,
                'attribute_key' => 'size',
                'attribute_value' => 10402
            ],
            [
                'product_category_id' => 1,
                'name' => 'Wines',
                'sku' => 'book-329',
                'price' => 84934.23,
                'attribute_key' => 'weight',
                'attribute_value' => 30
            ],
            [
                'product_category_id' => 3,
                'name' => 'Infinity Wars',
                'sku' => 'dvd-3932',
                'price' => 49304.30,
                'attribute_key' => 'size',
                'attribute_value' => 13002
            ],
            [
                'product_category_id' => 3,
                'name' => 'Halo',
                'sku' => 'dvd-39320',
                'price' => 434.349,
                'attribute_key' => 'size',
                'attribute_value' => 15902
            ],
            [
                'product_category_id' => 1,
                'name' => 'Smile',
                'sku' => 'book-22349',
                'price' => 3203.3,
                'attribute_key' => 'weight',
                'attribute_value' => 2
            ],
            [
                'product_category_id' => 3,
                'name' => 'Eternals',
                'sku' => 'dvd-8320',
                'price' => 3023.32,
                'attribute_key' => 'size',
                'attribute_value' => 10302
            ],
            [
                'product_category_id' => 1,
                'name' => 'Wild Boys',
                'sku' => 'book-82304',
                'price' => 3932.03,
                'attribute_key' => 'weight',
                'attribute_value' => 10
            ],
            [
                'product_category_id' => 1,
                'name' => 'Scream',
                'sku' => 'book-392349',
                'price' => 1984.03,
                'attribute_key' => 'weight',
                'attribute_value' => 14
            ],
            [
                'product_category_id' => 1,
                'name' => 'Rest',
                'sku' => 'book-32933',
                'price' => 1293.30,
                'attribute_key' => 'weight',
                'attribute_value' => 4
            ],
            [
                'product_category_id' => 3,
                'name' => 'Triple X',
                'sku' => 'dvd-8320',
                'price' => 3294.32,
                'attribute_key' => 'size',
                'attribute_value' => 14902
            ],
            [
                'product_category_id' => 3,
                'name' => 'Max Out',
                'sku' => 'dvd-59650',
                'price' => 32394.83,
                'attribute_key' => 'size',
                'attribute_value' => 49002
            ],
            [
                'product_category_id' => 2,
                'name' => 'Italian Pillows',
                'sku' => 'chair-329343',
                'price' => 30320.43,
                'attribute_key' => 'dimension',
                'attribute_value' => '83x49x83'
            ],
            [
                'product_category_id' => 2,
                'name' => 'Leather Smoothie',
                'sku' => 'chair-382940',
                'price' => 30839.38,
                'attribute_key' => 'dimension',
                'attribute_value' => '28x33x44'
            ],
            [
                'product_category_id' => 2,
                'name' => 'Rolex Insider',
                'sku' => 'chair-349430',
                'price' => 4045894.54,
                'attribute_key' => 'dimension',
                'attribute_value' => '21x49x79'
            ],
            [
                'product_category_id' => 2,
                'name' => 'Chillers',
                'sku' => 'chair-89453',
                'price' => 94344.54,
                'attribute_key' => 'dimension',
                'attribute_value' => '33x99x29'
            ],
            [
                'product_category_id' => 1,
                'name' => 'Indexed',
                'sku' => 'book-493024',
                'price' => 20343.49,
                'attribute_key' => 'weight',
                'attribute_value' => 3
            ],
        ];
    }
}
