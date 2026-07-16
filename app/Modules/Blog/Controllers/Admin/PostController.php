<?php
namespace App\Modules\Blog\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with('category')->orderBy('pub_date', 'desc');

        if ($request->has('is_auto')) {
            $query->where('is_auto', $request->is_auto);
        }

        $posts = $query->paginate(50);
        return view('blog::admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('blog::admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'summary' => 'nullable|string',
            'content' => 'required|string',
        ]);

        BlogPost::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'summary' => $request->summary,
            'content' => $request->content,
            'image' => $request->image,
            'author' => 'Admin',
            'is_auto' => false,
            'status' => true,
            'pub_date' => Carbon::now()
        ]);

        return redirect()->route('admin.blog.posts')->with('success', 'Đăng bài viết thành công!');
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $categories = BlogCategory::all();
        return view('blog::admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'content' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'summary' => $request->summary,
            'content' => $request->content,
            'image' => $request->image,
        ]);

        return redirect()->route('admin.blog.posts')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();
        return back()->with('success', 'Đã xóa bài viết!');
    }

    // news:fetch tự giới hạn 4 bài/lần để không vượt timeout 30s — chạy trực tiếp (không cần queue
    // job) khi bấm nút thủ công. Cron ngoài (cron-job.org) gọi route /system/sync-news/{token} mỗi
    // 3 tiếng để tự động, khớp cơ chế đã dùng cho sync-categories/sync-stock/sync-prices.
    public function sync()
    {
        set_time_limit(0);
        \Illuminate\Support\Facades\Artisan::call('news:fetch');

        return back()->with('success', 'Đã đồng bộ tin tức: ' . trim(\Illuminate\Support\Facades\Artisan::output()));
    }
}
