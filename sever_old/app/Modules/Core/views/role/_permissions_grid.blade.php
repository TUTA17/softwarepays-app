@php
    $groups = [];
    $groupLabels = [
        'manage' => '⚙️ Quản lý', 'view' => '👁️ Xem', 'create' => '➕ Tạo',
        'edit' => '✏️ Sửa', 'delete' => '🗑️ Xóa', 'export' => '📤 Xuất',
        'import' => '📥 Nhập', 'approve' => '✅ Duyệt', 'super' => '🔑 Super',
    ];
    foreach ($permissions as $perm) {
        $parts = explode('_', $perm->name, 2);
        $prefix = $parts[0] ?? 'other';
        $groups[$prefix][] = $perm;
    }
    uksort($groups, function($a, $b) use ($groupLabels) {
        return (isset($groupLabels[$a]) ? 0 : 1) - (isset($groupLabels[$b]) ? 0 : 1) ?: strcmp($a, $b);
    });
@endphp

<style>
    .pg { border:1px solid var(--border); border-radius:8px; margin-bottom:8px; overflow:hidden; }
    .pg-h { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:var(--bg-body); cursor:pointer; user-select:none; }
    .pg-h:hover { background:#e8ecf1; }
    .pg-t { font-size:13px; font-weight:600; }
    .pg-c { font-size:11px; color:var(--text-muted); margin-left:6px; }
    .pg-acts { display:flex; gap:6px; align-items:center; }
    .pg-tog { font-size:11px; color:var(--primary); cursor:pointer; padding:2px 8px; border-radius:4px; border:1px solid var(--primary); background:transparent; }
    .pg-tog:hover { background:var(--primary-light); }
    .pg-body { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:2px; padding:8px 12px; }
    .pg-body.collapsed { display:none; }
    .pi { display:flex; align-items:center; gap:6px; padding:5px 8px; border-radius:6px; cursor:pointer; font-size:12px; color:var(--text-secondary); }
    .pi:hover { background:var(--bg-body); }
    .pi input { width:14px; height:14px; accent-color:var(--primary); cursor:pointer; }
    .pi input:checked + span { color:var(--primary); font-weight:500; }
</style>

@foreach($groups as $prefix => $perms)
<div class="pg perm-group">
    <div class="pg-h" onclick="this.nextElementSibling.classList.toggle('collapsed')">
        <div><span class="pg-t">{{ $groupLabels[$prefix] ?? '📋 '.ucfirst($prefix) }}</span><span class="pg-c">{{ count($perms) }}</span></div>
        <div class="pg-acts">
            <button type="button" class="pg-tog" onclick="event.stopPropagation();toggleGroup(this)">Chọn/Bỏ</button>
            <i class="fas fa-chevron-down" style="font-size:11px;color:var(--text-muted);"></i>
        </div>
    </div>
    <div class="pg-body">
        @foreach($perms as $perm)
        <label class="pi perm-item"><input type="checkbox" name="permissions[]" value="{{ $perm->id }}" {{ in_array($perm->id, $checked ?? []) ? 'checked' : '' }}><span>{{ $perm->display_name ?? $perm->name }}</span></label>
        @endforeach
    </div>
</div>
@endforeach

@if(count($permissions) == 0)
<div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px;">
    <i class="fas fa-lock" style="font-size:24px;margin-bottom:8px;"></i><div>Chưa có quyền nào</div>
</div>
@endif
