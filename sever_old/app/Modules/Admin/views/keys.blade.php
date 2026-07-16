@extends('core::layouts.admin')

@section('title', 'Quản lý Kho Key')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Kho Key</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Kho Keys <span class="page-badge">INVENTORY</span></h1></div>
    </div>

    <!-- Form Nhập Key -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="margin:0; font-size: 15px;"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 5px;">vpn_key</span>Nhập Key mới vào kho</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.keys.store') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Chọn Game:</label>
                        <select name="product_id" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-surface);">
                            <option value="">-- Chọn Game --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div style="margin-top: 16px;">
                            <button type="submit" class="btn btn-primary" style="height: 38px; width: 100%;">NHẬP KHO</button>
                        </div>
                    </div>
                    <div style="flex: 2; min-width: 300px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Danh sách Key (Mỗi dòng 1 key):</label>
                        <textarea name="keys_text" required rows="4" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-family: monospace; background-color: var(--bg-surface); resize: vertical;" placeholder="XXXXX-YYYYY-ZZZZZ&#10;AAAAA-BBBBB-CCCCC"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng Danh sách Key -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Mã Key</th>
                        <th>Game</th>
                        <th>Trạng thái</th>
                        <th>Người Mua</th>
                        <th>Ngày bán</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keys as $key)
                    <tr>
                        <td style="font-family: monospace; font-weight: 600;">{{ $key->key_code }}</td>
                        <td style="font-weight: 500; color: var(--primary);">{{ $key->product->name ?? 'N/A' }}</td>
                        <td>
                            @if($key->status == 'available')
                                <span class="badge badge-success">Chưa bán</span>
                            @else
                                <span class="badge badge-muted">Đã bán</span>
                            @endif
                        </td>
                        <td style="font-size: 13px;">{{ $key->user->name ?? '-' }}</td>
                        <td style="font-size: 13px; color: var(--text-muted);">{{ $key->sold_at ? \Carbon\Carbon::parse($key->sold_at)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">Kho chưa có Key nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($keys->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $keys->links() }}
        </div>
        @endif
    </div>
@endsection
