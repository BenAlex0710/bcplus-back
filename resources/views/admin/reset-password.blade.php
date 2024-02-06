<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ get_setting('website_title') }} - {{ __('auth.reset_password.page_title') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Forcematix plan in laravel" name="description" />
    <meta content="Letscms Pvt. Ltd." name="developers" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link href="{{ asset('libs/spinkit/spinkit.css') }}" rel="stylesheet" type="text/css">
    <!-- App css -->
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/custom-style.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="account-pages">
    <div class="loader">
        <div class="sk-wave">
            <div class="sk-rect sk-rect1"></div>
            <div class="sk-rect sk-rect2"></div>
            <div class="sk-rect sk-rect3"></div>
            <div class="sk-rect sk-rect4"></div>
            <div class="sk-rect sk-rect5"></div>
        </div>
    </div>
    <!-- Begin page -->
    <div class="accountbg" style="background: url('{{ asset('admin/images/bg-1.jpg') }}');background-size: cover;background-position: center;"></div>
    <div class="wrapper-page account-page-full">
        <div class="card shadow-none">
            <div class="card-block">
                <div class="account-box">
                    <div class="card-box shadow-none p-4 mt-2">
                        <h2 class="text-center pb-3 logo">
                            <a href="javascript:void(0)" class="text-white">
                                <span><img src="{{ asset(get_setting('logo')) }}" alt="{{ get_setting('website_title') }}"></span>
                            </a>
                        </h2>
                        <h3 class="text-center mb-3 bt-3 text-success">{{ __('auth.reset_password.page_title') }}</h3>
                        <form id="reset_password_form" action="" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="password">{{ __('auth.reset_password.password') }}</label>
                                    <input class="form-control" type="password" name="password" id="password" required="" placeholder="Enter your password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="password">{{ __('auth.reset_password.confirm_password') }}</label>
                                    <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required="" placeholder="{{ __('auth.reset_password.confirm_password') }}">
                                </div>
                            </div>
                            <div class="form-group row text-center">
                                <div class="col-12">
                                    <button class="btn btn-block btn-primary waves-effect waves-light" type="submit">{{ __('auth.reset_password.submit_btn') }}</button>
                                </div>
                            </div>
                        </form>
                        <!-- <div class="row mt-4">
                                    <div class="col-sm-12 text-center">
                                        <p class="text-muted">Don't have an account? <a href="page-register.html" class="text-dark ml-1"><b>Sign Up</b></a></p>
                                    </div>
                                </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <p class="account-copyright">2019 - {{ date('Y') }} © {{ get_setting('website_title') }} Powered By @ <span class="d-none d-sm-inline-block"><a href="https://www.cosonas.com/">Cosonas.com</a></span></p>
        </div>
    </div>


    <!-- Vendor js -->
    <script src="{{ asset('admin/js/vendor.min.js') }}"></script>
    <!-- App js -->
    <!-- <script src="{{ asset('admin/js/app.min.js') }}"></script> -->
    <script type="text/javascript">
        $(function() {
            $('.loader').fadeOut();
            $('#reset_password_form')[0].reset();
            $('#reset_password_form').submit(function(e) {
                $('.loader').fadeIn();
                e.preventDefault();
                var form = $(this);
                form.prev('.alert-danger').remove();
                form.find('.text-danger').remove();
                $.ajax({
                    url: "{{ route('admin.rest-password', $token) }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),
                    success: function(result) {
                        $('.loader').fadeOut();
                        // console.log(result);
                        if (!result.status) {
                            form.before('<div class="alert alert-danger">' + result.message + '</div>');
                        } else {
                            form.before('<div class="alert alert-success">' + result.message + '</div>');
                            form[0].reset();
                            setTimeout(function() {
                                window.location.href = "{{ route('admin.login') }}";
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        $('.loader').fadeOut();
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                            });
                        } else if (xhr.status == 419) {
                            window.location.href = "";
                        }
                        // console.log(xhr);
                    }
                });
            });
        });
    </script>
</body>

</html>
