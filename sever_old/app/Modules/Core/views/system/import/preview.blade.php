@extends('core::layouts.admin')

@section('title', 'Xem trước dữ liệu Import')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>Xem trước dữ liệu tải lên</h1>
            <div class="page-breadcrumb">
                <span class="text-gray-500">Dashboard / Hệ thống / Import / Xem trước</span>
            </div>
        </div>
    </div>

    <div class="data-card" style="margin-bottom: 20px;">
        <div class="data-card-header" style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <p style="color: var(--text-muted); font-size: 14px; margin: 0;">
                Hệ thống tìm thấy <strong>{{ count($rows) }}</strong> bản ghi hợp lệ. Vui lòng kiểm tra lại trước khi lưu.
            </p>
            <form action="{{ route('admin.system.import.process') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined">save</span>
                    Lưu dữ liệu vào hệ thống
                </button>
            </form>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg-body); border-bottom: 1px solid var(--border); text-transform: uppercase; font-size: 13px; color: var(--text-muted);">
                        <th style="padding: 12px 16px; text-align: center; width: 50px;">#</th>
                        @foreach($column_keys as $col)
                        <th style="padding: 12px 16px; text-align: left;">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $index => $row)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 16px; text-align: center; color: var(--text-muted); font-weight: 500;">
                            {{ $index + 1 }}
                        </td>
                        @foreach($column_keys as $col)
                        <td style="padding: 12px 16px; color: var(--text-secondary); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $row[$col] ?? '' }}">
                            {{ $row[$col] ?? '' }}
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
