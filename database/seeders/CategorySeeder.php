<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Món Chính Đặc Sắc',
                'slug' => 'mon-chinh',
                'description' => 'Các món ăn no ngon miệng, chuẩn vị đậm đà hấp dẫn',
                'icon' => 'fa-utensils',
            ],
            [
                'id' => 2,
                'name' => 'Khai Vị & Ăn Vặt',
                'slug' => 'khai-vi-an-vat',
                'description' => 'Món ăn kèm giòn rụm, lai rai cực đã',
                'icon' => 'fa-bowl-food',
            ],
            [
                'id' => 3,
                'name' => 'Đồ Uống & Tráng Miệng',
                'slug' => 'do-uong-trang-mieng',
                'description' => 'Thức uống giải khát mát lạnh và món ngọt thanh mát',
                'icon' => 'fa-mug-hot',
            ],
            [
                'id' => 4,
                'name' => 'Món Chay Thanh Tịnh',
                'slug' => 'mon-chay',
                'description' => 'Món chay bổ dưỡng từ rau củ quả tươi tự nhiên',
                'icon' => 'fa-leaf',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['id' => $cat['id']], $cat);
        }
    }
}
