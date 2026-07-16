<!DOCTYPE html>
<html class="light" lang="vi">
@include('core::layouts.partials.head')
<body>
@php
    $siteLogo = \App\Modules\Core\Models\Setting::where('name','logo')->where('type','general_tab')->value('value');
    $siteName = \App\Modules\Core\Models\Setting::where('name','name')->where('type','general_tab')->value('value') ?: config('app.name');
@endphp
        <!-- Sidebar -->
@include('core::layouts.partials.sidebar')

<!-- Main Content -->
<div class="main-content">
    <!-- Top Header -->
    @include('core::layouts.partials.header')

    <!-- Page Content -->
    <div class="page-content">
        @if(session('success'))
            <script>document.addEventListener('DOMContentLoaded', () => showPopup('success', '{!! addslashes(session("success")) !!}'));</script>
        @endif
        @if(session('error'))
            <script>document.addEventListener('DOMContentLoaded', () => showPopup('error', '{!! addslashes(session("error")) !!}'));</script>
        @endif
        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => { 
                    @foreach($errors->all() as $error)
                        showPopup('error', '{!! addslashes($error) !!}');
                    @endforeach
                });
            </script>
        @endif
        @yield('content')
    </div>
</div>

@include('core::layouts.partials.scripts')

@include('core::layouts.partials.footer')
</body>
</html>







