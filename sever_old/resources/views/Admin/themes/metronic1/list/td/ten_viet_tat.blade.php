<?php
if (isset($item) && isset($field)) {
    $admin_ids = $item->{$field['name']};
}

$admin_ids = is_array($admin_ids) ? $admin_ids : explode('|', $admin_ids);

$admins = \App\Models\Admin::select(['id', 'name', 'image'])->whereIn('id', $admin_ids)->get();
//    dd($admins);
?>
@foreach($admins as $admin)
    {{ strtolower(mb_substr(explode(' ', $admin['name'])[0], 0, 1)) . '.' . strtolower(mb_substr(@explode(' ', $admin['name'])[1], 0, 1)) . ' ' . last(explode(' ', $admin['name'])) }}
@endforeach
