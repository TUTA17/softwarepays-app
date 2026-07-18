@extends('core::layouts.admin')

@section('title', 'Sound Meme')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Sound Meme</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Sound Meme <span class="page-badge">SOUNDS</span></h1></div>
        <div style="display:flex; gap:10px;">
            <button type="button" id="r2-test-btn" class="btn" style="background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">cloud_sync</span> Kiểm tra R2
            </button>
            <a href="{{ route('admin.soundmeme.categories') }}" class="btn" style="background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">category</span> Danh mục
            </a>
            <a href="{{ route('admin.soundmeme.sounds.create') }}" class="btn btn-primary">
                <span class="material-symbols-outlined" style="vertical-align:middle; font-size:16px;">upload</span> Tải sound lên
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
            fetch('{{ route('admin.soundmeme.r2_test') }}', { headers: { 'Accept': 'application/json' } })
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

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px;">
            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề..." class="form-control" style="flex:1; min-width:200px;">
                <select name="status" class="form-control" style="width:160px;">
                    <option value="">-- Trạng thái --</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã đăng</option>
                    <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
                <button type="submit" class="btn btn-primary">Lọc</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Thời lượng</th>
                        <th>Nghe / Tải</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sounds as $sound)
                    <tr>
                        <td>{{ $sound->id }}</td>
                        <td style="font-weight:600; color:var(--primary);">
                            {{ $sound->title }}
                            @if($sound->is_featured) <span class="badge" style="background:#fef3c7;color:#92400e;">Nổi bật</span> @endif
                        </td>
                        <td style="font-size:13px;">{{ $sound->category->name ?? '-' }}</td>
                        <td style="font-size:13px;">{{ $sound->duration ? gmdate('i:s', $sound->duration) : '-' }}</td>
                        <td style="font-size:13px;">{{ number_format($sound->play_count) }} / {{ number_format($sound->download_count) }}</td>
                        <td>
                            @if($sound->status === 'published')
                                <span class="badge badge-success">Đã đăng</span>
                            @elseif($sound->status === 'draft')
                                <span class="badge" style="background:#fef3c7;color:#92400e;">Nháp</span>
                            @else
                                <span class="badge badge-muted">Đã ẩn</span>
                            @endif
                        </td>
                        <td style="display:flex; gap:6px;">
                            <a href="{{ route('admin.soundmeme.sounds.edit', $sound->id) }}" class="btn" style="padding:6px 10px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">Sửa</a>
                            <form action="{{ route('admin.soundmeme.sounds.destroy', $sound->id) }}" method="POST" onsubmit="return confirm('Xoá sound này? File trên R2 cũng sẽ bị xoá.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color:#b91c1c;">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có sound nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sounds->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $sounds->links() }}
        </div>
        @endif
    </div>
@endsection
