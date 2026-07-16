@extends('core::layouts.admin')

@section('title', 'Quản lý Giftcard & Thẻ nạp')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Quản lý Thẻ Nạp</span>
@endsection

@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div><h1 class="page-title">Danh sách Thẻ Nạp (Kinguin) <span class="page-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">GIFTCARDS</span></h1></div>
        <form action="{{ route('admin.giftcards.sync') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-primary" style="background-color: #0ea5e9; border-color: #0ea5e9;">
                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px;">sync</span> Đồng bộ Thẻ từ Kinguin
            </button>
        </form>
    </div>

    <!-- Form Thêm Sản phẩm Thủ công (Wallet, Giftcard, etc.) -->
    <div class="card" style="margin-bottom: 24px; border-top: 3px solid #10b981;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px; color: #10b981;">card_giftcard</span>Thêm Thẻ Wallet / Giftcard (Kinguin)</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.manual_store') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 15px;">
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Tên Sản Phẩm:</label>
                        <input type="text" name="name" required class="form-control" placeholder="VD: Steam Wallet 50.000 VNĐ" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Kinguin Product ID:</label>
                        <input type="text" name="wholesale_product_id" required class="form-control" placeholder="VD: 627e1f402e861d00010bxxxx" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">ID sản phẩm lấy từ trang Kinguin API.</span>
                    </div>
                </div>
                <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Giá bán trên Web (VNĐ):</label>
                        <input type="number" name="price" required min="0" class="form-control" placeholder="VD: 55000" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Link Ảnh (Tùy chọn):</label>
                        <input type="url" name="header_image" class="form-control" placeholder="https://..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    <div style="min-width: 120px; display: flex; align-items: flex-end; padding-top: 24px;">
                        <button type="submit" class="btn btn-success" style="height: 38px; width: 100%; background-color: #10b981; border-color: #10b981;">THÊM SẢN PHẨM</button>
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
                        <th>Tên Sản Phẩm</th>
                        <th>Từ khóa phụ (Aliases)</th>
                        <th>Kinguin Product ID</th>
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
                        <td style="font-size:13px;color:var(--text-muted);">
                            <span class="badge badge-success" style="font-size:10px;">KINGUIN: {{ $product->wholesale_product_id }}</span>
                        </td>
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
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">Chưa có Giftcard nào trong kho</td></tr>
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
                    <p style="margin-bottom: 15px; font-size: 14px;">Sản phẩm: <strong id="aliasGameName"></strong></p>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Các từ khóa (Cách nhau bằng dấu phẩy):</label>
                    <textarea name="aliases" id="aliasInput" class="form-control" rows="3" placeholder="VD: steam wallet, the nap steam..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;"></textarea>
                    
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
