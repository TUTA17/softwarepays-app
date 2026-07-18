<?php

namespace App\Modules\GifMeme\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\GifMeme\Models\Gif;
use App\Modules\GifMeme\Models\GifCategory;
use App\Modules\GifMeme\Services\R2StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GifController extends Controller
{
    public function __construct(protected R2StorageService $r2)
    {
    }

    public function index(Request $request)
    {
        $query = Gif::with('category')->where('status', Gif::STATUS_PUBLISHED);

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

        $Gifs = $query->paginate(40)->withQueryString();
        $Gifs->getCollection()->transform(fn ($s) => $this->attachUrls($s));

        $categories = GifCategory::where('status', true)->orderBy('name')->get();

        // Các mục nổi bật (Lựa chọn biên tập viên / Top Meme / Mới nhất) chỉ hiện ở trang chủ mặc
        // định — khi khách đang tìm kiếm/lọc thì ẩn đi, chỉ hiện đúng kết quả khớp bộ lọc, tránh
        // rối mắt với danh sách không liên quan tới cái đang tìm.
        $isBrowsingDefault = !$request->filled('search') && !$request->filled('category') && $request->get('sort', 'newest') === 'newest' && $Gifs->currentPage() === 1;

        $editorsPicks = collect();
        $topMeme = collect();
        $latest = collect();

        if ($isBrowsingDefault) {
            $editorsPicks = Gif::with('category')->where('status', Gif::STATUS_PUBLISHED)->where('is_featured', true)
                ->orderBy('created_at', 'desc')->take(8)->get()->map(fn ($s) => $this->attachUrls($s));

            $topMeme = Gif::with('category')->where('status', Gif::STATUS_PUBLISHED)
                ->orderBy('play_count', 'desc')->take(8)->get()->map(fn ($s) => $this->attachUrls($s));

            $latest = Gif::with('category')->where('status', Gif::STATUS_PUBLISHED)
                ->orderBy('created_at', 'desc')->take(8)->get()->map(fn ($s) => $this->attachUrls($s));
        }

        return view('gifmeme::theme.index', compact('Gifs', 'categories', 'isBrowsingDefault', 'editorsPicks', 'topMeme', 'latest'));
    }

    public function show($slug)
    {
        $Gif = Gif::with('category')->where('slug', $slug)->where('status', Gif::STATUS_PUBLISHED)->firstOrFail();
        $Gif = $this->attachUrls($Gif);

        $related = Gif::where('status', Gif::STATUS_PUBLISHED)
            ->where('id', '!=', $Gif->id)
            ->when($Gif->category_id, fn ($q) => $q->where('category_id', $Gif->category_id))
            ->orderBy('play_count', 'desc')
            ->take(6)
            ->get()
            ->map(fn ($s) => $this->attachUrls($s));

        return view('gifmeme::theme.show', compact('Gif', 'related'));
    }

    // Tăng lượt nghe — chỉ gọi từ frontend sau khi thực sự nghe đủ vài giây (không phải mỗi lần
    // render), và tự giới hạn thêm ở đây theo IP+Gif trong 1 giờ để chống spam gọi API trực tiếp.
    public function play(Request $request, $slug)
    {
        $Gif = Gif::where('slug', $slug)->where('status', Gif::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'Gif_play_' . $Gif->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $Gif->increment('play_count');
            Cache::put($cacheKey, true, now()->addHour());
        }

        return response()->json(['success' => true, 'play_count' => $Gif->play_count]);
    }

    public function download(Request $request, $slug)
    {
        $Gif = Gif::where('slug', $slug)->where('status', Gif::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'Gif_download_' . $Gif->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $Gif->increment('download_count');
            Cache::put($cacheKey, true, now()->addHour());
        }

        $filename = \Illuminate\Support\Str::slug($Gif->title) . '.' . $Gif->extension;
        $url = $this->r2->getSignedDownloadUrl($Gif->object_key, 15, $filename);

        return redirect()->away($url);
    }

    public function like(Request $request, $slug)
    {
        $Gif = Gif::where('slug', $slug)->where('status', Gif::STATUS_PUBLISHED)->firstOrFail();

        $cacheKey = 'Gif_like_' . $Gif->id . '_' . $request->ip();
        if (!Cache::has($cacheKey)) {
            $Gif->increment('like_count');
            Cache::put($cacheKey, true, now()->addYear()); // Khóa 1 năm cho like
        }

        return response()->json(['success' => true, 'like_count' => $Gif->like_count]);
    }

    public function share(Request $request, $slug)
    {
        $Gif = Gif::where('slug', $slug)->where('status', Gif::STATUS_PUBLISHED)->firstOrFail();
        $Gif->increment('share_count');

        return response()->json(['success' => true, 'share_count' => $Gif->share_count]);
    }

    protected function attachUrls(Gif $Gif): Gif
    {
        $Gif->play_url = $this->r2->getSignedDownloadUrl($Gif->object_key, 30);
        $Gif->thumbnail_url = $Gif->thumbnail_key ? $this->r2->getSignedDownloadUrl($Gif->thumbnail_key, 30) : null;
        return $Gif;
    }
}


