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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
			{{-- <link rel="stylesheet" href="{{ asset('coffee/css/animate.min.css')}}"> --}}
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

        .scrollable-row {
          max-height: 300px; 
          overflow-y: hidden;
          overflow-x: auto;
        }
      </style>
	  @vite('resources/css/app.css')
		</head>
		<body>
      @include('layout-home.header')
      @include('layout-home.banner')
			<section class="menu-area section-gap" id="coffee">
				<div class="container">
					@yield('main')
				</div>	
			</section>	
			@include('layout-home.footer')
			<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
			

			{{-- <script src="{{ asset('coffee/js/vendor/jquery-2.2.4.min.js')}}"></script> --}}
			{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script> --}}
			{{-- <script src="{{ asset('coffee/js/vendor/bootstrap.min.js')}}"></script>			 --}}
			{{-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhOdIF3Y9382fqJYt5I_sswSrEw5eihAA"></script> --}}
  			{{-- <script src="{{ asset('coffee/js/easing.min.js')}}"></script>			 --}}
			{{-- <script src="{{ asset('coffee/js/hoverIntent.js')}}"></script> --}}
			{{-- <script src="{{ asset('coffee/js/superfish.min.js')}}"></script>	 --}}
			{{-- <script src="{{ asset('coffee/js/jquery.ajaxchimp.min.js')}}"></script> --}}
			{{-- <script src="{{ asset('coffee/js/jquery.magnific-popup.min.js')}}"></script>	 --}}
			{{-- <script src="{{ asset('coffee/js/owl.carousel.min.js')}}"></script>			 --}}
			{{-- <script src="{{ asset('coffee/js/jquery.sticky.js')}}"></script> --}}
			{{-- <script src="{{ asset('coffee/js/jquery.nice-select.min.js')}}"></script>			 --}}
			{{-- <script src="{{ asset('coffee/js/parallax.min.js')}}"></script>	 --}}
			{{-- <script src="{{ asset('coffee/js/waypoints.min.js')}}"></script> --}}
			{{-- <script src="{{ asset('coffee/js/jquery.counterup.min.js')}}"></script>					
			<script src="{{ asset('coffee/js/mail-script.js')}}"></script>	 --}}
			<script src="{{ asset('coffee/js/main.js')}}"></script>	
			<script>
			if (typeof $ === "undefined") { var $ = jQuery; }
			if (typeof $.fn.modal === "undefined" && typeof bootstrap !== "undefined") {
				$.fn.modal = function (config) {
					const modalEl = this[0];
					const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
					if (config === "show") modal.show();
					else if (config === "hide") modal.hide();
					return this;
				};
			}
			</script>
      		@stack('script-js')
			@vite('resources/js/app.js')
		</body>
	</html>







{{-- <!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <title>@yield('title')</title>
    @stack('style-css')
</head>
<body>
    @yield('main')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    @stack('script-js')
</body>
</html> --}}