<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foods = [
            // Món Chính (category_id: 1)
            [
                'category_id' => 1,
                'name' => 'Phở Bò Tái Lăn Hà Nội',
                'description' => 'Bò tươi xào lăn thơm phức tỏi gừng, nước dùng hầm xương đậm đà 24h ăn kèm quẩy giòn.',
                'price' => 65000,
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Cơm Tấm Sườn Bì Chả Trứng',
                'description' => 'Sườn nướng mật ong than hoa thơm lừng, bì dai giòn, chả trứng béo ngậy kèm mỡ hành tóp mỡ.',
                'price' => 60000,
                'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bún Chả Nướng Than Hoa',
                'description' => 'Chả viên và chả miếng nướng xém cạnh, nước mắm đu đủ cà rốt chua ngọt chuẩn vị phố cổ.',
                'price' => 55000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bánh Mì Kẹp Thịt Nướng Đặc Biệt',
                'description' => 'Vỏ bánh giòn rụm, pate béo ngậy tự làm, thịt xá xíu nướng mè và đồ chua sốt cay đặc biệt.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Mì Quảng Tôm Thịt Trứng Cút',
                'description' => 'Sợi mì vàng óng dai mềm, tôm rim đậm đà, thịt ba chỉ béo ngậy, rắc đậu phộng rang giòn.',
                'price' => 50000,
                'image' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],

            // Khai Vị & Ăn Vặt (category_id: 2)
            [
                'category_id' => 2,
                'name' => 'Gà Rán Giòn Sốt Phô Mai Cay',
                'description' => 'Đùi gà giòn tan rụm phủ lớp sốt cay Hàn Quốc và phô mai kéo sợi thơm phức béo ngậy.',
                'price' => 59000,
                'image' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Nem Rán Giòn Hà Nội (6 chiếc)',
                'description' => 'Nhân thịt băm mộc nhĩ nấm hương bọc bánh đa nem chiên vàng ruộm giòn tan.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Khoai Tây Chiên Lắc Phô Mai',
                'description' => 'Khoai tây cắt lát chiên vàng giòn rụm, lắc đều bột phô mai béo mặn ngọt hài hòa.',
                'price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],

            // Đồ Uống & Tráng Miệng (category_id: 3)
            [
                'category_id' => 3,
                'name' => 'Trà Sữa Trân Châu Đường Đen',
                'description' => 'Trà sữa ô long thơm nồng kết hợp trân châu thủ công dẻo dai đun sốt đường đen đậm vị.',
                'price' => 38000,
                'image' => 'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Trà Trái Cây Nhiệt Đới Tươi Mát',
                'description' => 'Trà lài ủ lạnh kết hợp chanh leo, dâu tây, cam vàng tươi mát giải nhiệt tức thì.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Bánh Flan Trứng Sữa Cà Phê',
                'description' => 'Flan mềm mịn tan chảy trong miệng hòa quyện cùng nước cốt cà phê thơm lừng và đá bào.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],

            // Món Chay (category_id: 4)
            [
                'category_id' => 4,
                'name' => 'Cơm Chiên Hạt Sen Nấm Hương Chay',
                'description' => 'Cơm rang tơi xốp với hạt sen bùi béo, nấm hương tươi, cà rốt và đậu Hà Lan thanh nhẹ.',
                'price' => 48000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Salad Bơ Trái Cây Sốt Mè Rang',
                'description' => 'Bơ sáp béo ngậy, cà chua bi, xà lách giòn và sốt mè rang Nhật Bản thanh mát tốt cho sức khỏe.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
        ];

        foreach ($foods as $food) {
            Food::updateOrCreate(
                ['name' => $food['name']],
                $food,
            );
        }
    }
}
