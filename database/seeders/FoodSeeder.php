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
            // ================= MÓN CHÍNH ĐẶC SẮC (category_id: 1) =================
            [
                'category_id' => 1,
                'name' => 'Phở Bò Tái Lăn Hà Nội',
                'description' => 'Bò tươi xào lăn thơm phức tỏi gừng, nước dùng hầm xương bò 24h ngọt thanh tự nhiên, ăn kèm quẩy giòn và giấm tỏi ớt.',
                'price' => 65000,
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Cơm Tấm Sườn Bì Chả Trứng',
                'description' => 'Sườn nướng mật ong than hoa mềm thơm, bì heo trộn thính dai giòn, chả trứng béo ngậy kèm mỡ hành tóp mỡ giòn rụm.',
                'price' => 60000,
                'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bún Chả Nướng Than Hoa Phố Cổ',
                'description' => 'Chả viên và chả miếng nướng xém cạnh thơm lừng, nước mắm đu đủ cà rốt chua ngọt chuẩn vị phố cổ Hà Nội, kèm rau sống tươi sạch.',
                'price' => 55000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bánh Mì Kẹp Thịt Nướng Đặc Biệt',
                'description' => 'Vỏ bánh nướng giòn rụm, pate béo ngậy thủ công, thịt xá xíu nướng mè, chả lụa và đồ chua sốt ớt cay đậm đà.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Mì Quảng Tôm Thịt Trứng Cút',
                'description' => 'Sợi mì vàng óng dai mềm, tôm rim mặn ngọt, thịt ba chỉ kho đậm đà, trứng cút, bánh tráng mè nướng và nước lèo sền sệt.',
                'price' => 50000,
                'image' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bún Bò Huế Chả Cua Giò Heo',
                'description' => 'Nước dùng ninh xương cay nồng thơm lừng mùi sả ruốc miền Trung, bắp bò hoa thái lát, móng giò mềm béo và chả cua giòn ngọt.',
                'price' => 68000,
                'image' => 'https://images.unsplash.com/photo-1576577445504-6af96477db52?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Cơm Rang Dưa Bò Hà Nội',
                'description' => 'Hạt cơm vàng óng đảo tơi giòn tan xào cùng bắp bò mềm ngọt và dưa cải muối chua giòn sần sật, rắc hành phi thơm phức.',
                'price' => 55000,
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Cơm Gà Xối Mỡ Da Giòn Rụm',
                'description' => 'Đùi gà góc tư chiên xối mỡ vàng ruộm lớp da giòn tan, thịt bên trong mềm mọng, ăn kèm cơm chiên cà chua thơm bùi.',
                'price' => 58000,
                'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bò Kho Bánh Mì Nóng Giòn',
                'description' => 'Nạm gân bò hầm mềm rục cùng cà rốt trong sốt hoa hồi quế thảo quả đậm đà, chấm kèm bánh mì nóng hổi thơm giòn.',
                'price' => 60000,
                'image' => 'https://images.unsplash.com/photo-1547928576-a4a33237cbc3?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bún Đậu Mắm Tôm Thập Cẩm',
                'description' => 'Đậu Mơ chiên vàng giòn vỏ mềm mướt bên trong, chả cốm nóng hổi, thịt bắp chân giò luộc, nem rán, mắm tôm Thanh Hóa đánh bông chanh ớt.',
                'price' => 65000,
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Bún Thịt Nướng Chả Giò Nam Bộ',
                'description' => 'Thịt nạc dăm ướp sả mật ong nướng than hoa thơm nức mũi, chả giò chiên giòn tan, bún tươi, đậu phộng rang giòn chan mắm chua ngọt.',
                'price' => 50000,
                'image' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Hủ Tiếu Nam Vang Khô Thập Cẩm',
                'description' => 'Sợi hủ tiếu dai trộn sốt tương tỏi phi sánh đậm đà, tôm sú tươi, tim gan heo luộc, thịt băm, trứng cút kèm tô súp xương hầm ngọt lịm.',
                'price' => 60000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Cơm Sườn Cốt Lết Sốt Chua Ngọt',
                'description' => 'Miếng sườn cốt lết dày dặn áp chảo xém vàng rim cùng sốt cà chua, hành tây, thơm và ớt chuông chua ngọt kích thích vị giác.',
                'price' => 55000,
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],

            // ================= KHAI VỊ & ĂN VẶT (category_id: 2) =================
            [
                'category_id' => 2,
                'name' => 'Gà Rán Giòn Sốt Phô Mai Cay',
                'description' => 'Đùi và cánh gà giòn tan rụm phủ lớp sốt cay Hàn Quốc ngọt cay hòa quyện và phô mai kéo sợi thơm phức béo ngậy.',
                'price' => 59000,
                'image' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Nem Rán Giòn Hà Nội (6 chiếc)',
                'description' => 'Nhân thịt nạc dăm, miến dong, mộc nhĩ nấm hương bọc vỏ bánh đa nem chiên giòn rụm, chấm nước mắm tỏi ớt chua ngọt.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Khoai Tây Chiên Lắc Phô Mai',
                'description' => 'Khoai tây cắt cọng lớn chiên vàng ráo dầu, lắc đều cùng bột phô mai béo mặn thơm lừng, món ăn vặt được yêu thích.',
                'price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Gỏi Cuốn Tôm Thịt Sốt Tương Bơ (4 cuốn)',
                'description' => 'Tôm sú tươi đỏ au, thịt ba chỉ luộc mỏng, bún tươi và hẹ cuộn bánh tráng dẻo, chấm sốt tương đen bơ đậu phộng rang béo ngậy.',
                'price' => 42000,
                'image' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Chân Gà Rút Xương Sốt Thái Chua Cay',
                'description' => 'Chân gà rút xương giòn sần sật ngâm đẫm sốt me Thái chua cay mặn ngọt nồng nàn, kèm xoài non, cóc xanh và tắc thái lát.',
                'price' => 65000,
                'image' => 'https://images.unsplash.com/photo-1608039829572-78524f79c4c7?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Bánh Tráng Nướng Đà Lạt Trứng Cút Bò Khô',
                'description' => 'Bánh tráng nướng than hoa phết bơ tép khô, hành lá, trứng cút, phô mai béo và bò khô cay xé sợi thơm nức mũi.',
                'price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Mực Chiên Giòn Lắc Muối Tiêu Chanh',
                'description' => 'Mực ống tươi thái khoanh tẩm bột bắp chiên phồng vàng rụm, lắc nhẹ muối tiêu sọ và chấm sốt mayonnaise béo thơm.',
                'price' => 68000,
                'image' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Há Cảo Hấp Tôm Thịt Quảng Đông (6 viên)',
                'description' => 'Vỏ bột lọc mỏng trong veo mềm dẻo, nhân tôm sú tươi giòn sần sật trộn thịt nạc băm, rưới xì dầu tỏi ớt thơm nồng.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],

            // ================= ĐỒ UỐNG & TRÁNG MIỆNG (category_id: 3) =================
            [
                'category_id' => 3,
                'name' => 'Cà Phê Muối Xứ Huế',
                'description' => 'Cà phê phin Robusta Đắk Lắk đậm đà hòa cùng lớp kem sữa muối béo mịn ngọt mặn đan xen độc đáo, đánh thức tỉnh táo.',
                'price' => 32000,
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Cà Phê Trứng Hà Nội',
                'description' => 'Lớp kem trứng đánh bông mịn như mây béo ngậy không tanh hòa quyện cùng cốt cà phê nóng hổi thơm nồng phố cổ.',
                'price' => 38000,
                'image' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Cà Phê Cốt Dừa Đá Xay',
                'description' => 'Cà phê phin nguyên chất thơm nồng hòa quyện cùng cốt dừa tươi Bến Tre đá xay béo ngậy, ngọt dịu mát lạnh sảng khoái.',
                'price' => 42000,
                'image' => 'https://images.unsplash.com/photo-1572442388796-11668ba67e53?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Trà Đào Cam Sả Tươi Mát',
                'description' => 'Trà đen hảo hạng ủ lạnh kết hợp nước cam vắt nguyên chất, sả tươi nồng ấm và những miếng đào ngâm giòn ngọt thanh nhiệt.',
                'price' => 38000,
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Trà Sữa Trân Châu Đường Đen',
                'description' => 'Trà ô long sữa thơm béo hài hòa kết hợp trân châu đen hoàng kim dẻo dai nấu sốt đường đen tự nhiên ngọt thanh.',
                'price' => 38000,
                'image' => 'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Trà Sen Vàng Hạt Dẻ Béo Bùi',
                'description' => 'Cốt trà ô long thanh khiết kết hợp hạt sen Huế ninh mềm ngọt bùi, củ năng giòn mát và lớp váng sữa macchiato mặn dịu.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Trà Mãng Cầu Đắk Lắk Tươi Mát',
                'description' => 'Thịt mãng cầu xiêm tươi dầm chua ngọt tự nhiên hòa cùng trà lài ủ lạnh thơm ngát và thạch dừa giòn sần sật.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Trà Trái Cây Nhiệt Đới Tươi Mát',
                'description' => 'Trà hoa quả ủ lạnh kết hợp chanh leo, dâu tây, cam vàng và dưa lưới tươi ngon mọng nước, giải nhiệt tức thì.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Sinh Tố Bơ Sáp Dừa Non',
                'description' => 'Bơ sáp loại 1 béo ngậy xay nhuyễn mịn cùng sữa đặc, sữa tươi và nước cốt dừa thơm phức, phủ dừa nạo sợi giòn bùi.',
                'price' => 40000,
                'image' => 'https://images.unsplash.com/photo-1638176066666-ffb2f5d08e06?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Nước Ép Cam Cà Rốt Nguyên Chất',
                'description' => 'Ép tươi 100% từ cam sành mọng nước và cà rốt tươi giàu Vitamin A, C, thanh lọc cơ thể không thêm đường hóa học.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Chè Khúc Bạch Hạnh Nhân Sữa Tươi',
                'description' => 'Khúc bạch phô mai mềm mịn núng nính, nhãn lồng giòn ngọt, hạnh nhân lát rang vàng trong nước đường phèn hoa bưởi thanh mát.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Bánh Flan Trứng Sữa Cà Phê Đá',
                'description' => 'Bánh flan mềm mịn núng nính tan ngay trong miệng, hòa quyện sốt caramel đắng nhẹ và cà phê phin đá mát lạnh.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],

            // ================= MÓN CHAY THANH TỊNH (category_id: 4) =================
            [
                'category_id' => 4,
                'name' => 'Cơm Chiên Hạt Sen Nấm Hương Chay',
                'description' => 'Cơm rang tơi xốp hạt vàng ráo dầu cùng hạt sen bùi ngậy, nấm đông cô, cà rốt và đậu Hà Lan thanh mát, đủ dinh dưỡng.',
                'price' => 48000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Salad Bơ Trái Cây Sốt Mè Rang',
                'description' => 'Bơ sáp thái lát, xà lách Romaine, cà chua bi, ngô ngọt và dưa leo giòn rụm trộn sốt mè rang Nhật Bản thơm lừng béo nhẹ.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Nấm Đùi Gà Kho Tiêu Gừng Cay Nồng',
                'description' => 'Nấm đùi gà tươi kho keo đậm vị cùng tiêu sọ cay nồng, ớt hiểm và gừng sợi thơm lừng, món chay cực kỳ bắt cơm.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Hủ Tiếu Xào Nấm Chay Thập Cẩm',
                'description' => 'Sợi hủ tiếu mềm dai xào lửa lớn cùng đậu hũ non chiên giòn, nấm bào ngư, nấm rơm, cà rốt và cải ngọt xanh mướt.',
                'price' => 42000,
                'image' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Đậu Hũ Non Chiên Giòn Tẩm Mỡ Hành',
                'description' => 'Đậu hũ non lướt ván giòn tan lớp vỏ ngoài, bên trong béo ngậy mềm mịn như thạch, rưới đẫm mỡ hành xanh mướt chấm tương ớt.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Canh Rong Biển Đậu Hũ Hạt Sen',
                'description' => 'Rong biển khô thanh mát nấu cùng đậu hũ non thanh khiết, hạt sen hầm bùi ngọt và gừng tươi ấm bụng, thanh lọc cơ thể.',
                'price' => 40000,
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=600&q=80',
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
