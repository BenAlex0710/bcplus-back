@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ $page_title }} </h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('package.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="card-box">
                <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">{{ __('package.create_package') }}</h4>
                <form id="create-package" method="post" action="{{ route('admin.package.save') }}">
                    @csrf
                    <div class="row">
                        @foreach ($langs as $lang)
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label text-white " for="{{ $lang }}_name">{{ __('package.name_' . $lang) }} *</label>
                                <input class="form-control required" id="{{ $lang }}_name" name="{{ $lang }}_name" type="text">
                            </div>
                        @endforeach
                        <input type="hidden" name="type" value="2">
                        {{-- <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="type">{{ __('package.type') }} *</label>
                            <select class="form-control required" id="type" name="type">
                                <option value="1">{{ __('package.type_1') }}</option>
                                <option value="2">{{ __('package.type_2') }}</option>
                            </select>
                        </div> --}}
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="validity">{{ __('package.validity') }} *</label>
                            <input class="form-control required" id="validity" name="validity" type="number">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="events">{{ __('package.events') }} *</label>
                            <input class="form-control required" id="events" name="events" type="number">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="events">{{ __('package.max_guests') }} *</label>
                            <input class="form-control required" id="max_guests" name="max_guests" type="number">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="events">{{ __('package.max_attendees') }} *</label>
                            <input class="form-control required" id="max_attendees" name="max_attendees" type="number">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="events">{{ __('package.ticket_commission') }} *</label>
                            <input class="form-control required" id="ticket_commission" name="ticket_commission" type="number">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="events">{{ __('package.storage') }} *</label>
                            <input class="form-control required" id="storage" name="storage" type="number">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="events">{{ __('package.video_quality') }} *</label>
                            <select class="form-control required" id="video_quality" name="video_quality">
                                <option value="480">480px</option>
                                <option value="720">720px</option>
                                <option value="1080">1080px</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="event_max_duration">{{ __('package.event_max_duration') }} *</label>
                            <input class="form-control required" id="event_max_duration" name="event_max_duration" type="number" max="480">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label class="control-label text-white" for="price">{{ __('package.price') }} *</label>
                            <input class="form-control required" id="price" name="price" type="number" step="0.01">
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-info" type="submit">{{ __('package.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(function() {

            $(document).on('submit', '#create-package', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var method = form.attr('method');
                form.prev('.alert').remove();
                form.find('.text-danger').remove();
                $('.loader').fadeIn();
                $.ajax({
                    url: url,
                    type: method,
                    dataType: 'json',
                    data: form.serialize(),
                    success: function(result) {
                        $('.loader').fadeOut();
                        if (!result.status) {
                            form.before('<div class="alert alert-danger">' + result.message + '</div>');
                        } else {
                            form.before('<div class="alert alert-success">' + result.message + '</div>');
                            window.location.href = "{{ route('admin.package.index') }}";
                        }
                    },
                    error: function(xhr) {
                        $('.loader').fadeOut();
                        // console.log(xhr);
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                if (form.find('[name="' + k + '"]').attr('type') == 'number') {
                                    form.find('[name="' + k + '"]').parent().after('<div class="text-danger">' + v + '</div>');
                                } else {
                                    form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                                }
                            });
                        } else if (xhr.status == 419) {
                            window.location.href = "";
                        }
                    }
                });
            });
        });
    </script>
@endsection
