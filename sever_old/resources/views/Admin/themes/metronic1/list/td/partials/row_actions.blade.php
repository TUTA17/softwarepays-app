<div class="row-actions" style="    font-size: 13px;">
    <span class="edit" title="ID của bản ghi">{{""}}: {{ @$item->id }} | </span>
    <span class="edit"><a
                href="{{ url('/admin/'.$module['code'].'/edit/' . $item->id) }}"
                title="Sửa bản ghi này">{{""}}</a> | </span>
    <span class=""><a
                href="{{ url('/admin/'.$module['code'].'/' . $item->id . '/duplicate') }}"
                title="Nhân bản bản ghi này">Nhân bản</a> | </span>
    @if(in_array($module['code'] . '_delete', $permissions) || in_array('super_admin', $permissions))
        <span class="trash"><a class="delete-warning"
                               href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}"
                               title="Xóa bản ghi">{{""}}</a> | </span>
    @endif
</div>
