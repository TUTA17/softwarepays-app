@php
    //  lấy dữ liệu hiển thị ra
    $model = new $field['model'];
    if(isset($field['where'])) {
        $model = $model->whereRaw($field['where']);
    }

    if (isset($field['orderByRaw'])) {
        $model = $model->orderByRaw($field['orderByRaw']);
    }

    $display_field = isset($field['display_field']) ? $field['display_field'] : 'name';
    $data = $model->orderBy($display_field, 'asc')->pluck($display_field, 'id');

    //  lấy dữ liệu đã chọn
    $value = [];
    if (isset($_GET[$name])) {
        $value = is_array($_GET[$name]) ? $_GET[$name] : [$_GET[$name]];
    } elseif (isset($field['value'])) {
        $value = $field['value'];
    }

@endphp
<select name="{{ $name }}{{ isset($field['multiple']) ? '[]' : '' }}" class="form-control select2-{{ $name }} {{ @$field['class'] }}" {{ isset($field['multiple']) ? 'multiple' : '' }}>
    <option value="">{{""}} {{ trans(@$field['label']) }}</option>
    @foreach ($data as $k => $v)
        <option value="{{ $k }}" {{ in_array($k, $value) ? 'selected':'' }}>{{ $v }}</option>
    @endforeach
</select>
<script>
    $(document).ready(function () {
        $('.select2-{{ $name }}').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
        });
    });
</script>