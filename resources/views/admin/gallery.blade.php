@extends('layouts.dashboard')
@section('breadcrumb')
    <script type="text/javascript" src="{{ asset('dtree.js') }}"></script>
    <h4 class="page-title-main">Manage Gallery</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">Manage Gallery</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">Upload Images</h4>
                <div class="" id="dropzone_gallery">
                    <div class="dz-message needsclick">
                        <i class="h1 text-muted dripicons-cloud-upload"></i>
                        <h3>Drop images here or click to upload.</h3>
                        <span class="text-muted font-13">only .jpeg, .jpg and .png files are allowed.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">Manage Images</h4>
                <div id="gallery"></div>
            </div>
        </div>
    </div>
    <div id="add-to-slider-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">Add Slider Data</h4>
                    <form id="add-to-slider-form" class="form-horizontal" action="{{ route('admin.gallery.slider_image') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <img id="slider_image_preview" src="">
                                    <input type="hidden" id="image_id" name="image_id">
                                </div>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="slider" name="slider" style="position: relative;">
                                        <label> Add To Slider </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Slider Title</label>
                                    <input id="title" name="title" type="text" class="form-control" placeholder="Title *">
                                </div>
                                <div class="form-group">
                                    <label>Slider Subtitle</label>
                                    <input id="subtitle" name="subtitle" type="text" class="form-control" placeholder="Subtitle *">
                                </div>
                                <div class="form-group">
                                    <label>Description</label> <small>(max: 160 characters)</small>
                                    <textarea class="form-control" name="description" placeholder="Description" id="description" maxlength="160"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Button Label</label>
                                    <input id="btn_label" name="btn_label" type="text" class="form-control" placeholder="Button Label">
                                </div>
                                <div class="form-group">
                                    <label>Button Url</label>
                                    <input id="btn_url" name="btn_url" type="text" class="form-control" placeholder="Button Url">
                                </div>
                                <div class="form-group">
                                    <label>Slide Order</label>
                                    <input id="slide_order" name="slide_order" type="number" class="form-control" placeholder="Slide Order">
                                </div>
                            </div>
                        </div>
                        <div class="form-group account-btn text-center m-t-10">
                            <div class="col-12">
                                <button class="btn width-lg btn-rounded btn-primary waves-effect waves-light" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
