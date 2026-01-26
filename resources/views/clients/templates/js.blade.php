@php
    $sessionMessages = [];
    if (session('success')) {
        $sessionMessages['success'] = session('success');
    }
    if (session('error')) {
        $sessionMessages['error'] = session('error');
    }
    if (session('warning')) {
        $sessionMessages['warning'] = session('warning');
    }
    if (session('info')) {
        $sessionMessages['info'] = session('info');
    }
    if ($errors->any()) {
        $sessionMessages['errors'] = $errors->all();
    }
@endphp

@if(!empty($sessionMessages))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initSessionMessages === 'function') {
                initSessionMessages({!! json_encode($sessionMessages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!});
            }
        });
    </script>
@endif
