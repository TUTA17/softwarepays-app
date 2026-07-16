<script>
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
            document.querySelectorAll('.user-dropdown').forEach(menu => menu.classList.remove('show'));
        }
    });

    // Toggle giao diện (Cơ bản cho thao tác UI trước mắt)
    function switchTheme(mode) {
        if(mode === 'dark') {
            document.getElementById('btn-theme-dark').classList.add('active');
            document.getElementById('btn-theme-light').classList.remove('active');
            document.body.classList.add('dark-theme');
        } else {
            document.getElementById('btn-theme-light').classList.add('active');
            document.getElementById('btn-theme-dark').classList.remove('active');
            document.body.classList.remove('dark-theme');
        }
        // Lưu trạng thái vào localStorage để đỡ mất sau khi F5
        localStorage.setItem('admin_theme', mode);
    }

    // Tự động set màu giao diện hiện tại
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('admin_theme');
        if(savedTheme && savedTheme === 'dark') switchTheme('dark');
    });

    $(document).ready(function() {
        $('select.form-control').each(function() {
            if ($(this).find('option').length > 6) {
                $(this).select2({
                    placeholder: $(this).find('option:first').text(),
                    allowClear: true,
                    language: { noResults: function() { return 'Không tìm thấy'; }, searching: function() { return 'Đang tìm...'; } }
                });
            }
        });
    });

    // === MONEY FORMAT ===
    function formatMoney(val) {
        val = val.replace(/[^0-9]/g, '');
        return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    $(document).on('input', '.money-input', function() {
        var raw = this.value.replace(/\./g, '');
        this.value = formatMoney(raw);
    });
    $(document).on('focus', '.money-input', function() {
        if (this.value === '0') this.value = '';
    });
    // Strip dots before submit
    $(document).on('submit', 'form', function() {
        $(this).find('.money-input').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
    });
    // Format on page load
        });
    });
</script>
