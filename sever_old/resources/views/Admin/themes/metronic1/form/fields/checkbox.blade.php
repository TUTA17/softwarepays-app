@php
    // Giá trị khi tích (thường là 1)
    $inputValue = $field['input_value'] ?? 1;

    // Mặc định (khi chưa có old): ưu tiên cấu hình 'checked', nếu không thì theo $field['value']
    $defaultChecked = isset($field['checked'])
        ? (bool)$field['checked']
        : (bool)($field['value'] ?? 0);

    // Trạng thái cuối cùng: ưu tiên old('name', $defaultChecked)
    $isChecked = (bool) old($field['name'], $defaultChecked);
@endphp

<label style="cursor: pointer" for="{{ \Illuminate\Support\Str::slug(@$field['label'], '-') }}" class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
    {{-- Hidden để luôn submit 0 khi không check (fix mất trạng thái khi validate fail) --}}
    <input type="hidden" name="{{ @$field['name'] }}" value="0">

    <input style="height: 20px; width: 18px; float: left; margin-right: 5px;"
           type="checkbox"
           name="{{ @$field['name'] }}"
           id="{{ \Illuminate\Support\Str::slug(@$field['label'], '-') }}"
           {!! @$field['inner'] !!}
           value="{{ $inputValue }}"
            {{ $isChecked ? 'checked' : '' }}>
    {{ trans(@$field['label']) }}
    <span></span>
</label>
