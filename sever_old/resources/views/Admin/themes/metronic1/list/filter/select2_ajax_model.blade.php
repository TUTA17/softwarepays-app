<?php
// 1. Xử lý lấy giá trị Value (Input > Old > Default)
$value = request()->input($name); // Thay cho $_GET

// Nếu không có trên URL thì lấy từ Old Input hoặc Config mặc định
if (is_null($value)) {
    $value = old($name, $field['value'] ?? null);
}

// Chuẩn hóa về dạng Mảng để xử lý thống nhất
$selectedIds = [];
if (!empty($value)) {
    if (is_array($value) || is_object($value)) {
        foreach ($value as $item) {
            $selectedIds[] = is_object($item) ? $item->id : $item;
        }
    } elseif (is_string($value)) {
        $selectedIds = explode('|', $value);
    } else {
        $selectedIds[] = $value;
    }
}

// 2. Query lấy dữ liệu hiển thị ban đầu (Pre-selected options)
$preSelectedOptions = collect([]);
if (!empty($selectedIds) && isset($field['model'])) {
    try {
        $modelInstance = new $field['model'];
        $preSelectedOptions = $modelInstance->whereIn('id', $selectedIds)->get();
    } catch (\Exception $e) {
        // Xử lý lỗi nếu Model không tồn tại hoặc sai cấu hình
        $preSelectedOptions = collect([]);
    }
}
?>

<select style="width: 100%"
        class="form-control {{ $field['class'] ?? '' }} select2-{{ $name }}"
        id="{{ $name }}"
        {{ isset($field['class']) && str_contains($field['class'], 'require') ? 'required' : '' }}
        name="{{ $name }}{{ isset($field['multiple']) ? '[]' : '' }}"
        {{ isset($field['multiple']) ? 'multiple' : '' }}>

    <option value="">{{ "" }} {{ trans($field['label']) }}</option>

    {{-- Hiển thị các Option đã được chọn trước --}}
    @if($preSelectedOptions->isNotEmpty())
        @foreach ($preSelectedOptions as $v)
            <option selected value="{{ $v->id }}">
                {{ $v->code ?? '' }}
                {{ $v->{$field['display_field']} ?? '' }}
                {{ (isset($field['display_field2']) && isset($v->{$field['display_field2']})) ? ' | ' . $v->{$field['display_field2']} : '' }}
            </option>
        @endforeach
    @endif
</select>

<script>
    $(document).ready(function () {
        $('.select2-{{ $name }}').select2({
            placeholder: "Chọn {{ trans($field['label']) }}",
            allowClear: true,
            ajax: {
                url: "/admin/{{ $field['object'] }}/search-for-select2",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        col: "{{ $field['display_field'] }}",
                        col2: "{{ $field['display_field2'] ?? '' }}",
                        where: "{!! isset($field['where']) ? addslashes($field['where']) : '' !!}", // Fix lỗi JS khi chuỗi có ký tự đặc biệt
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function (item) {
                            return {
                                text: (item.code ? item.code + '' : '') + item.{{ $field['display_field'] }} + (item.{{ $field['display_field2'] ?? 'null' }} ? ' | ' + item.{{ $field['display_field2'] ?? 'null' }} : ''),
                                id: item.id,
                                // Truyền thêm data gốc để dùng trong template
                                original_item: item
                            }
                        }),
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }

            var display_field = repo.{{ $field['display_field'] }} || '';

            @if(isset($field['display_field2']))
            var display_field2 = repo.{{ $field['display_field2'] }} || '';
            var titleAttr = "title='" + display_field2 + "'";
            var content = display_field + " | " + display_field2;
            @else
            var titleAttr = "";
            var content = display_field;
            @endif

            var markup = "<div " + titleAttr + " class='select2-result-repository clearfix'>" + content + "</div>";
            return markup;
        }

        function formatRepoSelection(repo) {
            // Ưu tiên hiển thị text đã format trong processResults
            return repo.text || repo.id;
        }
    });
</script>