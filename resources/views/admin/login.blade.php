<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ get_setting('website_title') }} - {{ __('auth.login.title') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="BCplus News" name="description" />
    <meta content="BCplus News" name="developers" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link href="{{ asset('libs/spinkit/spinkit.css') }}" rel="stylesheet" type="text/css">

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
    {{-- <div class="accountbg" style="background: url('{{ asset('admin/images/bg-1.jpg') }}');background-size: cover;background-position: center;"></div> --}}
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
                        <h3 class="text-center mb-3 bt-3 text-success">{{ __('auth.login.title') }}</h3>
                        {!! error_alerts($errors) !!}
                        <form id="login_form" action="" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="emailaddress">{{ __('auth.login.email') }}</label>
                                    <input class="form-control" type="email" name="email" id="emailaddress" required="" placeholder="{{ __('auth.login.placeholders.email') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <a href="javascript:void(0)" id="forgot-password" class="text-muted float-right" data-toggle="modal" data-target="#forgot-password-modal"><small>{{ __('auth.login.forgot_password') }}</small></a>
                                    <label for="password">{{ __('auth.login.password') }}</label>
                                    <input class="form-control" type="password" name="password" id="password" required="" placeholder="{{ __('auth.login.placeholders.password') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="checkbox checkbox-primary">
                                        <input id="remember" name="remember" type="checkbox">
                                        <label for="remember">
                                            {{ __('auth.login.remember') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row text-center">
                                <div class="col-12">
                                    <button class="btn btn-block btn-primary waves-effect waves-light" type="submit"> {{ __('auth.login.login_btn') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <p class="account-copyright">2020 - {{ date('Y') }} © {{ get_setting('website_title') }} {{ __('comman.powered_by') }} <span class="d-none d-sm-inline-block"> <a href="https://www.cosonas.com/">Cosonas.com</a></span></p>
        </div>
    </div>

    <!-- forgot password modal -->
    <div class="modal fade" id="forgot-password-modal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="forgotPasswordModalLabel">{{ __('auth.login.forgot_pass_modal.title') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-info">{{ __('auth.login.forgot_pass_modal.note') }}</p>
                    <form method="POST" id="forgot-password-form" action="{{ route('admin.forgot-passward') }}">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('auth.login.email') }}</label>
                            <input type="email" name="email" class="form-control" placeholder="{{ __('auth.login.placeholders.email') }}">
                        </div>
                        <button type="submit" class="btn btn-info">{{ __('auth.login.forgot_pass_modal.send_email_btn') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/js/vendor.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('.loader').fadeOut();
            $('#login_form')[0].reset();
            $('#login_form').submit(function(e) {
                $('.loader').fadeIn();
                e.preventDefault();
                var form = $(this);
                form.prev('.alert-danger').remove();
                form.find('.text-danger').remove();
                $.ajax({
                    url: "{{ route('admin.login') }}",
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
                            window.location.href = "";
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

            // forgot-password-form
            $('#forgot-password-form').submit(function(e) {
                $('.loader').fadeIn();
                e.preventDefault();
                var form = $(this);
                form.prev('.alert').remove();
                form.find('.text-danger').remove();
                $.ajax({
                    url: "{{ route('admin.forgot-passward') }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),
                    success: function(result) {
                        $('.loader').fadeOut();
                        // console.log(result);
                        if (!result.status) {
                            form.before('<div class="alert alert-danger">' + result.message + '</div>');
                        } else {
                            form[0].reset();
                            form.before('<div class="alert alert-success">' + result.message + '</div>');
                            setTimeout(function() {
                                $('.forgot-password-modal').modal('hide');
                            }, 1000);
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
