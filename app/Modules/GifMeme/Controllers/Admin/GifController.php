<?php

namespace App\Modules\GifMeme\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\GifMeme\Models\Gif;
use App\Modules\GifMeme\Models\GifCategory;
use App\Modules\GifMeme\Services\ImageMetadataService;
use App\Modules\GifMeme\Services\R2StorageService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use App\Modules\Core\Models\Setting;

class GifController extends Controller
{
    public function __construct(
        protected R2StorageService $r2,
        protected ImageMetadataService $metadata
    ) {
    }

    public function index(Request $request)
    {
        $Gifs = $this->filteredQuery($request)->paginate(20)->withQueryString();
        $Gifs->getCollection()->transform(function($s) {
            $s->play_url = $this->r2->getSignedDownloadUrl($s->object_key, 30);
            return $s;
        });

        $rateSetting = Setting::where('name', 'GifMeme_autocrawl_rate')->first();
        $crawlRate = $rateSetting ? $rateSetting->value : 10;

        return view('gifmeme::admin.gifs.index', compact('Gifs', 'crawlRate'));
    }

    public function create()
    {
        $categories = GifCategory::orderBy('name')->get();
        return view('gifmeme::admin.gifs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:gif_categories,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,hidden',
            'image' => 'required|file|max:' . (config('gif.max_upload_mb') * 1024),
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('image');
        $this->assertValidimageFile($file);

        [$objectKey, $meta] = $this->uploadimage($file);

        $thumbnailKey = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailKey = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $Gif = Gif::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Gif::generateUniqueSlug($request->title),
            'description' => $request->description,
            'tags' => $request->tags,
            'object_key' => $objectKey,
            'thumbnail_key' => $thumbnailKey,
            'original_filename' => Str::limit(basename($file->getClientOriginalName()), 200, ''),
            'mime_type' => $meta['mime_type'],
            'extension' => $meta['extension'],
            'width' => $meta['width'],
            'height' => $meta['height'],
            'file_size' => $file->getSize(),
            'status' => $request->status,
            'is_featured' => $request->boolean('is_featured'),
            'created_by' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.GifMeme.Gifs')->with('success', 'Đã tải lên Gif "' . $Gif->title . '"!');
    }

    public function edit($id)
    {
        $Gif = Gif::findOrFail($id);
        $categories = GifCategory::orderBy('name')->get();
        $playUrl = $this->r2->getSignedDownloadUrl($Gif->object_key, 30);
        return view('gifmeme::admin.gifs.edit', compact('Gif', 'categories', 'playUrl'));
    }

    public function update(Request $request, $id)
    {
        $Gif = Gif::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:gif_categories,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,hidden',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'status' => $request->status,
            'is_featured' => $request->boolean('is_featured'),
        ];

        if ($request->title !== $Gif->title) {
            $data['slug'] = Gif::generateUniqueSlug($request->title, $Gif->id);
        }

        if ($request->hasFile('thumbnail')) {
            $oldThumbnailKey = $Gif->thumbnail_key;
            $data['thumbnail_key'] = $this->uploadThumbnail($request->file('thumbnail'));

            if ($oldThumbnailKey) {
                try {
                    $this->r2->deleteObject($oldThumbnailKey);
                } catch (\Throwable $e) {
                    Log::error("Xoá thumbnail cũ thất bại [{$oldThumbnailKey}]: " . $e->getMessage());
                }
            }
        }

        $Gif->update($data);

        return redirect()->route('admin.GifMeme.Gifs')->with('success', 'Đã cập nhật Gif!');
    }

    public function crawl()
    {
        try {
            Artisan::call('gifmeme:crawl');
            $output = Artisan::output();
            return back()->with('success', "Crawl xong. Log: <br><pre>{$output}</pre>");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi Crawl: ' . $e->getMessage());
        }
    }

