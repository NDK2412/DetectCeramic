<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ceramic;

class CeramicSeeder extends Seeder
{
    public function run(): void
    {
        $ceramics = [
            [
                'name' => 'Bình gốm họa tiết rồng',
                'description' => 'Bình gốm truyền thống Việt Nam với họa tiết rồng, được vẽ tay với chi tiết tinh xảo.',
                'image' => 'ceramics/vase_dragon.jpg',
                'category' => 'Bình gốm',
                'origin' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đĩa sứ xanh trắng',
                'description' => 'Đĩa sứ xanh trắng cổ điển từ thời nhà Minh, lý tưởng cho các nhà sưu tập.',
                'image' => 'ceramics/blue_white_plate.jpg',
                'category' => 'Đĩa gốm',
                'origin' => 'Trung Quốc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bát trà Raku',
                'description' => 'Bát trà Raku thủ công dùng trong trà đạo Nhật Bản, nổi tiếng với lớp men độc đáo.',
                'image' => 'ceramics/raku_tea_bowl.jpg',
                'category' => 'Bát gốm',
                'origin' => 'Nhật Bản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bình gốm Celadon',
                'description' => 'Bình gốm Celadon với lớp men xanh nhạt, lấy cảm hứng từ kỹ thuật gốm cổ Hàn Quốc.',
                'image' => 'ceramics/celadon_vase.jpg',
                'category' => 'Bình gốm',
                'origin' => 'Hàn Quốc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tượng gốm vẽ tay',
                'description' => 'Tượng gốm hiện đại với thiết kế trừu tượng, được vẽ tay bởi nghệ nhân địa phương.',
                'image' => 'ceramics/sculpture.jpg',
                'category' => 'Tượng gốm',
                'origin' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($ceramics as $ceramic) {
            Ceramic::create($ceramic);
        }
    }
}