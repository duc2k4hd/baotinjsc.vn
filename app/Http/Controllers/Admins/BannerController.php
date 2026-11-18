<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerStoreRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($keyword = $request->get('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($position = $request->get('position')) {
            $query->where('position', $position);
        }

        if (($status = $request->get('status')) !== null && $status !== '') {
            if ($status === 'active') {
                $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_at')
                            ->orWhere('start_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_at')
                            ->orWhere('end_at', '>=', now());
                    });
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'expired') {
                $query->where('is_active', true)
                    ->whereNotNull('end_at')
                    ->where('end_at', '<', now());
            } else {
                $query->where('is_active', (bool) $status);
            }
        }

        $banners = $query->orderBy('order', 'asc')
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $positions = config('banners.positions', []);
        $positionBadges = config('banners.position_badges', []);

        // Trạng thái cài đặt caption hero slider
        $heroShowCaption = (bool) \App\Models\Setting::getValue('hero_show_caption', false);

        return view('admins.banners.index', compact('banners', 'positions', 'positionBadges', 'heroShowCaption'));
    }

    public function create()
    {
        $banner = new Banner;
        $positions = config('banners.positions', []);

        return view('admins.banners.create', compact('banner', 'positions'));
    }

    public function store(BannerStoreRequest $request)
    {
        $data = $this->preparePayload($request);
        Banner::create($data);

        $this->clearBannerCache();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Táº¡o banner thÃ nh cÃ´ng.');
    }

    public function edit(Banner $banner)
    {
        $positions = config('banners.positions', []);

        return view('admins.banners.edit', compact('banner', 'positions'));
    }

    public function update(BannerUpdateRequest $request, Banner $banner)
    {
        $data = $this->preparePayload($request, $banner);
        $banner->update($data);

        $this->clearBannerCache();

        return redirect()->route('admin.banners.edit', $banner)
            ->with('success', 'Cáº­p nháº­t banner thÃ nh cÃ´ng.');
    }

    public function destroy(Banner $banner)
    {
        $this->deleteImages($banner);
        $banner->delete();

        $this->clearBannerCache();

        return back()->with('success', 'ÄÃ£ xoÃ¡ banner.');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        $this->clearBannerCache();

        return back()->with('success', 'ÄÃ£ cáº­p nháº­t tráº¡ng thÃ¡i banner.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:banners,id',
            'orders.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('orders') as $item) {
            Banner::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        $this->clearBannerCache();

        return response()->json(['success' => true, 'message' => 'ÄÃ£ cáº­p nháº­t thá»© tá»± banner.']);
    }

    private function preparePayload(BannerStoreRequest|BannerUpdateRequest $request, ?Banner $banner = null): array
    {
        $data = $request->validated();

        // Xá»­ lÃ½ images
        if ($request->hasFile('image_desktop')) {
            $data['image_desktop'] = $this->handleImage($request->file('image_desktop'), $banner?->image_desktop);
        } elseif ($banner) {
            // Giá»¯ nguyÃªn image_desktop náº¿u khÃ´ng upload má»›i
            unset($data['image_desktop']);
        }

        if ($request->hasFile('image_mobile')) {
            $data['image_mobile'] = $this->handleImage($request->file('image_mobile'), $banner?->image_mobile);
        } elseif ($banner) {
            // Giá»¯ nguyÃªn image_mobile náº¿u khÃ´ng upload má»›i
            unset($data['image_mobile']);
        }

        // Xá»­ lÃ½ boolean vÃ  defaults
        $data['is_active'] = $request->boolean('is_active', config('banners.defaults.is_active', true));
        $data['target'] = $data['target'] ?? config('banners.defaults.target', '_blank');

        // Xá»­ lÃ½ datetime
        $data['start_at'] = $request->filled('start_at') ? $request->input('start_at') : null;
        $data['end_at'] = $request->filled('end_at') ? $request->input('end_at') : null;

        // Tá»± Ä‘á»™ng set order náº¿u khÃ´ng cÃ³ (chá»‰ khi táº¡o má»›i)
        if (! $banner && (! isset($data['order']) || $data['order'] === null)) {
            $data['order'] = Banner::getNextOrderForPosition($data['position']);
        } elseif (isset($data['order']) && $data['order'] === null) {
            // Náº¿u order lÃ  null khi update, giá»¯ nguyÃªn giÃ¡ trá»‹ cÅ©
            unset($data['order']);
        }

        return $data;
    }

    private function handleImage($file, ?string $current = null): ?string
    {
        if (! $file) {
            return $current;
        }

        $directory = public_path(config('banners.image.path', 'clients/assets/img/banners'));
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = 'banner-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        if ($current) {
            $this->deleteImageFile($current);
        }

        return $filename;
    }

    private function deleteImages(Banner $banner): void
    {
        $this->deleteImageFile($banner->image_desktop);
        $this->deleteImageFile($banner->image_mobile);
    }

    private function deleteImageFile(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path(config('banners.image.path', 'clients/assets/img/banners').'/'.$filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * XÃ³a cache banners_home_parent vÃ  banners_home_children.
     */
    private function clearBannerCache(): void
    {
        Cache::forget('banners_home_parent');
        Cache::forget('banners_home_children');
    }

    /**
     * Bật / tắt nhanh cài đặt hiển thị caption (tiêu đề & mô tả) trên Hero Slider.
     */
    public function toggleCaption(): \Illuminate\Http\RedirectResponse
    {
        $setting = \App\Models\Setting::firstOrCreate(
            ['key' => 'hero_show_caption'],
            [
                'group'       => 'banner',
                'type'        => 'boolean',
                'label'       => 'Hiển thị tiêu đề & mô tả trên Hero Slider',
                'description' => 'Bật để hiện tiêu đề, mô tả và nút CTA lên slider. Tắt để slider chỉ hiển thị ảnh thuần.',
                'value'       => '0',
                'is_public'   => true,
            ]
        );

        $newValue = $setting->getParsedValue() ? '0' : '1';
        $setting->value = $newValue;
        $setting->save();

        Cache::forget('settings');

        $msg = $newValue === '1'
            ? 'Đã BẬT hiển thị tiêu đề & mô tả trên slider.'
            : 'Đã TẮT tiêu đề & mô tả — slider chỉ hiển thị ảnh.';

        return redirect()->route('admin.banners.index')->with('success', $msg);
    }
}