    public function crawlImages(Request $request)
    {
        try {
            set_time_limit(0); // Tránh timeout cho request lâu
            $reset = $request->has('reset');
            $params = ['--limit' => 10]; // Lấy 10 bài mỗi lần để duyệt nhanh không timeout
            if ($reset) {
                $params['--reset'] = true;
            }
            Artisan::call('GifMeme:crawl-images', $params);
            $output = Artisan::output();
            
            if ($request->ajax() || $request->wantsJson()) {
                $isDone = str_contains($output, 'Không có Gif mới nào cần crawl');
                return response()->json(['success' => true, 'output' => $output, 'done' => $isDone]);
            }
            
            return back()->with('success', "Crawl Ảnh xong. Log: <br><pre>{$output}</pre>");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()]);
            }
            return back()->with('error', 'Lỗi khi Crawl Ảnh: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $Gif = Gif::findOrFail($id);
        $Gif->update(['status' => Gif::STATUS_PUBLISHED]);
        return back()->with('success', 'Đã duyệt bài thành công! Bài viết đã hiển thị trên web.');
    }

    public function saveSettings(Request $request)
    {
        $request->validate(['crawl_rate' => 'required|integer|min:0']);
        Setting::updateOrCreate(
            ['name' => 'GifMeme_autocrawl_rate'],
            ['value' => $request->crawl_rate, 'type' => 'GifMeme']
        );
        return back()->with('success', 'Đã lưu cấu hình tự động lấy bài.');
    }

    public function destroy($id)
    {
        $Gif = Gif::findOrFail($id);

        if ($Gif->object_key) $this->r2->deleteObject($Gif->object_key);
        if ($Gif->thumbnail_key) $this->r2->deleteObject($Gif->thumbnail_key);

        $Gif->delete();

        return back()->with('success', 'Đã xoá Gif!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids_string' => 'required|string'
        ]);

        $ids = explode(',', $request->ids_string);

        $Gifs = Gif::whereIn('id', $ids)->get();
        foreach ($Gifs as $Gif) {
            if ($Gif->object_key) $this->r2->deleteObject($Gif->object_key);
            if ($Gif->thumbnail_key) $this->r2->deleteObject($Gif->thumbnail_key);
            $Gif->delete();
        }

        return back()->with('success', 'Đã xoá ' . $Gifs->count() . ' Gif thành công.');
    }

    // Duyệt các Gif đã tick chọn (checkbox) — cùng cơ chế ids_string với bulkDelete ở trên.
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids_string' => 'required|string'
        ]);

        $ids = explode(',', $request->ids_string);
        $count = Gif::whereIn('id', $ids)->update(['status' => Gif::STATUS_PUBLISHED]);

        return back()->with('success', "Đã duyệt {$count} Gif thành công.");
    }

    // Duyệt TẤT CẢ Gif đang khớp bộ lọc hiện tại (tìm kiếm/trạng thái trên thanh lọc phía trên),
    // không giới hạn theo trang — dùng khi muốn duyệt sạch cả trăm bài Nháp cùng lúc.
    public function bulkApproveAll(Request $request)
    {
        $count = $this->filteredQuery($request)->update(['status' => Gif::STATUS_PUBLISHED]);

        return back()->with('success', "Đã duyệt tất cả {$count} Gif khớp bộ lọc hiện tại.");
    }

    // Xoá TẤT CẢ Gif đang khớp bộ lọc hiện tại — phải xoá từng file trên R2 trước (không thể
    // xoá hàng loạt bằng 1 câu SQL vì còn phải dọn file trên R2), nên duyệt qua từng bản ghi.
    public function bulkDeleteAll(Request $request)
    {
        $Gifs = $this->filteredQuery($request)->get();

        foreach ($Gifs as $Gif) {
            if ($Gif->object_key) $this->r2->deleteObject($Gif->object_key);
            if ($Gif->thumbnail_key) $this->r2->deleteObject($Gif->thumbnail_key);
            $Gif->delete();
        }

        return back()->with('success', 'Đã xoá tất cả ' . $Gifs->count() . ' Gif khớp bộ lọc hiện tại.');
    }

    protected function filteredQuery(Request $request)
    {
        $query = Gif::with('category')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    // Kiểm tra nội dung file thật bằng finfo (đọc magic byte), không tin Content-Type client gửi lên.
    protected function assertValidimageFile(UploadedFile $file): void
    {
        if ($file->getSize() === 0) {
            abort(422, 'File rỗng, không thể tải lên.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        $allowed = config('gif.allowed_mime_types');
        if (!in_array($realMime, $allowed, true)) {
            abort(422, "Định dạng file không hợp lệ (phát hiện: {$realMime}). Chỉ chấp nhận: " . implode(', ', $allowed));
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, config('gif.allowed_extensions'), true)) {
            abort(422, 'Phần mở rộng file không hợp lệ.');
        }
    }

    // Upload image lên R2 + đọc metadata bằng getID3 từ chính file tạm Laravel đang giữ
    // (không tạo thêm file tạm nào khác, không cần dọn dẹp thủ công).
    protected function uploadimage(UploadedFile $file): array
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        $ext = strtolower($file->getClientOriginalExtension());
        $uuid = (string) Str::uuid();
        $key = 'Gifs/meme/' . now()->format('Y/m') . '/' . $uuid . '.' . $ext;

        $metaInfo = $this->metadata->analyze($file->getRealPath());

        $this->r2->uploadObject($file->getRealPath(), $key, $realMime);

        return [$key, [
            'mime_type' => $realMime,
            'extension' => $ext,
            'width' => $metaInfo['width'],
            'height' => $metaInfo['height']
        ]];
    }

    protected function uploadThumbnail(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $key = 'Gifs/thumbnails/' . now()->format('Y/m') . '/' . Str::uuid() . '.' . $ext;

        $this->r2->uploadObject($file->getRealPath(), $key, $file->getMimeType());

        return $key;
    }
}


