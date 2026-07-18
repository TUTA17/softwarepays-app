<?php

namespace App\Modules\SoundMeme\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\SoundMeme\Models\SoundCategory;
use App\Modules\SoundMeme\Services\AudioMetadataService;
use App\Modules\SoundMeme\Services\R2StorageService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use App\Modules\Core\Models\Setting;

class SoundController extends Controller
{
    public function __construct(
        protected R2StorageService $r2,
        protected AudioMetadataService $metadata
    ) {
    }

    public function index(Request $request)
    {
        $query = Sound::with('category')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sounds = $query->paginate(20)->withQueryString();
        $sounds->getCollection()->transform(function($s) {
            $s->play_url = $this->r2->getSignedDownloadUrl($s->object_key, 30);
            return $s;
        });

        $publishRate = Setting::getValue('soundmeme_autopublish_rate', 1);

        return view('soundmeme::admin.sounds.index', compact('sounds', 'publishRate'));
    }

    public function create()
    {
        $categories = SoundCategory::orderBy('name')->get();
        return view('soundmeme::admin.sounds.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:sound_categories,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,hidden',
            'audio' => 'required|file|max:' . (config('sound.max_upload_mb') * 1024),
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('audio');
        $this->assertValidAudioFile($file);

        [$objectKey, $meta] = $this->uploadAudio($file);

        $thumbnailKey = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailKey = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $sound = Sound::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Sound::generateUniqueSlug($request->title),
            'description' => $request->description,
            'tags' => $request->tags,
            'object_key' => $objectKey,
            'thumbnail_key' => $thumbnailKey,
            'original_filename' => Str::limit(basename($file->getClientOriginalName()), 200, ''),
            'mime_type' => $meta['mime_type'],
            'extension' => $meta['extension'],
            'duration' => $meta['duration'],
            'bitrate' => $meta['bitrate'],
            'codec' => $meta['codec'],
            'file_size' => $file->getSize(),
            'status' => $request->status,
            'is_featured' => $request->boolean('is_featured'),
            'created_by' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.soundmeme.sounds')->with('success', 'Đã tải lên sound "' . $sound->title . '"!');
    }

    public function edit($id)
    {
        $sound = Sound::findOrFail($id);
        $categories = SoundCategory::orderBy('name')->get();
        $playUrl = $this->r2->getSignedDownloadUrl($sound->object_key, 30);
        return view('soundmeme::admin.sounds.edit', compact('sound', 'categories', 'playUrl'));
    }

    public function update(Request $request, $id)
    {
        $sound = Sound::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:sound_categories,id',
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

        if ($request->title !== $sound->title) {
            $data['slug'] = Sound::generateUniqueSlug($request->title, $sound->id);
        }

        if ($request->hasFile('thumbnail')) {
            $oldThumbnailKey = $sound->thumbnail_key;
            $data['thumbnail_key'] = $this->uploadThumbnail($request->file('thumbnail'));

            if ($oldThumbnailKey) {
                try {
                    $this->r2->deleteObject($oldThumbnailKey);
                } catch (\Throwable $e) {
                    Log::error("Xoá thumbnail cũ thất bại [{$oldThumbnailKey}]: " . $e->getMessage());
                }
            }
        }

        $sound->update($data);

        return redirect()->route('admin.soundmeme.sounds')->with('success', 'Đã cập nhật sound!');
    }

    public function crawl()
    {
        try {
            Artisan::call('soundmeme:crawl');
            return back()->with('success', 'Đã chạy lệnh Crawl thành công. Hãy kiểm tra danh sách bài Nháp (Draft).');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi Crawl: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $sound = Sound::findOrFail($id);
        $sound->update(['status' => Sound::STATUS_APPROVED]);
        return back()->with('success', 'Đã duyệt bài! Bài này sẽ được Cronjob tự động đăng trong thời gian tới.');
    }

    public function saveSettings(Request $request)
    {
        $request->validate(['publish_rate' => 'required|integer|min:0']);
        Setting::setValue('soundmeme_autopublish_rate', $request->publish_rate);
        return back()->with('success', 'Đã lưu cấu hình tự động đăng.');
    }

    public function destroy($id)
    {
        $sound = Sound::findOrFail($id);

        // Xoá file thật trên R2 trước, chỉ xoá bản ghi DB nếu R2 xoá thành công — nếu R2 lỗi,
        // không được âm thầm bỏ qua (ném lỗi ra ngoài, log rõ ràng, giữ nguyên bản ghi DB để retry).
        try {
            if ($this->r2->objectExists($sound->object_key)) {
                $this->r2->deleteObject($sound->object_key);
            }
            if ($sound->thumbnail_key && $this->r2->objectExists($sound->thumbnail_key)) {
                $this->r2->deleteObject($sound->thumbnail_key);
            }
            if ($sound->waveform_key && $this->r2->objectExists($sound->waveform_key)) {
                $this->r2->deleteObject($sound->waveform_key);
            }
        } catch (\Throwable $e) {
            Log::error("Xoá sound #{$sound->id} thất bại khi xoá file trên R2: " . $e->getMessage());
            return back()->with('error', 'Xoá file trên R2 thất bại, bản ghi chưa bị xoá. Vui lòng thử lại hoặc kiểm tra log.');
        }

        $sound->delete();

        return back()->with('success', 'Đã xoá sound!');
    }

    // Kiểm tra nội dung file thật bằng finfo (đọc magic byte), không tin Content-Type client gửi lên.
    protected function assertValidAudioFile(UploadedFile $file): void
    {
        if ($file->getSize() === 0) {
            abort(422, 'File rỗng, không thể tải lên.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        $allowed = config('sound.allowed_mime_types');
        if (!in_array($realMime, $allowed, true)) {
            abort(422, "Định dạng file không hợp lệ (phát hiện: {$realMime}). Chỉ chấp nhận: " . implode(', ', $allowed));
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, config('sound.allowed_extensions'), true)) {
            abort(422, 'Phần mở rộng file không hợp lệ.');
        }
    }

    // Upload audio lên R2 + đọc metadata bằng getID3 từ chính file tạm Laravel đang giữ
    // (không tạo thêm file tạm nào khác, không cần dọn dẹp thủ công).
    protected function uploadAudio(UploadedFile $file): array
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        $ext = strtolower($file->getClientOriginalExtension());
        $uuid = (string) Str::uuid();
        $key = 'sounds/meme/' . now()->format('Y/m') . '/' . $uuid . '.' . $ext;

        $metaInfo = $this->metadata->analyze($file->getRealPath());

        $this->r2->uploadObject($file->getRealPath(), $key, $realMime);

        return [$key, [
            'mime_type' => $realMime,
            'extension' => $ext,
            'duration' => $metaInfo['duration'],
            'bitrate' => $metaInfo['bitrate'],
            'codec' => $metaInfo['codec'],
        ]];
    }

    protected function uploadThumbnail(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $key = 'sounds/thumbnails/' . now()->format('Y/m') . '/' . Str::uuid() . '.' . $ext;

        $this->r2->uploadObject($file->getRealPath(), $key, $file->getMimeType());

        return $key;
    }
}
