<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
		<meta name="author" content="AdminKit">
		<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

		<link rel="canonical" href="https://demo-basic.adminkit.io/" />

		<title>@yield('title')</title>
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<link href="{{ asset("adminkit-dev-old/static/css/app.css") }}" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
		@vite('resources/css/app.css')
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
	</head>

	<body>
		<div class="wrapper">
			@include('layout-dashboard.sidebar')

			<div class="main">
				@include('layout-dashboard.navbar')
				<main class="content">
					<div class="container-fluid p-0">
						<h1 class="h3 mb-3">@yield('title-page')</h1>
						@yield('main')
					</div>
				</main>
				@include('layout-dashboard.footer')
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
		<script src="{{ asset("adminkit-dev-old/static/js/app.js") }}"></script>
		<script>
			document.addEventListener("DOMContentLoaded", function() {
				Echo.channel(`order-event`)
				.listen('OrderEvent', (e) => {
					Swal.fire({
						icon: e.icon,
						title: e.title,
						text: e.text,
						showConfirmButton: false
					});
				});
			});
		</script>

		@stack('script')
		@vite('resources/js/app.js')
	</body>
</html>