{{-- Google Fonts: Open Sans --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

{{-- CSS Files --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('clients/assets/css/main.css') }}?v={{ file_exists(public_path('clients/assets/css/main.css')) ? filemtime(public_path('clients/assets/css/main.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('clients/assets/css/header.css') }}?v={{ file_exists(public_path('clients/assets/css/header.css')) ? filemtime(public_path('clients/assets/css/header.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('clients/assets/css/footer.css') }}?v={{ file_exists(public_path('clients/assets/css/footer.css')) ? filemtime(public_path('clients/assets/css/footer.css')) : time() }}">
@if(request()->routeIs('client.technical-service.*'))
    <link rel="stylesheet" href="{{ asset('clients/assets/css/technical-service.css') }}">
@endif
@stack('styles')
