@extends('core::layouts.admin')

@section('title', 'Cấu hình chung')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <span>Cấu hình chung</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Cấu hình chung</h1>
        <p class="page-subtitle">Quản lý thông tin website, email, SEO và hệ thống</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
    @csrf

    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px 20px; background: #e8f5e9; color: #2e7d32; border-radius: var(--radius); margin-bottom: 20px; border: 1px solid #c8e6c9; display: flex; align-items: center; gap: 8px;">
            <span class="material-symbols-outlined" style="font-size: 20px;">check_circle</span>
            <span style="font-size: 13.5px; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="padding: 12px 20px; background: #ffebee; color: #c62828; border-radius: var(--radius); margin-bottom: 20px; border: 1px solid #ffcdd2; display: flex; flex-direction: column; gap: 4px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                <span style="font-size: 13.5px; font-weight: bold;">Có lỗi xảy ra:</span>
            </div>
            <ul style="margin: 4px 0 0 28px; padding: 0; font-size: 13px; list-style-type: disc;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="card" style="margin-bottom: 0; border-bottom: none; border-radius: var(--radius) var(--radius) 0 0;">
        <div class="tab-nav" id="settingTabs">
            @foreach($module['tabs'] as $key => $tab)
                <a href="javascript:void(0)"
                   class="tab-item {{ $loop->first ? 'active' : '' }}"
                   data-tab="{{ $key }}"
                   onclick="switchTab('{{ $key }}', this)">
                    <span class="material-symbols-outlined" style="font-size:18px; vertical-align: middle; margin-right: 4px;">{{ $tab['icon'] ?? 'settings' }}</span>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="card" style="border-radius: 0 0 var(--radius) var(--radius);">
        <div class="card-body">
            @foreach($module['tabs'] as $key => $tab)
                <div class="tab-pane {{ $loop->first ? '' : 'hidden' }}" id="tab-{{ $key }}">
                    <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 16px;">
                        @foreach($tab['td'] as $field)
                            @php
                                $col = $field['col'] ?? 12;
                                $inputName = $key . '_' . $field['name'];
                                $value = $tabs[$key][$field['name']] ?? '';
                            @endphp
                            <div style="grid-column: span {{ $col }};" class="form-group">
                                <label class="form-label">
                                    {{ $field['label'] }}
                                    @if(!empty($field['required']))
                                        <span style="color: var(--danger);">*</span>
                                    @endif
                                </label>

                                @if($field['type'] === 'text')
                                    <input type="text"
                                           name="{{ $inputName }}"
                                           value="{{ old($inputName, $value) }}"
                                           class="form-control"
                                           placeholder="{{ $field['label'] }}">

                                @elseif($field['type'] === 'textarea')
                                    <textarea name="{{ $inputName }}"
                                              class="form-control"
                                              rows="{{ $field['rows'] ?? 3 }}"
                                              placeholder="{{ $field['label'] }}">{{ old($inputName, $value) }}</textarea>

                                @elseif($field['type'] === 'select')
                                    <select name="{{ $inputName }}" class="form-control">
                                        @foreach($field['options'] as $optVal => $optLabel)
                                            <option value="{{ $optVal }}" {{ $value == $optVal ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>

                                @elseif($field['type'] === 'file_image')
                                    <div class="image-upload-box" id="upload-{{ $inputName }}">
                                        <div class="image-preview" id="preview-{{ $inputName }}" style="{{ $value ? '' : 'display:none;' }}">
                                            @if($value)
                                                <img src="{{ asset($value) }}" alt="{{ $field['label'] }}">
                                            @endif
                                            <button type="button" class="image-remove" onclick="removeImage('{{ $inputName }}')" title="Xóa ảnh">
                                                <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                                            </button>
                                        </div>
                                        <label class="image-dropzone" id="dropzone-{{ $inputName }}" style="{{ $value ? 'display:none;' : '' }}">
                                            <span class="material-symbols-outlined" style="font-size:36px; color: var(--primary); opacity:.5;">cloud_upload</span>
                                            <span style="font-size:13px; color: var(--text-muted);">Kéo thả hoặc click để chọn ảnh</span>
                                            <input type="file" name="{{ $inputName }}" accept=".png,.jpg,.jpeg,.gif,.webp,.svg" style="display:none;" onchange="previewImage(this, '{{ $inputName }}')">
                                        </label>
                                        <input type="hidden" name="{{ $inputName }}_current" value="{{ $value }}">
                                        <input type="hidden" name="{{ $inputName }}_delete" value="0" id="delete-{{ $inputName }}">
                                    </div>
                                @endif

                                @if(!empty($field['des']))
                                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">{{ $field['des'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($key === 'currency_tab')
                        <div style="margin-top: 16px; padding: 16px; border-radius: var(--radius); background: var(--bg-body); border: 1px solid var(--border);">
                            <div style="font-size: 12px; font-weight: 600; margin-bottom: 12px;">Ví dụ: sản phẩm giá 100.000đ, khách trả bằng ngoại tệ sẽ thấy giá:</div>
                            <div style="display: flex; flex-wrap: wrap; gap: 24px;">
                                <div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 4px;">Trả bằng USD</div>
                                    <div style="font-size: 18px; font-weight: 700;" id="currency-preview-usd">-</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Không cộng %: <span id="currency-base-usd">-</span></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 4px;">Trả bằng EUR</div>
                                    <div style="font-size: 18px; font-weight: 700;" id="currency-preview-eur">-</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Không cộng %: <span id="currency-base-eur">-</span></div>
                                </div>
                            </div>
                        </div>
                        <script>
                        (function () {
                            // liveUsd/liveEur = tỷ giá thị trường thực, tính theo "1 đơn vị ngoại tệ = ? VNĐ".
                            var liveUsd = {{ (float) ($liveRates['USD'] ?? 0) }};
                            var liveEur = {{ (float) ($liveRates['EUR'] ?? 0) }};
                            var SAMPLE_VND = 100000;
                            var usdInput = document.querySelector('[name="currency_tab_margin_percent_usd"]');
                            var eurInput = document.querySelector('[name="currency_tab_margin_percent_eur"]');
                            var usdOut = document.getElementById('currency-preview-usd');
                            var eurOut = document.getElementById('currency-preview-eur');
                            var usdBase = document.getElementById('currency-base-usd');
                            var eurBase = document.getElementById('currency-base-eur');

                            function fmtMoney(n, symbol) {
                                return symbol + n.toFixed(2);
                            }

                            function recalc() {
                                // Margin dương -> khách trả bằng ngoại tệ phải trả NHIỀU $/€ hơn cho cùng
                                // 1 sản phẩm giá VNĐ. Về mặt tỷ giá, tương đương chia liveRate cho (1+margin)
                                // (site quy đổi 1 USD/EUR ra ÍT VNĐ hơn) — khớp đúng CurrencyHelper::rate().
                                var pUsd = parseFloat(usdInput && usdInput.value) || 0;
                                var pEur = parseFloat(eurInput && eurInput.value) || 0;

                                if (usdOut && liveUsd > 0) {
                                    usdOut.textContent = fmtMoney(SAMPLE_VND / (liveUsd / (1 + pUsd / 100)), '$');
                                    usdBase.textContent = fmtMoney(SAMPLE_VND / liveUsd, '$');
                                }
                                if (eurOut && liveEur > 0) {
                                    eurOut.textContent = fmtMoney(SAMPLE_VND / (liveEur / (1 + pEur / 100)), '€');
                                    eurBase.textContent = fmtMoney(SAMPLE_VND / liveEur, '€');
                                }
                            }

                            if (usdInput) usdInput.addEventListener('input', recalc);
                            if (eurInput) eurInput.addEventListener('input', recalc);
                            recalc();
                        })();
                        </script>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Footer Actions --}}
        <div style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: flex-end; gap: 10px; background: var(--bg-body);">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Quay lại
            </a>
            <button type="submit" class="btn btn-primary">
                <span class="material-symbols-outlined" style="font-size:16px;">save</span> Lưu cấu hình
            </button>
        </div>
    </div>
