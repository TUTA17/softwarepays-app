<?php

namespace App\Modules\Blog\Controllers\Theme;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::whereHas('category', fn($q) => $q->where('slug', '!=', 'huong-dan'))
            ->orderBy('pub_date', 'desc')->paginate(12);
        return view('blog::theme.index', compact('posts'));
    }

    public function guides()
    {
        $posts = BlogPost::whereHas('category', fn($q) => $q->where('slug', 'huong-dan'))
            ->orderBy('pub_date', 'desc')->paginate(12);
        $isGuidePage = true;
        return view('blog::theme.index', compact('posts', 'isGuidePage'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();

        // Bài viết liên quan (cùng danh mục với bài đang xem)
        $related = BlogPost::where('id', '!=', $post->id)
                           ->where('category_id', $post->category_id)
                           ->orderBy('pub_date', 'desc')
                           ->take(4)
                           ->get();

        return view('blog::theme.show', compact('post', 'related'));
    }

    public function fetchCron()
    {
        // 1. Cho phép kịch bản tiếp tục chạy ngầm dù máy khách ngắt kết nối
        ignore_user_abort(true);
        set_time_limit(0);

        // 2. Trả về thông báo JSON cho cron-job.org trước khi chạy tác vụ nặng
        if (function_exists('fastcgi_finish_request')) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Hệ thống đang chạy ngầm để bóc tách tin tức GameHub...'
            ]);
            fastcgi_finish_request();
        }

        // 3. Thực hiện cào dữ liệu trực tiếp (Không dùng hàng đợi Queue nữa)
        \Illuminate\Support\Facades\Artisan::call('news:fetch');

        if (!function_exists('fastcgi_finish_request')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã chạy xong đồng bộ tin tức GameHub.'
            ]);
        }
    }
}
