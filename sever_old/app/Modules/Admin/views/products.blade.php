@extends('core::layouts.admin')

@section('title', 'Quản lý Game')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Quản lý Game</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Danh sách Game <span class="page-badge">PRODUCTS</span></h1></div>
    </div>

    <!-- Form Thêm Game -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px;">add_circle</span>Thêm Game mới từ Steam</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Steam App ID:</label>
                        <input type="number" name="steam_app_id" required class="form-control" placeholder="Ví dụ: 271590" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Lấy ID từ URL Steam. VD: store.steampowered.com/app/271590</span>
                    </div>
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Giá bán trên Web (VNĐ):</label>
                        <input type="number" name="price" required min="0" class="form-control" placeholder="Ví dụ: 150000" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div style="min-width: 120px;">
                        <button type="submit" class="btn btn-primary" style="height: 38px; width: 100%;">THÊM GAME</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng Danh sách -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tên Game</th>
                        <th>Từ khóa phụ (Aliases)</th>
                        <th>Steam App ID</th>
                        <th>Giá Bán</th>
                        <th>Tồn Kho</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td style="font-weight:600;">{{ $product->id }}</td>
                        <td style="font-weight:500;">
                            <a href="/game/{{ $product->id }}-x" target="_blank" style="color: var(--primary); text-decoration: none;">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td style="font-size:13px;color:var(--text-muted);">
                            {{ $product->aliases ?? '---' }}
                        </td>
                        <td style="font-size:13px;color:var(--text-muted);">{{ $product->steam_app_id }}</td>
                        <td style="font-weight:600; color: #16a34a;">{{ number_format($product->price) }}đ</td>
                        <td>
                            @if($product->available_keys > 0)
                                <span class="badge badge-success">{{ $product->available_keys }} Key</span>
                            @else
                                <span class="badge badge-danger">Hết hàng</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary" style="padding: 4px 8px; font-size: 12px;" onclick="openAliasModal({{ $product->id }}, '{{ htmlspecialchars($product->name, ENT_QUOTES) }}', '{{ htmlspecialchars($product->aliases ?? '', ENT_QUOTES) }}')">
                                <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">edit</span> Cập nhật Từ khóa
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có Game nào trong kho</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Cập nhật Aliases -->
    <div id="aliasModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title" style="margin:0;">Cập nhật Từ khóa tìm kiếm</h3>
                <button type="button" onclick="closeAliasModal()" style="background:none; border:none; cursor:pointer; font-size:20px;">&times;</button>
            </div>
            <div class="card-body">
                <form id="aliasForm" method="POST">
                    @csrf
                    <p style="margin-bottom: 15px; font-size: 14px;">Game: <strong id="aliasGameName"></strong></p>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Các từ khóa (Cách nhau bằng dấu phẩy):</label>
                    <textarea name="aliases" id="aliasInput" class="form-control" rows="3" placeholder="VD: gta, gta5, cướp đường phố..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;"></textarea>
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Khách hàng có thể tìm game này bằng các từ khóa trên.</span>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeAliasModal()" class="btn btn-secondary" style="background:#e5e7eb; color:#374151; border:none; margin-right: 10px;">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAliasModal(id, name, aliases) {
            document.getElementById('aliasGameName').innerText = name;
            document.getElementById('aliasInput').value = aliases;
            document.getElementById('aliasForm').action = "{{ url('admin/products') }}/" + id + "/aliases";
            document.getElementById('aliasModal').style.display = 'flex';
        }

        function closeAliasModal() {
            document.getElementById('aliasModal').style.display = 'none';
        }
    </script>
@endsection
