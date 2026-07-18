@extends('core::layouts.admin')

@section('title', 'Gif Meme')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Gif Meme</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Gif Meme <span class="page-badge">GifS</span></h1></div>
        <div style="display:flex; gap:10px;">
            <button type="button" id="r2-test-btn" class="btn" style="background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">cloud_sync</span> Kiểm tra R2
            </button>
            <a href="{{ route('admin.gifmeme.categories') }}" class="btn" style="background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">category</span> Danh mục
            </a>
              <form action="{{ route('admin.gifmeme.gifs.crawl') }}" method="POST" style="display:inline;" onsubmit="return confirm('Tiến hành thu thập 10 GIF mới nhất từ Tenor?');">
                  @csrf
                  <button type="submit" class="btn" style="background: #10b981; border:none; color: white;">
                      <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">spider</span> Crawl GIF (Tenor)
                  </button>
              </form>
            <a href="{{ route('admin.gifmeme.gifs.create') }}" class="btn btn-primary">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">upload</span> Tải Gif lên
            </a>
        </div>
    </div>
    

    <div id="r2-test-result" style="display:none; margin-bottom: 16px; padding: 14px 18px; border-radius: 8px; border: 1px solid;"></div>

    <script>
        document.getElementById('r2-test-btn').addEventListener('click', function () {
            var btn = this;
            var box = document.getElementById('r2-test-result');
            btn.disabled = true;
            btn.textContent = 'Đang kiểm tra...';
            fetch('{{ route('admin.gifmeme.r2_test') }}', { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    box.style.display = 'block';
                    box.style.background = data.success ? '#ecfdf5' : '#fef2f2';
                    box.style.borderColor = data.success ? '#a7f3d0' : '#fecaca';
                    box.style.color = data.success ? '#047857' : '#b91c1c';
                    box.textContent = data.message;
                })
                .catch(function () {
                    box.style.display = 'block';
                    box.style.background = '#fef2f2';
                    box.style.borderColor = '#fecaca';
                    box.style.color = '#b91c1c';
                    box.textContent = 'Không gọi được endpoint kiểm tra R2.';
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.innerHTML = '<span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">cloud_sync</span> Kiểm tra R2';
                });
        });
    </script>

    @if(session('success'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #ecfdf5; border-color: #a7f3d0; color: #047857;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #fef2f2; border-color: #fecaca; color: #b91c1c;">{{ session('error') }}</div>
    @endif

    <div class="card" style="margin-bottom: 24px; padding: 16px;">
        <form action="{{ route('admin.gifmeme.gifs.settings') }}" method="POST" style="display:flex; gap:10px; align-items:center;">
            @csrf
            <strong style="white-space:nowrap;">Cấu hình Cronjob tự động cào bài (Crawl):</strong>
            <input type="number" name="crawl_rate" value="{{ $crawlRate ?? 10 }}" min="0" class="form-control" style="width:100px;">
            <span style="color:var(--text-muted);">bài được lấy về / 15 phút (0 = tắt)</span>
            <button type="submit" class="btn btn-primary" style="padding:6px 16px;">Lưu Cấu Hình</button>
        </form>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px;">
            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề..." class="form-control" style="flex:1; min-width:200px;">
                <select name="status" class="form-control" style="width:160px;">
                    <option value="">-- Trạng thái --</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp / Chờ duyệt</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã đăng</option>
                    <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
                <button type="submit" class="btn btn-primary">Lọc</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px; padding: 16px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <strong style="white-space:nowrap;">Thao tác hàng loạt (theo bộ lọc đang chọn ở trên):</strong>
        <form action="{{ route('admin.gifmeme.gifs.bulk_approve_all') }}" method="POST" style="display:inline;" onsubmit="return confirm('Duyệt TẤT CẢ Gif đang khớp bộ lọc hiện tại (không chỉ trang này)?');">
            @csrf
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit" class="btn" style="background: #10b981; border:none; color: white;">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">done_all</span> Duyệt tất cả
            </button>
        </form>
        <form action="{{ route('admin.gifmeme.gifs.bulk_delete_all') }}" method="POST" style="display:inline;" onsubmit="return confirm('XOÁ TẤT CẢ Gif đang khớp bộ lọc hiện tại (không chỉ trang này)? File trên R2 cũng sẽ bị xoá, không thể hoàn tác!');">
            @csrf
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit" class="btn" style="background: #b91c1c; border:none; color: white;">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">delete_sweep</span> Xoá tất cả
            </button>
        </form>
    </div>

    <div class="card">
        <div style="padding: 16px; border-bottom: 1px solid var(--border-color); display: none; gap:10px;" id="bulk-action-bar">
            <form action="{{ route('admin.gifmeme.gifs.bulk_approve') }}" method="POST" id="bulk-approve-form" style="display:inline;" onsubmit="return confirm('Duyệt các Gif đã chọn?');">
                @csrf
                <input type="hidden" name="ids_string" class="bulk-ids-input" value="">
                <button type="submit" class="btn" style="background:#10b981; color:white; border:none;">
                    <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">check</span> Duyệt các mục đã chọn (<span class="selected-count">0</span>)
                </button>
            </form>
            <form action="{{ route('admin.gifmeme.gifs.bulk_delete') }}" method="POST" id="bulk-delete-form" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xoá các Gif đã chọn?');">
                @csrf
                <input type="hidden" name="ids_string" class="bulk-ids-input" value="">
                <button type="submit" class="btn" style="background:#b91c1c; color:white; border:none;">
                    <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">delete</span> Xoá các mục đã chọn (<span class="selected-count">0</span>)
                </button>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Kích thước</th>
                        <th>Nghe / Tải</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($Gifs as $Gif)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $Gif->id }}" class="row-checkbox"></td>
                        <td>{{ $Gif->id }}</td>
                        <td style="font-weight:600; color:var(--primary);">
                            {{ $Gif->title }}
                            @if($Gif->is_featured) <span class="badge" style="background:#fef3c7;color:#92400e;">Nổi bật</span> @endif
                        </td>
                        <td style="font-size:13px;">{{ $Gif->category->name ?? '-' }}</td>
                        <td style="font-size:13px;">{{ $Gif->width && $Gif->height ? $Gif->width . 'x' . $Gif->height : '-' }}</td>
                        <td style="font-size:13px;">{{ number_format($Gif->play_count) }} / {{ number_format($Gif->download_count) }}</td>
                        <td>
                            @if($Gif->status === 'published')
                                <span class="badge badge-success">Đã đăng</span>
                            @elseif($Gif->status === 'draft')
                                <span class="badge" style="background:#fef3c7;color:#92400e;">Nháp (Chờ duyệt)</span>
                            @else
                                <span class="badge badge-muted">Đã ẩn</span>
                            @endif
                        </td>
                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                            @if(isset($Gif->play_url))
                                <a href="{{ $Gif->play_url }}" target="_blank" class="btn" style="padding:6px 10px; font-size:12px; background: #e0e7ff; border:none; color: #4338ca;">🖼️ Xem</a>
                            @endif
                            @if($Gif->status === 'draft')
                            <form action="{{ route('admin.gifmeme.gifs.approve', $Gif->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background: #10b981; color:white; border:none;">Duyệt</button>
                            </form>
                            @endif
                            <a href="{{ route('admin.gifmeme.gifs.edit', $Gif->id) }}" class="btn" style="padding:6px 10px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">Sửa</a>
                            <form action="{{ route('admin.gifmeme.gifs.destroy', $Gif->id) }}" method="POST" onsubmit="return confirm('Xoá Gif này? File trên R2 cũng sẽ bị xoá.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color:#b91c1c;">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có Gif nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($Gifs->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $Gifs->links() }}
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var checkAll = document.getElementById('check-all');
            var checkboxes = document.querySelectorAll('.row-checkbox');
            var actionBar = document.getElementById('bulk-action-bar');

            function updateBulkAction() {
                var checked = document.querySelectorAll('.row-checkbox:checked');
                var count = checked.length;
                document.querySelectorAll('.selected-count').forEach(function (el) { el.textContent = count; });
                actionBar.style.display = count > 0 ? 'flex' : 'none';
                checkAll.checked = (count === checkboxes.length && checkboxes.length > 0);

                var ids = Array.from(checked).map(function(cb) { return cb.value; });
                document.querySelectorAll('.bulk-ids-input').forEach(function (el) { el.value = ids.join(','); });
            }

            if (checkAll) {
                checkAll.addEventListener('change', function () {
                    checkboxes.forEach(function (cb) { cb.checked = checkAll.checked; });
                    updateBulkAction();
                });
            }

            checkboxes.forEach(function (cb) {
                cb.addEventListener('change', updateBulkAction);
            });
        });
    </script>
@endsection


