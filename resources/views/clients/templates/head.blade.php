<!-- Favicon -->
@if(($settings->site_favicon ?? null))
    <link rel="icon" type="image/png" href="{{ asset('clients/assets/img/business/' . $settings->site_favicon) }}">
    <link rel="shortcut icon" href="{{ asset('clients/assets/img/business/' . $settings->site_favicon) }}">
    <link rel="apple-touch-icon" href="{{ asset('clients/assets/img/business/' . $settings->site_favicon) }}">
@else
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
@endif
