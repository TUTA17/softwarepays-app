<?php
if (!function_exists('formatName')) {
    function formatName($str)
    {
        // Tách chuỗi thành các từ
        $words = explode(' ', $str);

        // Đếm số lượng từ
        $wordCount = count($words);

        // Tạo chuỗi kết quả
        $result = '';
        foreach ($words as $index => $word) {
            if ($index < $wordCount - 1) {
                // Nếu không phải từ cuối cùng
                $result .= strtoupper(mb_substr($word, 0, 1)) . ($wordCount > 2 ? '.' : ' ');
            } else {
                // Từ cuối cùng được giữ nguyên
                $result .= $word;
            }
        }

        return $result;
    }
}
    ?>
<a href="/admin/{{ @$field['object'] }}/edit/{{ @$item->{$field['object']}->id }}"
   target="_blank">

    {{ formatName(@$item->{@$field['object']}->{@$field['display_field']}) }}
</a>
@if(isset($field['tooltip_info']))
    <div id="tooltip-info-{{@$field['name']}}" class="div-tooltip_info" data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode(@$field['tooltip_info']) }}"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>
@endif