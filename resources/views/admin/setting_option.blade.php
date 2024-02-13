@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ $field_title }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('settings.breadcrumb_dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ $field_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3 float-left text-success">{{ $field_title }}</h4>
                <a href="" class="btn  mb-3 btn-success float-right" data-toggle="modal" data-target="#add_new_setting-options">{{ __('settings.options.add_new') }}</a>
                <table id="personality_tag_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('settings.options.id') }}</th>
                            @foreach ($langs as $lang)
                                <th>{{ __('settings.options.name') }} ({{ $lang }})</th>
                            @endforeach
                            <th>{{ __('settings.options.created_at') }}</th>
                            <th>{{ __('settings.options.update') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($settingOption as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                @foreach ($langs as $lang)
                                    <td>{{ $tag->{$lang . '_name'} }}</td>
                                @endforeach
                                <td>{{ $tag->created_at }}</td>
                                <td>
                                    <a href="javascript:void(0)" data-id="{{ $tag->id }}" data-tag_data="{{ $tag }}" class="text-primary edit_setting-options"><i class="fe-edit"></i></a> &nbsp;
                                    @if ($tag->status == '1')
                                        <a href="javascript:void(0)" data-id="{{ $tag->id }}" data-type="Disable" data-url="{{ route('admin.setting-options-change-status', $tag->id) }}" class="text-danger change_status_setting-options"><i class="fe-x-circle"></i></a>
                                    @else
                                        <a href="javascript:void(0)" data-id="{{ $tag->id }}" data-type="Enable" data-url="{{ route('admin.setting-options-change-status', $tag->id) }}" class="text-success change_status_setting-options"><i class="fe-check-circle"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_new_setting-options" tabindex="-1" aria-labelledby="add_new_setting-optionsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add_new_setting-optionsLabel">{{ __('settings.options.add_field_option', ['field_title' => $field_title]) }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_new_setting-options_form" class="form-horizontal" action="{{ route('admin.setting-options', $field) }}" method="post">
                        @csrf
                        <input id="field" name="field" type="hidden" class="form-control" placeholder="" value="personality_tag">
                        <div class="row">
                            @foreach ($langs as $lang)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('settings.options.name') }} ({{ $lang }})</label>
                                        <input id="name" name="{{ $lang }}_name" type="text" class="form-control" placeholder="{{ __('settings.options.name') }}" value="">
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-12 text-center">
                                <button class="btn width-lg btn-rounded btn-primary waves-effect waves-light" type="submit">{{ __('settings.options.save_option') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_setting-option-modal" tabindex="-1" aria-labelledby="edit_setting-optionsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_setting-optionsLabel"> {{ __('settings.options.update_field_option', ['field_title' => $field_title]) }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_setting-options_form" class="form-horizontal" action="{{ route('admin.setting-options-edit', $field) }}" method="put">
                        @csrf
                        <input id="edit-id" name="id" type="hidden" class="form-control" value="">
                        <input id="edit-field" name="field" type="hidden" class="form-control" placeholder="" value="">
                        <div class="row">
                            <div class="col-md-12">
                                @foreach ($langs as $lang)
                                    <div class="form-group">
                                        <label>{{ __('settings.options.name') }} ({{ $lang }})</label>
                                        <input id="edit-name-{{ $lang }}" name="{{ $lang }}_name" type="text" class="form-control" placeholder="{{ __('settings.options.name') }}" value="">
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12 text-center">
                                <button class="btn width-lg btn-rounded btn-primary waves-effect waves-light" type="submit">{{ __('settings.options.update_option') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('js')

    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            var langs = JSON.parse('<?php echo json_encode($langs); ?>');
            // console.log(lang);

            $(document).on('submit', '#add_new_setting-options_form', function(e) {
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
                        // console.log(result);

                        if (!result.status) {
                            form.before('<div class="alert alert-danger">' + result.message + '</div>');
                        } else {
                            form.before('<div class="alert alert-success">' + result.message + '</div>');
                            // window.location.href = "";
                            // $('#users_table').DataTable().ajax.reload();
                            location.reload(true);
                            $('#edit-profile-modal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        $('.loader').fadeOut();
                        // console.log(xhr);
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                            });
                        } else if (xhr.status == 419) {
                            a
                            window.location.href = "";
                        }
                    }
                });
            });

            $(document).on('click', '.edit_setting-options', function(e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var data = $(this).data('tag_data');
                $.each(langs, function(k, v) {
                    $("#edit-name-" + v).val(data[v + '_name']);
                });
                $("#edit-id").val(id);
                $('#edit_setting-option-modal').modal('show');
            });

            $(document).on('submit', '#edit_setting-options_form', function(e) {
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
                        // console.log(result);

                        if (!result.status) {
                            form.before('<div class="alert alert-danger">' + result.message + '</div>');
                        } else {
                            form.before('<div class="alert alert-success">' + result.message + '</div>');
                            // window.location.href = "";
                            // $('#users_table').DataTable().ajax.reload();
                            location.reload(true);
                            $('#edit-profile-modal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        $('.loader').fadeOut();
                        // console.log(xhr);
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                            });
                        } else if (xhr.status == 419) {
                            a
                            window.location.href = "";
                        }
                    }
                });
            });

            $(document).on('click', '.change_status_setting-options', function(e) {
                var type = $(this).data('type');
                var url = $(this).data('url');
                if (type == '1') {
                    var title = "{{ __('settings.options.status_enable_popup.title') }}";
                    var subtitle = "{{ __('settings.options.status_enable_popup.subtitle') }}";
                    var confirm_text = "{{ __('settings.options.status_enable_popup.confirm_text') }}";
                    var cancel_text = "{{ __('settings.options.status_enable_popup.cancel_text') }}";
                    var result_success_title = "{{ __('settings.options.status_enable_popup.result_success_title') }}";
                    var result_failed_title = "{{ __('settings.options.status_enable_popup.result_failed_title') }}";
                } else {
                    var title = "{{ __('settings.options.status_disable_popup.title') }}";
                    var subtitle = "{{ __('settings.options.status_disable_popup.subtitle') }}";
                    var confirm_text = "{{ __('settings.options.status_disable_popup.confirm_text') }}";
                    var cancel_text = "{{ __('settings.options.status_disable_popup.cancel_text') }}";
                    var result_success_title = "{{ __('settings.options.status_disable_popup.result_success_title') }}";
                    var result_failed_title = "{{ __('settings.options.status_disable_popup.result_failed_title') }}";
                }
                Swal.fire({
                    title: title,
                    text: subtitle,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirm_text,
                    cancelButtonText: cancel_text
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'put',
                            dataType: 'json',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        result_success_title,
                                        res.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                } else {
                                    Swal.fire(
                                        result_failed_title,
                                        res.message,
                                        'error'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                }
                            },
                            error: function(xhr) {
                                session_error(xhr);
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection
