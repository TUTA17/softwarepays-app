<a href="{{ $item->{$field['name']} }}" target="_blank" style="display: inline-block;
            max-width: 150px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;">
    @if(isset($field['inner']))
        {!! $field['inner'] !!}
    @else
        {!! $item->{$field['name']} !!}
    @endif
</a>