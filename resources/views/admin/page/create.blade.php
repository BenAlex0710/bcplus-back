@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ $page_title }} </h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('package.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
    <style type="text/css">
        .ck-editor__editable_inline {
            min-height: 500px !important;
        }

    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="card-box">
                <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">{{ __('page.create_new_page') }}</h4>
                <form id="create-page" method="post" action="{{ route('admin.page.save') }}">
                    @csrf

                    @foreach ($langs as $lang)
                        <div class="form-group clearfix">
                            <label class="control-label" for="{{ $lang }}_title">{{ __('page.' . $lang . '_title') }}</label>
                            <input class="form-control required" id="{{ $lang }}_title" name="{{ $lang }}_title" type="text">
                        </div>
                        <div class="form-group clearfix">
                            <label class="control-label" for="s">{{ __('page.' . $lang . '_description') }}</label>
                            <textarea class="ckEditor" name="{{ $lang }}_description"></textarea>
                            <div id="word-count"></div>
                        </div>
                    @endforeach

                    <div class="form-group clearfix">
                        <button class="btn btn-info" type="submit">{{ __('page.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('css')
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('ckeditor/ckeditor-dark-theme.css') }}"> --}}
@endsection
@section('js')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('.ckEditor').each(function(ele) {

                ClassicEditor.create(this, {
                        heading: {
                            options: [{
                                    model: 'paragraph',
                                    title: 'Paragraph',
                                    class: 'ck-heading_paragraph'
                                },
                                {
                                    model: 'heading1',
                                    view: 'h1',
                                    title: 'Heading 1',
                                    class: 'ck-heading_heading1'
                                },
                                {
                                    model: 'heading2',
                                    view: 'h2',
                                    title: 'Heading 2',
                                    class: 'ck-heading_heading2'
                                },
                                {
                                    model: 'heading3',
                                    view: 'h3',
                                    title: 'Heading 3',
                                    class: 'ck-heading_heading3'
                                },
                                {
                                    model: 'heading4',
                                    view: 'h4',
                                    title: 'Heading 4',
                                    class: 'ck-heading_heading4'
                                },
                                {
                                    model: 'heading5',
                                    view: 'h5',
                                    title: 'Heading 5',
                                    class: 'ck-heading_heading5'
                                },
                                {
                                    model: 'heading6',
                                    view: 'h6',
                                    title: 'Heading 6',
                                    class: 'ck-heading_heading6'
                                },
                            ]
                        },
                        toolbar: {
                            items: [
                                'heading',
                                'fontSize',
                                'fontFamily',
                                '|',
                                'fontBackgroundColor',
                                'fontcolor',
                                'bold',
                                'italic',
                                'underline',
                                'strikethrough',
                                'alignment',
                                'superscript',
                                'subscript',
                                'numberedList',
                                'bulletedList',
                                '|',
                                'imageUpload',
                                'indent',
                                'outdent',
                                'blockQuote',
                                'codeblock',
                                'insertTable',
                                'mediaEmbed',
                                'pageBreak',
                                'link',
                                'CKFinder',
                                'specialCharacters',
                                'code',
                                'undo',
                                'redo'
                            ]
                        },
                        language: 'en',
                        table: {
                            contentToolbar: [
                                'tableColumn',
                                'tableRow',
                                'mergeTableCells',
                                'tableCellProperties'
                            ]
                        },
                        licenseKey: '',
                        simpleUpload: {
                            // The URL that the images are uploaded to.
                            uploadUrl: "{{ route('admin.ck_image_upload') }}",
                            // Enable the XMLHttpRequest.withCredentials property.
                            withCredentials: true,
                            // Headers sent along with the XMLHttpRequest to the upload server.
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                // Authorization: 'Bearer <JSON Web Token>'
                            }
                        },
                        image: {
                            // Configure the available styles.
                            styles: [
                                'alignLeft', 'alignCenter', 'alignRight'
                            ],

                            // Configure the available image resize options.
                            resizeOptions: [{
                                    name: 'imageResize:original',
                                    label: 'Original',
                                    value: null
                                },
                                {
                                    name: 'imageResize:25',
                                    label: '25%',
                                    value: '25'
                                }, {
                                    name: 'imageResize:50',
                                    label: '50%',
                                    value: '50'
                                },
                                {
                                    name: 'imageResize:75',
                                    label: '75%',
                                    value: '75'
                                }
                            ],

                            // You need to configure the image toolbar, too, so it shows the new style
                            // buttons as well as the resize buttons.
                            toolbar: [
                                'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight',
                                '|',
                                'imageResize',
                                '|',
                                'imageTextAlternative'
                            ]
                        }

                    })
                    .then(editor => {
                        window.editor = editor;
                        // const wordCountPlugin = editor.plugins.get('WordCount');
                        // const wordCountWrapper = document.getElementById('word-count');
                        // wordCountWrapper.appendChild(wordCountPlugin.wordCountContainer);
                    })
                    .catch(error => {
                        // console.error( 'Oops, something went wrong!' );
                        // console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                        console.warn('Please contact developer for support.');
                        console.error(error);
                    });
            });

            $('#create-page').submit(function(e) {
                $('.loader').fadeIn();
                e.preventDefault();
                var form = $(this);
                form.prev('.alert').remove();
                form.find('.text-danger').remove();
                var url = form.attr('action');
                var type = form.attr('method');
                $.ajax({
                    url: url,
                    type: type,
                    dataType: 'json',
                    data: form.serialize(),
                    success: function(result) {
                        $('.loader').fadeOut();
                        // console.log(result);
                        if (!result.status) {
                            form.before('<div class="alert alert-danger">' + result.message + '</div>');
                        } else {
                            form.before('<div class="alert alert-success">' + result.message + '</div>');
                            form[0].reset();
                            window.location.href = "{{ route('admin.page.index') }}";
                        }
                    },
                    error: function(xhr) {
                        $('.loader').fadeOut();
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                            });
                        } else if (xhr.status == 419 || xhr.status == 401) {
                            window.location.href = "";
                        }
                        // console.log(xhr);
                    }
                });
            });


        });
    </script>
@endsection
