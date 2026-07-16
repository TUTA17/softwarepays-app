@extends('core::layouts.admin')
@section('title', 'Thêm vai trò')
@section('content')
    <div class="page-header"><h1 class="page-title">Thêm Vai trò</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Tên vai trò *</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Mô tả</label><input type="text" name="description" class="form-control"></div>
            <div class="form-group">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <label class="form-label" style="margin:0;">Quyền hạn</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="text" id="permSearch" placeholder="🔍 Tìm quyền..." style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;width:200px;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="document.querySelectorAll('.perm-cb').forEach(c=>{if(c.closest('label').style.display!=='none')c.checked=true})">Chọn hết</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="document.querySelectorAll('.perm-cb').forEach(c=>c.checked=false)">Bỏ hết</button>
                    </div>
                </div>
                <div id="permList" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                    @foreach($permissions as $perm)
                        <label class="perm-label" style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-secondary);cursor:pointer;padding:4px 0;">
                            <input type="checkbox" class="perm-cb" name="permissions[]" value="{{ $perm->id }}"> {{ $perm->display_name ?? $perm->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </form>
    </div></div>
@endsection
@push('scripts')
<script>
document.getElementById('permSearch').addEventListener('input',function(){
    var q=this.value.toLowerCase();
    document.querySelectorAll('.perm-label').forEach(function(l){
        l.style.display=l.textContent.toLowerCase().includes(q)?'':'none';
    });
});
</script>
@endpush
