@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('settings.agora.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('settings.breadcrumb_dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ __('settings.agora.breadcrumb_name') }}</li>
    </ol>
@endsection
@php $mode = get_setting('stripe_mode'); @endphp
@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card-box">
                <h4 class="header-title text-success">{{ __('settings.agora.box_heading') }}</h4>
                <form id="agora_settings_form" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="agora-details">
                        {{-- <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.app_url') }}</label>
                            <input type="text" class="form-control" name="agora_app_url" placeholder="{{ __('settings.agora.app_url') }}" value="{{ get_setting('agora_app_url') }}">
                        </div> --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.app_id') }}</label>
                            <input type="text" class="form-control" name="agora_app_id" placeholder="{{ __('settings.agora.app_id') }}" value="{{ get_setting('agora_app_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.app_certificate') }}</label>
                            <input type="text" class="form-control" name="agora_app_certificate" placeholder="{{ __('settings.agora.app_certificate') }}" value="{{ get_setting('agora_app_certificate') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.customer_id') }}</label>
                            <input type="text" class="form-control" name="agora_customer_id" placeholder="{{ __('settings.agora.customer_id') }}" value="{{ get_setting('agora_customer_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.customer_certificate') }}</label>
                            <input type="text" class="form-control" name="agora_customer_certificate" placeholder="{{ __('settings.agora.customer_certificate') }}" value="{{ get_setting('agora_customer_certificate') }}">
                        </div>
                        <h4 class="header-title text-success">{{ __('settings.agora.recording_settings') }}</h4>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.vendor') }}</label>
                            <select id="agora_recording_vendor" class="form-control" name="agora_recording_vendor">
                                <option value="" selected disabled>{{ __('settings.agora.select_vendor') }}</option>
                                <option value="0">Qiniu Cloud</option>
                                <option value="1">Amazon S3</option>
                                <option value="2">Alibaba Cloud</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.region') }}</label>
                            <select id="agora_recording_region" class="form-control" name="agora_recording_region">
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.bucket') }}</label>
                            <input type="text" class="form-control" name="agora_recording_bucket" placeholder="{{ __('settings.agora.bucket') }}" value="{{ get_setting('agora_recording_bucket') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.access_key') }}</label>
                            <input type="text" class="form-control" name="agora_recording_access_key" placeholder="{{ __('settings.agora.access_key') }}" value="{{ get_setting('agora_recording_access_key') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.agora.secret_key') }}</label>
                            <input type="text" class="form-control" name="agora_recording_secret_key" placeholder="{{ __('settings.agora.secret_key') }}" value="{{ get_setting('agora_recording_secret_key') }}">
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
        $(function() {

            var aws_regions = {
                "0": "US_EAST_1",
                "1": "US_EAST_2",
                "2": "US_WEST_1",
                "3": "US_WEST_2",
                "4": "EU_WEST_1",
                "5": "EU_WEST_2",
                "6": "EU_WEST_3",
                "7": "EU_CENTRAL_1",
                "8": "AP_SOUTHEAST_1",
                "9": "AP_SOUTHEAST_2",
                "10": "AP_NORTHEAST_1",
                "11": "AP_NORTHEAST_2",
                "12": "SA_EAST_1",
                "13": "CA_CENTRAL_1",
                "14": "AP_SOUTH_1",
                "15": "CN_NORTH_1",
                "16": "CN_NORTHWEST_1",
                "17": "US_GOV_WEST_1",
            };
            var qiniu_regions = {
                "0": "East China",
                "1": "North China",
                "2": "South China",
                "3": "North America",
            };
            var alibaba_regions = {
                "0": "CN_Hangzhou",
                "1": "CN_Shanghai",
                "2": "CN_Qingdao",
                "3": "CN_Beijing",
                "4": "CN_Zhangjiakou",
                "5": "CN_Huhehaote",
                "6": "CN_Shenzhen",
                "7": "CN_Hongkong",
                "8": "US_West_1",
                "9": "US_East_1",
                "10": "AP_Southeast_1",
                "11": "AP_Southeast_2",
                "12": "AP_Southeast_3",
                "13": "AP_Southeast_5",
                "14": "AP_Northeast_1",
                "15": "AP_South_1",
                "16": "EU_Central_1",
                "17": "EU_West_1",
                "18": "EU_East_1",
            };

            $('#agora_recording_vendor').change(function() {
                var vendor = $(this).val();
                var options;
                $('#agora_recording_region').val('');
                $('#agora_recording_region option').remove();
                if (vendor == '0') {
                    options = qiniu_regions;
                } else if (vendor == '1') {
                    options = aws_regions;
                } else if (vendor == '2') {
                    options = alibaba_regions;
                }
                $.each(options, function(k, v) {
                    $('#agora_recording_region').append('<option value="' + k + '">' + v + '</option>');
                })
            });

            var vendor, region = 0;
            vendor = "{{ get_setting('agora_recording_vendor') }}";
            region = "{{ get_setting('agora_recording_region') }}";

            // console.log(vendor, region);
            $('#agora_recording_vendor').val(vendor).change();
            $('#agora_recording_region').val(region);



            $('#agora_settings_form').submit(function(e) {
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
