<?php
namespace App\Modules\Blog\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::orderBy('order', 'asc')->paginate(50);
        return view('blog::admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => true
        ]);

        return back()->with('success', 'Thêm danh mục thành công!');
    }
}