@section('css')
    <link href="{{ asset('libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('justifiedGallery/justifiedGallery.min.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .jg-entry .image_overlay {
            position: absolute;
            top: 0;
            right: 00;
            background: #040404c7;
            padding: 10px;
            width: 100%;
            height: 100%;
            transition: all 0.5s ease;
            transform: translateY(100%);
            display: table;
        }

        .jg-entry:hover .image_overlay {
            transform: translate(0);
        }

        .btn-container {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

    </style>
@endsection
@section('js')
    <script src="{{ asset('libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('justifiedGallery/jquery.justifiedGallery.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            var uploadedDocumentMap = {}
            var myDropzone = new Dropzone("div#dropzone_gallery", {
                url: "{{ route('admin.gallery.upload') }}",
                parallelUploads: 1,
                maxFilesize: 2, // MB
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            $('#dropzone_gallery').addClass('dropzone');

            myDropzone.on('success', function(file, response) {
                $('#gallery').prepend('<a href="javascript:void(0)">' +
                    '<img src="' + response.thumb_link + '" />' +
                    '<div class="image_overlay">' +
                    '<span class="btn-container">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger m-1 btn-rounded delete_image" data-toggle="tooltip" data-id="' + response.id + '" title="Delete"><i class="fe-trash"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-outline-warning m-1 btn-rounded set_as_slider" data-toggle="tooltip" data-id="' + response.id + '" title="Set As Slider image"><i class="fe-image"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-outline-info m-1 btn-rounded view_image" data-toggle="tooltip" data-url="' + response.url + '" title="View"><i class="fe-eye"></i></button>' +
                    '</span></div></a>');

                $('#gallery').justifiedGallery();
                $('*[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover'
                });
                // $(file.previewElement).remove();
            });
            myDropzone.on('error', function(file, response) {
                // console.log(response.errors.file[0]);
                $(file.previewElement).addClass("dz-error").find('.dz-error-message').text(response.errors.file[0]);
            });
            myDropzone.on("complete", function(file) {
                myDropzone.removeFile(file);
            });


            $(document).on('click', '.delete_image', function() {
                var confirmed = confirm('Are You sure, you want to delete this image ?');
                var $this = $(this);
                if (!confirmed) {
                    return false;
                }
                var image_id = $(this).data('id');
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '{{ route('admin.gallery.delete') }}',
                    data: {
                        id: image_id
                    },
                    success: function(result) {
                        // console.log(result);
                        $this.parents('a').remove();
                        $('#gallery').justifiedGallery();
                    }
                });
            });

            $(document).on('click', '.view_image', function() {
                window.open($(this).data('url'));
            });

            $(document).on('click', '.set_as_slider', function() {
                var image_id = $(this).data('id');
                $('#add-to-slider-form').prev('.alert').remove();
                $('#add-to-slider-form').find('.text-danger').remove();
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '{{ route('admin.gallery.slider_image') }}',
                    data: {
                        id: image_id
                    },
                    success: function(result) {
                        // console.log(result);
                        if (result.slider == 1) {
                            $('#slider').prop('checked', true);
                        } else {
                            $('#slider').prop('checked', false);
                        }
                        $('#slider_image_preview').attr('src', result.thumb_link);
                        $('#image_id').val(result.id);
                        $('#title').val(result.title);
                        $('#subtitle').val(result.subtitle);
                        $('#description').val(result.description);
                        $('#slide_order').val(result.slide_order);
                        $('#btn_url').val(result.btn_url);
                        $('#btn_label').val(result.btn_label);
                        $('#add-to-slider-modal').modal('show');
                    }
                });
            });

            $(document).on('submit', '#add-to-slider-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                $form.find('.text-danger').remove();
                $form.prev('.alert').remove();
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '{{ route('admin.gallery.slider_image') }}',
                    data: $form.serialize(),
                    success: function(result) {
                        // console.log(result);
                        if (result) {
                            $form.trigger('reset');
                            $('#add-to-slider-modal').modal('hide');
                        } else {
                            $form.before('<div class="alert alert-danger">Sorry somthing went wrong, please try again.</div>');
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

            appendGallery();

            $('#gallery').justifiedGallery({
                rowHeight: 200,
                margins: 5
            });

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                    appendGallery();
                }
            });

            function appendGallery() {
                var start = $('#gallery a').length;
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '{{ route('admin.gallery.list') }}',
                    data: {
                        start: start
                    },
                    success: function(result) {
                        $.each(result, function(index, item) {
                            // console.log(item.thumb_link);
                            $('#gallery').append('<a href="javascript:void(0)">' +
                                '<img src="' + item.thumb_link + '" />' +
                                '<div class="image_overlay">' +
                                '<span class="btn-container">' +
                                '<button type="button" class="btn btn-sm btn-outline-danger m-1 btn-rounded delete_image" data-toggle="tooltip" data-id="' + item.id + '" title="Delete"><i class="fe-trash"></i></button> &nbsp;' +
                                '<button type="button" class="btn btn-sm btn-outline-warning m-1 btn-rounded set_as_slider" data-toggle="tooltip" data-id="' + item.id + '" title="Set As Slider image"><i class="fe-image"></i></button> &nbsp;' +
                                '<button type="button" class="btn btn-sm btn-outline-primary m-1 btn-rounded view_image" data-toggle="tooltip" data-url="' + item.url + '" title="View"><i class="fe-eye"></i></button>' +
                                '</span></div></a>');
                        });
                        $('#gallery').justifiedGallery();
                        $('*[data-toggle="tooltip"]').tooltip({
                            trigger: 'hover'
                        });
                    }
                });
            }


        });
    </script>
@endsection
