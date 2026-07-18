<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\SoundMeme\Services\R2StorageService;
use Illuminate\Support\Str;

// Endpoint tự-kiểm-tra kết nối R2: upload 1 file nhỏ, xác nhận tồn tại, xoá lại — không trả về
// bất kỳ credential nào, chỉ trả success/message để admin xác nhận cấu hình .env đúng trước khi
// bật tính năng Sound Meme cho người dùng thật.
class R2TestController extends Controller
{
    public function test(R2StorageService $r2)
    {
        $key = 'sounds/temp/r2-self-test-' . Str::uuid() . '.txt';
        $tmpPath = tempnam(sys_get_temp_dir(), 'r2test');
        file_put_contents($tmpPath, 'SoftwarePays R2 self-test ' . now()->toDateTimeString());

        try {
            $r2->uploadObject($tmpPath, $key, 'text/plain');

            if (!$r2->objectExists($key)) {
                return response()->json(['success' => false, 'message' => 'Upload xong nhưng không thấy object tồn tại trên R2.'], 500);
            }

            $r2->deleteObject($key);

            if ($r2->objectExists($key)) {
                return response()->json(['success' => false, 'message' => 'Xoá object test thất bại, object vẫn còn trên R2.'], 500);
            }

            return response()->json(['success' => true, 'message' => 'Kết nối R2 hoạt động bình thường: upload, kiểm tra tồn tại, và xoá đều thành công.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi kết nối R2: ' . $e->getMessage()], 500);
        } finally {
            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }
        }
    }
}
