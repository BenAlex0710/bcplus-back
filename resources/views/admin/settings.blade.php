@extends('layouts.dashboard')
@section('breadcrumb')
    {{-- <link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <h4 class="page-title-main">{{ __('settings.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('settings.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.breadcrumb_name') }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6 col-lg-8">
            <div class="card-box">
                <h4 class="header-title mb-3 text-success">{{ __('settings.website.box_heading') }}</h4>
                <form id="website_settings_form" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.title') }}</label>
                                <input type="text" class="form-control" name="website_title" placeholder="{{ __('settings.website.title') }}" value="{{ get_setting('website_title') }}">
                            </div>
                            {{-- <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.subtitle') }}</label>
                                <input type="text" class="form-control" name="website_subtitle" placeholder="{{ __('settings.website.subtitle') }}" value="{{ get_setting('website_subtitle') }}">
                            </div> --}}
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.support_email') }}</label>
                                <input type="email" name="website_support_email" class="form-control" placeholder="{{ __('settings.website.support_email') }}" value="{{ get_setting('website_support_email') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.support_number') }}</label>
                                <input type="text" class="form-control" name="website_support_phone" placeholder="{{ __('settings.website.support_number') }}" value="{{ get_setting('website_support_phone') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.stream_late_max_time') }} <small class="text-info">( {{ __('settings.website.stream_late_max_time_instruction') }} )</small></label>
                                <input type="text" class="form-control" name="stream_late_max_time" placeholder="{{ __('settings.website.stream_late_max_time') }}" value="{{ get_setting('stream_late_max_time') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.stream_late_penalty') }} <small class="text-info">( {{ __('settings.website.stream_late_penalty_instruction') }} )</small></label>
                                <input type="text" class="form-control" name="stream_late_penalty" placeholder="{{ __('settings.website.stream_late_penalty') }}" value="{{ get_setting('stream_late_penalty') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.trending_minimum_attendees') }} <small class="text-info">( {{ __('settings.website.trending_minimum_attendees_instruction') }} )</small></label>
                                <input type="text" class="form-control" name="trending_minimum_attendees" placeholder="{{ __('settings.website.trending_minimum_attendees') }}" value="{{ get_setting('trending_minimum_attendees') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.stream_auto_leave_timeout') }} <small class="text-info">( {{ __('settings.website.stream_auto_leave_timeout_instruction') }} )</small></label>
                                <input type="number" min="0" class="form-control" name="stream_auto_leave_timeout" placeholder="{{ __('settings.website.stream_auto_leave_timeout') }}" value="{{ get_setting('stream_auto_leave_timeout') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.stream_early_time') }} <small class="text-info">( {{ __('settings.website.stream_early_time_instruction') }} )</small></label>
                                <input type="number" min="0" class="form-control" name="stream_early_time" placeholder="{{ __('settings.website.stream_early_time') }}" value="{{ get_setting('stream_early_time') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.reset_password_link') }}</label>
                                <input type="number" min="1" class="form-control" name="reset_password_link_expire_time" placeholder="{{ __('settings.website.reset_password_link') }}" value="{{ get_setting('reset_password_link_expire_time') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.address') }}</label>
                                <textarea class="form-control" name="company_address">{{ get_setting('company_address') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.logo') }}</label>
                                <input type="file" class="form-control" id="logo" data-width="auto" data-height="50" accept="image/png, image/jpeg">
                                <img style="max-width:100%;" class="mt-2 mb-2 previewImage" width="auto" height="50" src="{{ storage_url(get_setting('logo')) }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.go_live_event_type') }}</label>
                                <select class="form-control" name="go_live_event_type">
                                    <option value="" selected disabled></option>
                                    @foreach ($event_types as $event_type)
                                        <option value="{{ $event_type->id }}" @if (get_setting('go_live_event_type') == $event_type->id) selected @endif>{{ $event_type->{$locale . '_name'} }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.website.pages_to_show_on_website') }}</label>
                                <select class="form-control pages_to_show_on_website" name="pages_to_show_on_website[]" multiple>
                                    @foreach ($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->{$locale . '_title'} }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('settings.update_btn') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- end row -->
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card-box">
                <h4 class="header-title mb-3 text-success">{{ __('settings.personal.box_heading') }}</h4>
                {{-- <p class="sub-header">Update Your Profile Details</p> --}}
                <div class="row">
                    <div class="col-12">
                        <form id="update_personl_settings_form" method="post" autocomplete="off">
                            @csrf
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.personal.name') }}</label>
                                <input type="text" class="form-control" name="name" placeholder="{{ __('settings.personal.name') }}" value="{{ auth()->guard('admin')->user()->name }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{!! __('settings.personal.email') !!}</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ auth()->guard('admin')->user()->email }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{!! __('settings.personal.new_password') !!}</label>
                                <input type="password" name="new_password" class="form-control" placeholder="********" value="">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.personal.old_password') }}</label>
                                <input type="password" name="old_password" class="form-control" placeholder="********">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('settings.update_btn') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end row -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-8">
            <div class="card-box">
                <h4 class="header-title mb-3 text-success">{{ __('settings.social_login.box_heading') }}</h4>
                <form id="social_settings_form" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <h4 class="header-title mb-3 text-info">{{ __('settings.social_login.google_details') }}</h4>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.client_id') }}</label>
                                <input type="text" class="form-control" name="google_client_id" placeholder="{{ __('settings.social_login.client_id') }}" value="{{ get_setting('google_client_id') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.client_secret') }}</label>
                                <input type="text" class="form-control" name="google_client_secret" placeholder="{{ __('settings.social_login.client_secret') }}" value="{{ get_setting('google_client_secret') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.api_key') }}</label>
                                <input type="text" name="google_api_key" class="form-control" placeholder="{{ __('settings.social_login.api_key') }}" value="{{ get_setting('google_api_key') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.redirect_url') }}</label>
                                <input type="text" name="google_redirect_url" class="form-control" placeholder="{{ __('settings.social_login.redirect_url') }}" value="{{ get_setting('google_redirect_url') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="header-title mb-3 text-info">{{ __('settings.social_login.facebook_details') }}</h4>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.client_id') }}</label>
                                <input type="text" class="form-control" name="facebook_client_id" placeholder="{{ __('settings.social_login.client_id') }}" value="{{ get_setting('facebook_client_id') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.client_secret') }}</label>
                                <input type="text" class="form-control" name="facebook_client_secret" placeholder="{{ __('settings.social_login.client_secret') }}" value="{{ get_setting('facebook_client_secret') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('settings.social_login.redirect_url') }}</label>
                                <input type="text" name="facebook_redirect_url" class="form-control" placeholder="{{ __('settings.social_login.redirect_url') }}" value="{{ get_setting('facebook_redirect_url') }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('settings.update_btn') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- end row -->
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('libs/select2/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $('.pages_to_show_on_website').select2()
            $('.pages_to_show_on_website').val({!! get_setting('pages_to_show_on_website') !!});
            $('.pages_to_show_on_website').trigger('change');

            $("#logo").change(function() {
                previewImage(this);
            });

            $("#login_page_image").change(function() {
                previewImage(this);
            });

            $('#update_personl_settings_form').submit(function(e) {
                $('.loader').fadeIn();
                e.preventDefault();
                var form = $(this);
                form.prev('.alert').remove();
                form.find('.text-danger').remove();
                $.ajax({
                    url: "{{ route('admin.update-personal-settings') }}",
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
                                form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                            });
                        } else if (xhr.status == 419) {
                            window.location.href = "";
                        }
                        // console.log(xhr);
                    }
                });
            });

            $('#website_settings_form, #social_settings_form').submit(function(e) {
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
                        if ("logo_error" in result && result.logo_error.length > 0) {
                            form.before('<div class="alert alert-danger"> Logo Error : ' + result.logo_error + '</div>');
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
@endsection
