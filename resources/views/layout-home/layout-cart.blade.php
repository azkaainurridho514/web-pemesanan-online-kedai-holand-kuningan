<!DOCTYPE html>
	<html lang="zxx" class="no-js">
	<head>
		<!-- Mobile Specific Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Favicon-->
		<link rel="shortcut icon" href="img/fav.png">
		<!-- Author Meta -->
		<meta name="author" content="codepixer">
		<!-- Meta Description -->
		<meta name="description" content="">
		<!-- Meta Keyword -->
		<meta name="keywords" content="">
		<!-- meta character set -->
		<meta charset="UTF-8">
		<!-- Site Title -->
		<title>@yield('title')</title>

		<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet"> 
        <!--
        CSS
        ============================================= -->
        <link rel="stylesheet" href="{{ asset('coffee/css/linearicons.css')}}">
        <link rel="stylesheet" href="{{ asset('coffee/css/font-awesome.min.css')}}">
        {{-- <link rel="stylesheet" href="{{ asset('coffee/css/bootstrap.css')}}"> --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('coffee/css/magnific-popup.css')}}">
        <link rel="stylesheet" href="{{ asset('coffee/css/nice-select.css')}}">					
        <link rel="stylesheet" href="{{ asset('coffee/css/animate.min.css')}}">
        <link rel="stylesheet" href="{{ asset('coffee/css/owl.carousel.css')}}">
        <link rel="stylesheet" href="{{ asset('coffee/css/main.css')}}">
        <style>
            	/* Untuk semua browser modern */
		::-webkit-scrollbar {
            display: none;
            }

            /* Untuk Firefox */
            html {
            scrollbar-width: none;
            }

            /* Pastikan body tetap bisa di-scroll */
            body {
            overflow: auto;
            }

        </style>
        @vite('resources/css/app.css')
	</head>
    <body>
        <section class="menu-area section-gap" id="coffee">
            <div class="container">
                @yield('main')
            </div>	
        </section>	
        @include('layout-home.footer')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        {{-- <script src="{{ asset('coffee/js/vendor/jquery-2.2.4.min.js')}}"></script>
        <script src="{{ asset('coffee/js/vendor/bootstrap.min.js')}}"></script>			 --}}
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhOdIF3Y9382fqJYt5I_sswSrEw5eihAA"></script>
        <script src="{{ asset('coffee/js/mail-script.js')}}"></script>	
        <script src="{{ asset('coffee/js/main.js')}}"></script>	
        @stack('script-js')
        @vite('resources/js/app.js')
	</body>
</html>