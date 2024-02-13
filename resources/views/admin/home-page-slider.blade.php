@extends('layouts.dashboard')
@section('breadcrumb')
    {{-- <link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <h4 class="page-title-main">{{ __('settings.home_slider.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('settings.home_slider.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.home_slider.breadcrumb_name') }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card-box">
                <div class="d-flex mb-3 justify-content-between align-items-center">
                    <h4 class="header-title text-success">{{ __('settings.home_slider.box_heading') }}</h4>
                    <button class="btn btn-sm btn-success create-slide">{{ __('settings.home_slider.create_new_slide_btn') }}</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('settings.home_slider.language') }}</th>
                                <th>{{ __('settings.home_slider.image') }}</th>
                                <th>{{ __('settings.home_slider.title') }}</th>
                                <th>{{ __('settings.home_slider.subtitle') }}</th>
                                <th>{{ __('settings.home_slider.button_label') }}</th>
                                <th>{{ __('settings.home_slider.button_url') }}</th>
                                <th>{{ __('settings.home_slider.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slides as $slide)
                                <tr class="v-align-middle">
                                    <td>{{ $slide->language }}</td>
                                    <td><img src="{{ storage_url($slide->image) }}" width="100" height="auto"></td>
                                    <td>{{ $slide->title }}</td>
                                    <td>{{ $slide->subtitle }}</td>
                                    <td>{{ $slide->button_label }}</td>
                                    <td>{{ $slide->button_url }}</td>
                                    <td>
                                        <a class="text-info edit-slide" href="{{ route('admin.home-slider.view_slide', $slide->id) }}"><i class="fe-edit"></i></a>
                                        <a class="text-danger delete-slide" href="{{ route('admin.home-slider.delete_slide', $slide->id) }}"><i class="fe-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="manage-slide-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">{{ __('settings.home_slider.create_new_slide') }}</h4>
                        <form id="manage-slide-form" class="form-horizontal" action="{{ route('admin.home-slider.create_slide') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="image" data-width="100%" data-height="auto" accept="image/png, image/jpeg">
                                        <img style="max-width:100%;" class="mt-2 mb-2 previewImage" src="" width="100%" height="auto">
                                        <input type="hidden" name="image" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.home_slider.language') }}</label>
                                        <select id="language" name="language" class="form-control">
                                            @foreach ($langs as $lang)
                                                <option value="{{ $lang }}">{{ $lang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.home_slider.title') }}</label>
                                        <input id="title" name="title" type="text" class="form-control" placeholder="{{ __('settings.home_slider.title') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.home_slider.subtitle') }}</label>
                                        <input id="subtitle" name="subtitle" type="text" class="form-control" placeholder="{{ __('settings.home_slider.subtitle') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.home_slider.button_label') }}</label>
                                        <input id="button_label" name="button_label" type="text" class="form-control" placeholder="{{ __('settings.home_slider.button_label') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.home_slider.button_url') }}</label>
                                        <input id="button_url" name="button_url" type="text" class="form-control" placeholder="{{ __('settings.home_slider.button_url') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.home_slider.slide_order') }}</label>
                                        <input id="slide_order" name="slide_order" type="number" class="form-control" placeholder="{{ __('settings.home_slider.slide_order') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group account-btn text-center m-t-10">
                                <div class="col-12">
                                    <button class="btn width-lg btn-rounded btn-primary waves-effect waves-light" type="submit">{{ __('settings.home_slider.submit_btn') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>


    </div>
@endsection
@section('js')
    <script src="{{ asset('libs/select2/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#image").change(function() {
                previewImage(this);
            });

            $(".create-slide").click(function() {
                var $form = $('#manage-slide-form');
                $form[0].reset();
                $form.prev('.alert').remove();
                $form.find('.text-danger').remove();
                $form.attr('action', "{{ route('admin.home-slider.create_slide') }}");
                $('.previewImage').attr('src', '');
                $('#manage-slide-modal').modal('show');
            });

            $(document).on('click', '.edit-slide', function(e) {
                e.preventDefault();
                var $form = $('#manage-slide-form');
                $form.prev('.alert').remove();
                $form.find('.text-danger').remove();
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: $(this).attr('href'),
                    success: function(result) {
                        // console.log(result);
                        $form.attr('action', result.slide.update_url)
                        $('.previewImage').attr('src', result.slide.image_url);
                        $('#language').val(result.slide.language);
                        $('#title').val(result.slide.title);
                        $('#subtitle').val(result.slide.subtitle);
                        $('#description').val(result.slide.description);
                        $('#slide_order').val(result.slide.slide_order);
                        $('#button_url').val(result.slide.button_url);
                        $('#button_label').val(result.slide.button_label);
                        $('#manage-slide-modal').modal('show');
                    }
                });
            });

            $(document).on('submit', '#manage-slide-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                $form.find('.text-danger').remove();
                $form.prev('.alert').remove();
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    success: function(result) {
                        // console.log(result);
                        if (result) {
                            $form.trigger('reset');
                            // $('#manage-slide-modal').modal('hide');
                            window.location.reload();
                        } else {
                            $form.before('<div class="alert alert-danger">' + res.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                if (k == 'slide_order') {
                                    $form.find('[name="' + k + '"]').parent().after('<div class="text-danger">' + v + '</div>');
                                } else {
                                    $form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                                }
                            });
                        } else if (xhr.status == 419 || xhr.status == 401) {
                            window.location.href = "";
                        }
                    }
                });
            });

            $(document).on('click', '.delete-slide', function(e) {
                e.preventDefault();
                var confirmed = confirm('Are You sure, you want to delete this image ?');
                var $this = $(this);
                if (!confirmed) {
                    return false;
                }
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: $this.attr('href'),
                    success: function(result) {
                        if (result.status) {
                            window.location.reload();
                        }
                    }
                });
            });


        });
    </script>
@endsection
