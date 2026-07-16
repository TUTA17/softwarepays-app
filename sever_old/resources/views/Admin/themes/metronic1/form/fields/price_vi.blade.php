@php
    $name        = $field['name'];
    // Luôn hiện riêng cho exp_price hoặc cho phép bật qua config 'always_show' => true
    $alwaysShow  = ($name === 'exp_price') || (!empty($field['always_show']));
@endphp

<div class="input-group">
    {{-- Preview p: giữ nguyên điều kiện cũ, NHƯNG ẩn khi alwaysShow để tránh trùng lặp --}}
    @if((!isset($field['show_p_tag']) || $field['show_p_tag'] === true) && !$alwaysShow)
        <p id="input-{{ $name }}" style="color:#000;margin:0;">
            {!! old($name) != null ? nl2br(old($name)) : nl2br(number_format(@$field['value'], 0, '.', '.')) !!}<sup>đ</sup>
        </p>
    @endif

    <input type="text"
           name="{{ $name }}"
           class="form-control {{ @$field['class'] }}"
           id="{{ $name }}" {!! @$field['inner'] !!}

           {{-- CHỈ ẨN khi KHÔNG phải exp_price --}}
           @if(!$alwaysShow && isset($field['value']) && $field['value'] != '') style="display:none;" @endif

           value="{{ old($name) ?? @$field['value'] }}"
            {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>

    <div class="input-group-append"
         @if(!$alwaysShow && isset($field['value']) && $field['value'] != '') style="display:none;" @endif>
        <span class="input-group-text">đ</span>
    </div>

    {{-- JS toggle CHỈ dùng cho field KHÔNG phải exp_price --}}
    @unless($alwaysShow)
        <script>
            $('input#{{ $name }}, #form-group-{{ $name }}').click(function () {
                $('#input-{{ $name }}').hide();
                $('#{{ $name }}').show().click();
            });
        </script>
    @endunless
</div>
