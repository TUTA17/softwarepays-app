@extends('core::layouts.admin')

@section('title', 'Xử lý Đơn Hàng')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator">/</span><span>Đơn Hàng</span>
@endsection

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Đơn Hàng <span class="page-badge">ORDERS</span></h1></div>
    </div>

    @if(session('success'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #ecfdf5; border-color: #a7f3d0; color: #047857;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="card" style="margin-bottom: 16px; padding: 14px 18px; background: #fef2f2; border-color: #fecaca; color: #b91c1c;">{{ session('error') }}</div>
    @endif

    <div class="card" style="margin-bottom: 24px; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="margin: 0 0 4px; font-size: 14px; font-weight: 700;">Chế độ xử lý đơn hàng (Game/Giftcard/Subscription/Software/Thẻ nạp/eSIM/MXH)</h3>
            <p style="margin: 0; font-size: 12px; color: var(--text-muted);">
                Game/Giftcard/Subscription/Software: chỉ áp dụng khi kho Key trống. Thẻ nạp/eSIM/MXH: áp dụng cho mọi đơn (không có kho sẵn).
                Ở chế độ Tự động, nếu API đối tác lỗi thì đơn cũng tự chuyển sang "Chờ xử lý" bên dưới thay vì huỷ.
            </p>
        </div>
        <form action="{{ route('admin.orders.toggle_mode') }}" method="POST" style="display: flex; gap: 8px; align-items: center;">
            @csrf
            <button type="submit" name="mode" value="manual"
                    class="btn {{ $fulfillmentMode === 'manual' ? 'btn-primary' : '' }}"
                    style="{{ $fulfillmentMode === 'manual' ? '' : 'background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--text-primary);' }}">
                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 16px;">pan_tool</span> Thủ công
            </button>
            <button type="submit" name="mode" value="auto"
                    class="btn {{ $fulfillmentMode === 'auto' ? 'btn-primary' : '' }}"
                    style="{{ $fulfillmentMode === 'auto' ? '' : 'background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--text-primary);' }}"
                    onclick="return confirm('Bật chế độ TỰ ĐỘNG: hệ thống sẽ tự gọi API mua Key thật ngay khi khách thanh toán, không qua duyệt tay. Xác nhận?');">
                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 16px;">bolt</span> Tự động
            </button>
        </form>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            @php
                $filters = [
                    'all' => 'Tất cả',
                    'pending_manual' => 'Chờ xử lý (' . $pendingCount . ')',
                    'processing' => 'Đang xử lý',
                    'sold' => 'Đã giao',
                    'failed' => 'Thất bại',
                ];
            @endphp
            @foreach($filters as $key => $label)
                <a href="{{ route('admin.orders', ['status' => $key]) }}"
                   class="btn {{ $status === $key ? 'btn-primary' : '' }}"
                   style="{{ $status === $key ? '' : 'background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--text-primary);' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sản phẩm / Dịch vụ</th>
                        <th>Khách hàng</th>
                        <th>Trạng thái</th>
                        <th>Người xử lý</th>
                        <th>Thời gian</th>
                        <th>Key / Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $row)
                    @php $order = $row['model']; $buyer = $row['buyer']; @endphp
                    <tr>
                        @if($row['type'] === 'smm')
                            <td style="font-weight: 600;">#{{ $order->id }} <span class="badge badge-muted" style="font-size:10px;">MXH</span></td>
                            <td style="font-weight: 500; color: var(--primary); max-width: 240px;">{{ $order->service_name }}</td>
                            <td style="font-size: 13px;">
                                {{ $order->user->name ?? '-' }}
                                @if($order->user)<br><span style="color: var(--text-muted);">{{ $order->user->email }}</span>@endif
                            </td>
                            <td>
                                @php
                                    $smmStatusLower = strtolower($order->status);
                                    $smmStatusColor = match(true) {
                                        str_contains($smmStatusLower, 'complet') => 'background:#dcfce7;color:#15803d;',
                                        str_contains($smmStatusLower, 'progress') => 'background:#dbeafe;color:#1e40af;',
                                        str_contains($smmStatusLower, 'partial') => 'background:#fef3c7;color:#92400e;',
                                        str_contains($smmStatusLower, 'cancel') => 'background:#fee2e2;color:#b91c1c;',
                                        default => 'background:#fef3c7;color:#92400e;',
                                    };
                                @endphp
                                <span class="badge" style="{{ $smmStatusColor }}">{{ $order->status }}</span>
                            </td>
                            <td style="font-size: 13px; color: var(--text-muted);">-</td>
                            <td style="font-size: 13px; color: var(--text-muted);">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td style="min-width: 200px;">
                                <div style="font-size:12px; margin-bottom:6px;">SL: {{ number_format($order->quantity) }} &middot; {{ number_format($order->charge) }}đ</div>
                                @if($order->api_order_id)
                                    <form action="{{ route('admin.smm_orders.refresh', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn" style="padding:6px 10px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">Cập nhật trạng thái</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.smm_orders.send_to_api', $order->id) }}" method="POST" onsubmit="return confirm('Gửi đơn này qua API nhà cung cấp SMM ngay?');">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="padding:6px 10px; font-size:12px;">Gửi qua API</button>
                                    </form>
                                @endif
                            </td>
                        @else
                            <td style="font-weight: 600;">#{{ $order->id }}</td>
                            <td style="font-weight: 500; color: var(--primary);">{{ $order->product->name ?? 'N/A' }}</td>
                            <td style="font-size: 13px;">
                                {{ $buyer->name ?? '-' }}
                                @if($buyer)<br><span style="color: var(--text-muted);">{{ $buyer->email }}</span>@endif
                            </td>
                            <td>
                                @if($order->status === 'pending_manual')
                                    <span class="badge" style="background:#fef3c7;color:#92400e;">Chờ xử lý</span>
                                @elseif($order->status === 'sold')
                                    <span class="badge badge-success">Đã giao</span>
                                @elseif($order->status === 'processing')
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">Đang xử lý</span>
                                @elseif($order->status === 'failed')
                                    <span class="badge" style="background:#fee2e2;color:#b91c1c;" title="{{ $order->error_message }}">Thất bại</span>
                                @else
                                    <span class="badge badge-muted">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td style="font-size: 13px;">
                                @if($order->assignedAdmin)
                                    @if($order->assignedAdmin->id === $currentAdmin->id)
                                        <span class="badge" style="background:#dbeafe;color:#1e40af;">Bạn đang xử lý</span>
                                    @else
                                        {{ $order->assignedAdmin->name }}
                                    @endif
                                @else
                                    <span style="color: var(--text-muted);">Chưa nhận</span>
                                @endif
                            </td>
                            <td style="font-size: 13px; color: var(--text-muted);">{{ $order->sold_at ? \Carbon\Carbon::parse($order->sold_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td style="min-width: 280px;">
                                @php
                                    $lockedByOther = $order->assigned_admin_id && $order->assigned_admin_id != $currentAdmin->id;
                                    $canAct = !$lockedByOther || $canManageAll;
                                @endphp
                                @if($order->status === 'pending_manual')
                                    @if(!$order->assigned_admin_id)
                                        <form action="{{ route('admin.orders.claim', $order->id) }}" method="POST" style="margin-bottom:6px;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="padding:6px 12px; font-size:12px;">Nhận đơn</button>
                                        </form>
                                    @elseif($lockedByOther && !$canManageAll)
                                        <span style="font-size:12px; color: var(--text-muted);">Đơn đã được <strong>{{ $order->assignedAdmin->name ?? 'người khác' }}</strong> nhận xử lý.</span>
                                    @endif

                                    @if($canAct && $order->assigned_admin_id)
                                        <form action="{{ route('admin.orders.fulfill_manual', $order->id) }}" method="POST" style="display:flex; flex-direction:column; gap:6px; margin-bottom:6px;">
                                            @csrf
                                            <input type="text" name="key_code" required placeholder="Nhập key/nội dung giao cho khách" class="form-control" style="padding:6px 10px; border:1px solid var(--border-color); border-radius:6px; font-family:monospace; font-size:12px;">
                                            <input type="text" name="note" placeholder="Ghi chú (không bắt buộc)" class="form-control" style="padding:6px 10px; border:1px solid var(--border-color); border-radius:6px; font-size:12px;">
                                            <button type="submit" class="btn btn-primary" style="padding:6px 12px; font-size:12px; white-space:nowrap;">Giao tay</button>
                                        </form>
                                        <form action="{{ route('admin.orders.fulfill_api', $order->id) }}" method="POST" onsubmit="return confirm('Gọi API nhà cung cấp để mua key thật cho đơn này?');" style="margin-bottom:6px;">
                                            @csrf
                                            <button type="submit" class="btn" style="padding:6px 12px; font-size:12px; background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">Lấy key qua API</button>
                                        </form>
                                    @endif

                                    @if($lockedByOther && $canManageAll)
                                        <form action="{{ route('admin.orders.release', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn" style="padding:4px 10px; font-size:11px; background: var(--bg-surface); border:1px solid var(--border-color); color: var(--text-primary);">Bỏ nhận</button>
                                        </form>
                                    @endif
                                @else
                                    <span style="font-family:monospace; font-size:12px; word-break:break-all;">{{ $order->key_code ?? '-' }}</span>
                                    @if($order->note)
                                        <div style="font-size:12px; color: var(--text-muted); margin-top:4px;">Ghi chú: {{ $order->note }}</div>
                                    @endif
                                @endif
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">Không có đơn hàng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div style="padding: 16px; border-top: 1px solid var(--border-color);">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
@endsection
