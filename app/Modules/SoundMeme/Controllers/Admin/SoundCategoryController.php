<?php

namespace App\Modules\SoundMeme\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\SoundMeme\Models\SoundCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SoundCategoryController extends Controller
{
    public function index()
    {
        $categories = SoundCategory::withCount('sounds')->orderBy('order')->orderBy('name')->get();
        return view('soundmeme::admin.categories', compact('categories'));
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
        while (SoundCategory::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->name) . '-' . $i++;
        }

        SoundCategory::create([
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
        $category = SoundCategory::findOrFail($id);

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
        $category = SoundCategory::findOrFail($id);

        if ($category->sounds()->exists()) {
            return back()->with('error', 'Không thể xoá danh mục đang có sound. Hãy chuyển hoặc xoá sound trước.');
        }

        $category->delete();

        return back()->with('success', 'Đã xoá danh mục!');
    }
}
