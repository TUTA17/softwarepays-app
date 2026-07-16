@if(isset($field['height']))
    <style>
        div#cke_ck_{{ $field['name'] }} .cke_contents {
            height: {{ $field['height'] }} !important;
        }
    </style>
@endif

<textarea id="ck_{{ $field['name'] }}" name="{{ $field['name'] }}"
          {{ isset($field['class']) && str_contains($field['class'], 'require') ? 'required' : '' }}
          placeholder="{{ trans($field['label'] ?? '') }}"
          {!! $field['inner'] ?? '' !!}
          class="form-control {{ $field['class'] ?? '' }}"
          {{ isset($field['disabled']) && $field['disabled'] == 'true' ? 'disabled' : '' }}>{!! old($field['name']) !== null ? old($field['name']) : ($field['value'] ?? '') !!}</textarea>

{{-- SỬA LỖI $errors: Kiểm tra biến tồn tại trước khi gọi --}}
<span class="text-danger">{{ isset($errors) ? $errors->first($field['name']) : '' }}</span>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Kiểm tra CKEDITOR đã được load chưa để tránh lỗi JS
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace("ck_{{ $field['name'] }}", {
                filebrowserBrowseUrl: '{{ route('browser') }}',
                filebrowserImageBrowseUrl: '{{ route("browser") }}?Type=Images',
                // Cập nhật đường dẫn upload chuẩn hơn (dùng / thay vì ..)
                filebrowserUploadUrl: '/ckfinder/connector?command=QuickUpload&type=Files',
                filebrowserImageUploadUrl: '/ckfinder/connector?command=QuickUpload&type=Images',
                filebrowserWindowWidth: '1000',
                filebrowserWindowHeight: '700',
                width: '100%',
                // Fix lỗi chiều cao nếu config height được truyền vào
                @if(isset($field['height']))
                height: '{{ $field['height'] }}',
                @endif
            });
        } else {
            console.warn('CKEditor library is not loaded.');
        }
    });
</script>