</form>

<style>
    .tab-pane.hidden { display: none; }
    .tab-nav { flex-wrap: wrap; }
    @media (max-width: 768px) {
        div[style*="grid-column: span 6"] { grid-column: span 12 !important; }
    }
    .image-upload-box { position: relative; }
    .image-dropzone {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px; padding: 24px; border: 2px dashed var(--border); border-radius: var(--radius);
        cursor: pointer; transition: all .2s; background: var(--bg-body); min-height: 120px;
    }
    .image-dropzone:hover, .image-dropzone.drag-over {
        border-color: var(--primary); background: rgba(59,130,246,.04);
    }
    .image-preview {
        position: relative; display: inline-block; border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden; background: var(--bg-body); padding: 8px;
    }
    .image-preview img { max-height: 120px; max-width: 100%; object-fit: contain; display: block; }
    .image-remove {
        position: absolute; top: 4px; right: 4px; width: 24px; height: 24px;
        border-radius: 50%; background: var(--danger); color: #fff; border: none;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,.2); transition: transform .15s;
    }
    .image-remove:hover { transform: scale(1.15); }
</style>

<script>
function switchTab(tabKey, el) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.getElementById('tab-' + tabKey).classList.remove('hidden');
    document.querySelectorAll('#settingTabs .tab-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

function previewImage(input, name) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('preview-' + name);
            var dropzone = document.getElementById('dropzone-' + name);
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">' +
                '<button type="button" class="image-remove" onclick="removeImage(\'' + name + '\')" title="Xóa ảnh">' +
                '<span class="material-symbols-outlined" style="font-size:16px;">close</span></button>';
            preview.style.display = '';
            dropzone.style.display = 'none';
            document.getElementById('delete-' + name).value = '0';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(name) {
    var preview = document.getElementById('preview-' + name);
    var dropzone = document.getElementById('dropzone-' + name);
    preview.style.display = 'none';
    preview.innerHTML = '';
    dropzone.style.display = '';
    // Reset file input
    var fileInput = dropzone.querySelector('input[type=file]');
    if (fileInput) fileInput.value = '';
    document.getElementById('delete-' + name).value = '1';
}

// Drag & drop
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.image-dropzone').forEach(function(zone) {
        var fileInput = zone.querySelector('input[type=file]');
        zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('drag-over'); });
        zone.addEventListener('dragleave', function() { zone.classList.remove('drag-over'); });
        zone.addEventListener('drop', function(e) {
            e.preventDefault(); zone.classList.remove('drag-over');
            if (e.dataTransfer.files.length && fileInput) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    });
});
</script>
@endsection
