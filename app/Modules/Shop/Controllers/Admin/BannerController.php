<?php

namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderBy('id')->get();

        return view('shop::admin.banners', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_intl' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'link_url' => 'required|url',
        ]);

        $banner = Banner::create([
            'image_intl' => $this->storeImage($request->file('image_intl'), 'intl'),
            'image' => $request->hasFile('image') ? $this->storeImage($request->file('image'), 'vi') : null,
            'show_vi_image' => $request->boolean('show_vi_image'),
            'link_url' => $request->link_url,
            'sort_order' => (int) Banner::max('sort_order') + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Đã thêm banner mới.');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'link_url' => 'required|url',
            'sort_order' => 'nullable|integer|min:0',
            'image_intl' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $attrs = [
            'link_url' => $request->link_url,
            'sort_order' => $request->sort_order ?? $banner->sort_order,
            'show_vi_image' => $request->boolean('show_vi_image'),
        ];

        if ($request->hasFile('image_intl')) {
            $this->deleteOldImage($banner->image_intl);
            $attrs['image_intl'] = $this->storeImage($request->file('image_intl'), 'intl', $banner->id);
        }

        if ($request->hasFile('image')) {
            $this->deleteOldImage($banner->image);
            $attrs['image'] = $this->storeImage($request->file('image'), 'vi', $banner->id);
        }

        $banner->update($attrs);

        return back()->with('success', 'Đã cập nhật banner.');
    }

    public function toggleActive($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        return back()->with('success', $banner->is_active ? 'Đã bật banner.' : 'Đã tắt banner.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        $this->deleteOldImage($banner->image);
        $this->deleteOldImage($banner->image_intl);
        $banner->delete();

        return back()->with('success', 'Đã xóa banner.');
    }

    protected function storeImage(UploadedFile $file, string $variant, ?int $bannerId = null): string
    {
        $filename = 'banner_' . $variant . '_' . ($bannerId ?: 'new') . '_' . now()->timestamp . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/banners');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $filename);

        return asset('uploads/banners/' . $filename);
    }

    protected function deleteOldImage(?string $imageUrl): void
    {
        if ($imageUrl && str_contains($imageUrl, 'uploads/banners/')) {
            File::delete(public_path('uploads/banners/' . basename($imageUrl)));
        }
    }
}
