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
                                </div>
                                <form id="form-login">
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
                                    {{-- <div class="mb-3">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <span class="form-check-label">
                                                Remember me next time
                                            </span>
                                        </label>
                                    </div> --}}

                                    <div class="text-center mt-3">
                                        <button type="submit" id="btn-login" class="btn btn-lg btn-primary w-100">
                                            Sign in
                                        </button>
                                    </div>

                                </form>
                                <div id="error-global" class="text-danger d-none mt-3"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>



@endsection

@push('stack-auth')
    <script>
        $(document).ready(function () {
            $("#form-login").on("submit", function(e){
                e.preventDefault();
            
                $("#btn-login").prop("disabled", true).text("Processing...");
            
                let formData = $(this).serializeArray();  
                formData.push({name: "_token", value: $('meta[name="csrf-token"]').attr('content')});
            
                $.ajax({
                    url: "/login",
                    type: "POST",
                    data: $.param(formData),
                    success: function(res) {
                        window.location.href = res.redirect;
                    },
                    error: function(err) {
                        console.log(err)
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
        });

    </script>
@endpush
