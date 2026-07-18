<?php

namespace App\Modules\GifMeme\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\GifMeme\Models\GifCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GifCategoryController extends Controller
{
    public function index()
    {
        $categories = GifCategory::withCount('Gifs')->orderBy('order')->orderBy('name')->get();
        return view('gifmeme::admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $slug = Str::slug($request->name);
        $i = 2;
        while (GifCategory::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->name) . '-' . $i++;
        }

        GifCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => true,
        ]);

        return back()->with('success', 'Đã thêm danh mục!');
    }

    public function update(Request $request, $id)
    {
        $category = GifCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->has('status'),
        ]);

        return back()->with('success', 'Đã cập nhật danh mục!');
    }

    public function destroy($id)
    {
        $category = GifCategory::findOrFail($id);

        if ($category->Gifs()->exists()) {
            return back()->with('error', 'Không thể xoá danh mục đang có Gif. Hãy chuyển hoặc xoá Gif trước.');
        }

        $category->delete();

        return back()->with('success', 'Đã xoá danh mục!');
    }
}


