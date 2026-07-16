<?php
namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Theme\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name', 'asc')->paginate(50);
        return view('shop::admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => true
        ]);
        
        return back()->with('success', 'Thêm danh mục thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::findOrFail($id);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active')
        ]);
        
        return back()->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return back()->with('success', 'Xóa danh mục thành công!');
    }

    public function sync()
    {
        // Chạy nền qua queue: bảng products hiện ~97k dòng, chạy đồng bộ trong 1 request HTTP sẽ
        // luôn timeout/hết bộ nhớ và không bao giờ hoàn tất (đây là lý do danh mục cũ bị sai/lỗi thời).
        \App\Jobs\SyncCategoriesJob::dispatch();

        return back()->with('success', 'Đã bắt đầu đồng bộ danh mục theo dữ liệu Kinguin thật ở chế độ nền. Vài phút nữa tải lại trang để xem kết quả.');
    }
}
