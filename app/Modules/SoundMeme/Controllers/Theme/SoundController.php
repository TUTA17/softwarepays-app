<?php

namespace App\Modules\SoundMeme\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\SoundMeme\Models\Sound;
use App\Modules\SoundMeme\Models\SoundCategory;
use App\Modules\SoundMeme\Services\R2StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SoundController extends Controller
{
    public function __construct(protected R2StorageService $r2)
    {
    }

    public function index(Request $request)
    {
        $query = Sound::with('category')->where('status', Sound::STATUS_PUBLISHED);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('tags', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        switch ($request->get('sort', 'newest')) {
            case 'popular':
                $query->orderBy('play_count', 'desc');
                break;
            case 'downloads':
                $query->orderBy('download_count', 'desc');
                break;
            case 'likes':
                $query->orderBy('like_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $sounds = $query->paginate(40)->withQueryString();
        $sounds->getCollection()->transform(fn ($s) => $this->attachUrls($s));

        $categories = SoundCategory::where('status', true)->orderBy('name')->get();

        // Các mục nổi bật (Lựa chọn biên tập viên / Top Meme / Mới nhất) chỉ hiện ở trang chủ mặc
        // định — khi khách đang tìm kiếm/lọc thì ẩn đi, chỉ hiện đúng kết quả khớp bộ lọc, tránh
        // rối mắt với danh sách không liên quan tới cái đang tìm.
        $isBrowsingDefault = !$request->filled('search') && !$request->filled('category') && $request->get('sort', 'newest') === 'newest' && $sounds->currentPage() === 1;

        $editorsPicks = collect();
        $topMeme = collect();
        $latest = collect();

        if ($isBrowsingDefault) {
            $editorsPicks = Sound::with('category')->where('status', Sound::STATUS_PUBLISHED)->where('is_featured', true)
                ->orderBy('created_at', 'desc')->take(8)->get()->map(fn ($s) => $this->attachUrls($s));

            $topMeme = Sound::with('category')->where('status', Sound::STATUS_PUBLISHED)
                ->orderBy('play_count', 'desc')->take(8)->get()->map(fn ($s) => $this->attachUrls($s));

            $latest = Sound::with('category')->where('status', Sound::STATUS_PUBLISHED)
                ->orderBy('created_at', 'desc')->take(8)->get()->map(fn ($s) => $this->attachUrls($s));
        }

        return view('soundmeme::theme.index', compact('sounds', 'categories', 'isBrowsingDefault', 'editorsPicks', 'topMeme', 'latest'));
    }

    public function show($slug)
    {
        $sound = Sound::with('category')->where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();
        $sound = $this->attachUrls($sound);

        $related = Sound::where('status', Sound::STATUS_PUBLISHED)
            ->where('id', '!=', $sound->id)
            ->when($sound->category_id, fn ($q) => $q->where('category_id', $sound->category_id))
            ->orderBy('play_count', 'desc')
            ->take(6)
            ->get()
            ->map(fn ($s) => $this->attachUrls($s));

        return view('soundmeme::theme.show', compact('sound', 'related'));
    }

    // Tăng lượt nghe — chỉ gọi từ frontend sau khi thực sự nghe đủ vài giây (không phải mỗi lần
    // render), và tự giới hạn thêm ở đây theo IP+sound trong 1 giờ để chống spam gọi API trực tiếp.
    public function play(Request $request, $slug)
    {
        $sound = Sound::where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'sound_play_' . $sound->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $sound->increment('play_count');
            Cache::put($cacheKey, true, now()->addHour());
        }

        return response()->json(['success' => true, 'play_count' => $sound->play_count]);
    }

    public function download(Request $request, $slug)
    {
        $sound = Sound::where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'sound_download_' . $sound->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $sound->increment('download_count');
            Cache::put($cacheKey, true, now()->addHour());
        }

        $filename = \Illuminate\Support\Str::slug($sound->title) . '.' . $sound->extension;
        $url = $this->r2->getSignedDownloadUrl($sound->object_key, 15, $filename);

        return redirect()->away($url);
    }

    public function like(Request $request, $slug)
    {
        $sound = Sound::where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'sound_like_' . $sound->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $sound->increment('like_count');
            Cache::put($cacheKey, true, now()->addYear()); // Khóa 1 năm cho like
        }

        return response()->json(['success' => true, 'like_count' => $sound->like_count]);
    }

    public function share(Request $request, $slug)
    {
        $sound = Sound::where('slug', $slug)->where('status', Sound::STATUS_PUBLISHED)->firstOrFail();
        $sound->increment('share_count');

        return response()->json(['success' => true, 'share_count' => $sound->share_count]);
    }

    protected function attachUrls(Sound $sound): Sound
    {
        $sound->play_url = $this->r2->getSignedDownloadUrl($sound->object_key, 30);
        $sound->thumbnail_url = $sound->thumbnail_key ? $this->r2->getSignedDownloadUrl($sound->thumbnail_key, 30) : null;
        return $sound;
    }
}
