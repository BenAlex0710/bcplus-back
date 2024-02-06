@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('settings.payment.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('settings.breadcrumb_dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ __('settings.payment.breadcrumb_name') }}</li>
    </ol>
@endsection
@php $mode = get_setting('stripe_mode'); @endphp
@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card-box">
                <h4 class="header-title text-success">{{ __('settings.payment.box_heading') }}</h4>
                <form id="payment_settings_form" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.payment.mode') }}</label>
                        <select class="form-control" name="stripe_mode">
                            <option value="test">{{ __('settings.payment.mode_stage') }}</option>
                            <option value="live">{{ __('settings.payment.mode_live') }}</option>
                        </select>
                    </div>
                    <div class="test-mode-details" style="display: none;">
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.payment.stripe_test_key') }}</label>
                            <input type="text" class="form-control" name="stripe_test_key" placeholder="{{ __('settings.payment.stripe_test_key') }}" value="{{ get_setting('stripe_test_key') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.payment.stripe_test_secret') }}</label>
                            <input type="text" class="form-control" name="stripe_test_secret" placeholder="{{ __('settings.payment.stripe_test_secret') }}" value="{{ get_setting('stripe_test_secret') }}">
                        </div>
                    </div>
                    <div class="live-mode-details" style="display: none;">
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.payment.stripe_key') }}</label>
                            <input type="text" class="form-control" name="stripe_key" placeholder="{{ __('settings.payment.stripe_key') }}" value="{{ get_setting('stripe_key') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.payment.stripe_secret') }}</label>
                            <input type="text" class="form-control" name="stripe_secret" placeholder="{{ __('settings.payment.stripe_secret') }}" value="{{ get_setting('stripe_secret') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('settings.update_btn') }}</button>
                    </div>
                </form>
                <!-- end row -->
            </div>
        </div>
    </div>
@endsection
@section('css')
@endsection
@section('js')
    <script type="text/javascript">
        function toggleModes(mode) {
            if (mode == 'test') {
                $('.test-mode-details').slideDown();
                $('.live-mode-details').slideUp();
            } else {
                $('.test-mode-details').slideUp();
                $('.live-mode-details').slideDown();
            }
        }

        $(function() {

            var mode = "{{ $mode ?? 'test' }}";
            toggleModes(mode);
            $('[name="stripe_mode"]').val(mode);

            $('[name="stripe_mode"]').change(function() {
                // console.log($(this).val());
                toggleModes($(this).val());
            });


            $('#payment_settings_form').submit(function(e) {
                $('.loader').fadeIn();
                e.preventDefault();
                var form = $(this);
                form.siblings('.alert').remove();
                form.find('.text-danger').remove();
                $.ajax({
                    url: "{{ route('admin.update-website-settings') }}",
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
                            // form[0].reset();
                            // window.location.href = "";
                        }
                    },
                    error: function(xhr) {
                        $('.loader').fadeOut();
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                form.find('[name="ec_pay_' + k + '"]').after('<div class="text-danger">' + v + '</div>');
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


@endsection
