<?php
$model = new $field['model'];

// 1. Xử lý điều kiện lọc (Where)
if (isset($field['where'])) {
    $model = $model->whereRaw($field['where']);
}

// 2. Xử lý lọc theo thuộc tính của bản ghi hiện tại (Dependencies)
if (isset($field['where_attr']) && isset($result) && isset($result->{$field['where_attr']})) {
    $model = $model->where($field['where_attr'], $result->{$field['where_attr']});
}

// 3. Xử lý sắp xếp
if (isset($field['orderByRaw'])) {
    $model = $model->orderByRaw($field['orderByRaw']);
} else {
    $model = $model->orderBy($field['display_field'], 'asc');
}

$data = $model->get();

// 4. Xử lý giá trị đã chọn (Selected Values)
$value = [];

// Trường hợp Multi Select
if (isset($field['multiple'])) {
    // Ưu tiên lấy từ Old Input (khi submit lỗi)
    $oldValue = old($field['name']);
    if ($oldValue) {
        $value = $oldValue;
    }
    // Sau đó lấy từ Database (Edit mode)
    elseif (isset($result) && isset($result->{$field['name']})) {
        if (is_array($result->{$field['name']}) || is_object($result->{$field['name']})) {
            foreach ($result->{$field['name']} as $item) {
                $value[] = is_object($item) ? $item->id : $item;
            }
        } elseif (is_string($result->{$field['name']})) {
            $value = explode('|', $result->{$field['name']});
        }
    }
}
// Trường hợp Single Select
else {
    if (old($field['name']) !== null) {
        $value[] = old($field['name']);
    } elseif (isset($result) && isset($result->{$field['name']})) {
        $value[] = $result->{$field['name']};
    } elseif (isset($field['value'])) {
        $value[] = $field['value'];
    }
}
?>

<select class="form-control {{ $field['class'] ?? '' }} select2-{{ $field['name'] }}"
        id="{{ $field['name'] }}"
        {{ isset($field['class']) && str_contains($field['class'], 'require') ? 'required' : '' }}
        name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}"
        {{ isset($field['multiple']) ? 'multiple' : '' }}
        {!! $field['inner'] ?? '' !!}>

    <option value="">{{ "" }} {{ trans($field['label']) }}</option>

    @foreach ($data as $v)
        <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected' : '' }}>
            {{ $v->{$field['display_field']} }}
            {{ (isset($field['display_field2']) && isset($v->{$field['display_field2']})) ? ' | ' . $v->{$field['display_field2']} : '' }}
        </option>
    @endforeach
</select>

<script>
    $(document).ready(function () {
        $('.select2-{{ $field['name'] }}').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
        });
    });
</script>