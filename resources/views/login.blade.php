@extends('layout-dashboard.main-auth')
@section('main')
<main class="d-flex w-100">
    <div class="container d-flex flex-column">
        <div class="row vh-100">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                <div class="d-table-cell align-middle">

                    <div class="text-center mt-4">
                        <p class="lead">Sign in to your account to continue</p>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="m-sm-4">
                                <div class="text-center">
                                    {{-- <img src="{{ asset('adminkit-dev-old/static/img/avatars/avatar.jpg') }}" 
                                         class="img-fluid rounded-circle" width="132" height="132" /> --}}
                                </div>
                                <div id="error-global" class="text-danger d-none"></div>

                                <form id="form-login">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" />
                                        <small class="text-danger error-email"></small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" />
                                        <small class="text-danger error-password"></small>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button type="submit" id="btn-login" class="btn btn-lg btn-primary w-100">
                                            Sign in
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

{{-- AJAX SCRIPT --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$("#form-login").on("submit", function(e){
    e.preventDefault();

    // disabled button
    $("#btn-login").prop("disabled", true).text("Processing...");

    // reset error
    $(".error-email").text("");
    $(".error-password").text("");
    $("#error-global").addClass("d-none").text("");

    $.ajax({
        url: "{{ route('login.ajax') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res) {
            window.location.href = res.redirect;
        },
        error: function(err) {
            $("#btn-login").prop("disabled", false).text("Sign in");
            if (err.status === 422) {
                let errors = err.responseJSON.errors;
                if (errors.email) $(".error-email").text(errors.email[0]);
                if (errors.password) $(".error-password").text(errors.password[0]);
            }
            if (err.status === 401) {
                $("#error-global").removeClass("d-none")
                    .text(err.responseJSON.message);
            }
        }
    });
});
</script>

@endsection
