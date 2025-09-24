<!-- favicon -->
{{--<link rel="icon" href="{{ asset('assets/images/fav.png') }}">--}}
<link rel="icon" href="{{ getIcon($setting->favicon) }}?v=1">

<!--  css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/swiper.min.css') }}">

<link rel="stylesheet" href="{{ asset('frontend/assets/css/main.min.css') }}?v=5.2">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.min.css') }}?v=2.1">

<!-- DataTables Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- Responsive extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<style>
    input[type="file"].form-control {
        padding: 5px 10px !important;
    }
    .header_section .nav-link.active {
        color: #ffcc00;
    }
</style>
