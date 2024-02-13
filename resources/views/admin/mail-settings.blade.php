@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('settings.mail.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('settings.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.mail.breadcrumb_name') }}</li>
    </ol>
    <style>
        .ck-editor__editable {
            min-height: 350px;
            color: #000;
        }

    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card-box">
                <h4 class="header-title text-success">{{ __('settings.mail.box_heading') }}</h4>
                <form id="mail_settings_form" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.mailer') }}</label>
                        <select class="form-control" name="mail_mailer">
                            <option value="smtp">SMTP</option>
                            <option value="sendmail">Mail</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.host') }}</label>
                        <input type="text" class="form-control" name="mail_host" placeholder="{{ __('settings.mail.host') }}" value="{{ $mail_settings['mail_host'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.port') }}</label>
                        <input type="text" class="form-control" name="mail_port" placeholder="{{ __('settings.mail.port') }}" value="{{ $mail_settings['mail_port'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.username') }}</label>
                        <input type="text" class="form-control" name="mail_username" placeholder="{{ __('settings.mail.username') }}" value="{{ $mail_settings['mail_username'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.password') }}</label>
                        <input type="text" class="form-control" name="mail_password" placeholder="{{ __('settings.mail.password') }}" value="{{ $mail_settings['mail_password'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.encryption') }}</label>
                        <input type="text" class="form-control" name="mail_encryption" placeholder="{{ __('settings.mail.encryption') }}" value="{{ $mail_settings['mail_encryption'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.from_name') }}</label>
                        <input type="text" class="form-control" name="mail_from_name" placeholder="{{ __('settings.mail.from_name') }}" value="{{ $mail_settings['mail_from_name'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('settings.mail.from_email') }}</label>
                        <input type="text" class="form-control" name="mail_from_address" placeholder="{{ __('settings.mail.from_email') }}" value="{{ $mail_settings['mail_from_address'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info">{{ __('settings.update_btn') }}</button>
                    </div>
                </form>
                <!-- end row -->
            </div>
        </div>
        <div class="col-md-8">
            <div class="card-box">
                <h4 class="header-title text-success">{{ __('settings.mail.templates.box_heading') }}</h4>
                <p class="sub-header">{{ __('settings.mail.templates.box_subheading') }}</p>
                <form id="mail_templates_form" method="post" action="{{ route('admin.update_mail_template') }}" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <select class="form-control" name="template_name">
                        <option selected disabled value="">{{ __('settings.mail.templates.select_template') }}</option>
                        @foreach ($mail_templates as $key => $name)
                            <option value="{{ $key }}">{{ $name['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="mail-template-form-body p-2 mt-3" style="display: none;">
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.mail.templates.subject') }}</label>
                            <input type="text" class="form-control" name="subject" placeholder="{{ __('settings.mail.templates.subject') }}" value="{{ $mail_settings['mail_subject'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.mail.templates.content') }}</label>
                            <textarea id="ckEditor" class="form-control" name="content" value="{{ $mail_settings['mail_content'] ?? '' }}"></textarea>
                            <div id="word-count"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('settings.mail.templates.varibales') }}</label>
                            <p class="sub-header mb-1">{{ __('settings.mail.templates.varibale_instructions') }}</p>
                            <ul class="mail_varibales p-0">
                                {{-- @foreach ($mail_varibales as $var)
                            <li data-val="{{ $var }}">{{ str_replace('_', ' ', $var) }}</li>
                            @endforeach --}}
                            </ul>
                        </div>
                        <button type="'submit" class="btn btn-info">{{ __('settings.update_btn') }}</button>
                    </div>
                </form>
            </div>
            <div class="card-box">
                <h4 class="header-title text-success mb-2">{{ __('settings.mail.footer_links.box_heading') }}</h4>
                <p class="sub-header">{{ __('settings.mail.footer_links.box_subheading') }}</p>
                <div class="form-group mb-3">
                    <div id="sortable">
                        @php
                            if (empty($mail_settings['mail_footer_links'])) {
                                $footer_links = [];
                            } else {
                                $footer_links = json_decode($mail_settings['mail_footer_links'], true);
                            }
                            // print_r($footer_links);
                            // die;
                        @endphp
                        @foreach ($footer_links as $key => $link_data)
                            <?php //print_r($link_data);
                            ?>
                            <div class="mb-2 d-flex justify-content-between footer_links_block ui-state-default">
                                <div class="align-self-center">
                                    <div class="p-1" title="Move Up Or Down">
                                        <i class="fe-move"></i>
                                    </div>
                                </div>
                                <div class="footer-links-inputs flex-fill">
                                    <input type="text" class="form-control mb-1" name="text" placeholder="{{ __('settings.mail.footer_links.text') }}" value="{{ $link_data['text'] }}" autocomplete="off">
                                    <input type="text" class="form-control" name="url" placeholder="{{ __('settings.mail.footer_links.url') }}" value="{{ $link_data['url'] }}" autocomplete="off">
                                </div>
                                <div class="footer-links-submit align-self-stretch">
                                    <button type="button" class="btn btn-lg btn-danger h-100 remove_footer_link">
                                        <i class="fe-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-2 d-flex justify-content-between footer_links_block" id="footer_links">
                        <div class="align-self-center d-none">
                            <div class="p-1" title="Move Up Or Down">
                                <i class="fe-move"></i>
                            </div>
                        </div>
                        <div class="footer-links-inputs flex-fill">
                            <input type="text" class="form-control mb-1" name="text" placeholder="{{ __('settings.mail.footer_links.text') }}" value="">
                            <input type="text" class="form-control" name="url" placeholder="{{ __('settings.mail.footer_links.url') }}" value="">
                        </div>
                        <div class="footer-links-submit align-self-stretch">
                            <button type="button" class="btn btn-lg btn-dark h-100 add_footer_link">
                                <i class="fe-check-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-info" onclick="update_footer_links();">{{ __('settings.update_btn') }}</button>
            </div>
        </div>

    </div>
@endsection
@section('css')
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('ckeditor/ckeditor-dark-theme.css') }}"> --}}
@endsection
@section('js')
    <script src="{{ asset('libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            var mailer = "{{ $mail_settings['mail_mailer'] ?? 'smtp' }}";

            $('[name="mail_mailer"]').val(mailer);


            $("#sortable").sortable();
            $("#sortable").disableSelection();

            var footer_links_index = parseInt('{{ time() }}');

            $(document).on('click', '.add_footer_link', function() {
                var cloned = $('#footer_links')
                    .clone()
                    .attr('id', '')
                    .appendTo('#sortable');
                cloned.find('.d-none').removeClass('d-none');
                cloned.find('.add_footer_link')
                    .html('<i class="fe-x-circle"></i>')
                    .removeClass('add_footer_link btn-dark')
                    .addClass('btn-danger remove_footer_link');
                cloned.find('input').each(function(item) {
                    // $(this)
                    //     // .attr('name', 'mail_footer_links[' + footer_links_index + '][' + $(this).data('name') +']')
                    //     .prop('readonly', true);
                    // .val($(this).data('name') +
                    //     '_' + footer_links_index);
                })
                $('#footer_links').find('input').val('');
                footer_links_index++;

                // update_footer_links();

            });

            $(document).on('click', '.remove_footer_link', function() {
                $(this).parents('.footer_links_block').remove();
                // update_footer_links();
            });


            $(document).on('click', '.mail_varibales li', function() {
                var code = '{' + '{' + $(this).data('val') + '}' + '}';
                editor.model.change(writer => {
                    const linkedText = writer.createText(code);
                    editor.model.insertContent(linkedText, editor.model.document.selection);
                });
            });

            $('[name="template_name"]').change(function() {
                var form_body = $('.mail-template-form-body');
                form_body.slideUp();

                var form = $('#mail_templates_form');
                form.prev('.alert').remove();
                form.find('.text-danger').remove();
                $('.loader').fadeIn();
                var template_name = $(this).val();
                // console.log(template_name)
                if (template_name) {
                    $.ajax({
                        url: "{{ route('admin.get_mail_template') }}",
                        dataType: 'JSON',
                        data: {
                            template_name: template_name
                        },
                        success: function(result) {
                            $('.loader').fadeOut();
                            form_body.slideDown();
                            $('.mail_varibales').html('');
                            if (result.data) {
                                $('[name="subject"]').val(result.data.subject);
                                editor.setData(result.data.content);
                                $.each(result.mail_varibales, function(k, v) {
                                    $('.mail_varibales').append('<li data-val="' + v + '">' + v.replaceAll('_', ' ') + '</li>');
                                });
                            }
                        },
                        error: function(xhr) {
                            $('.loader').fadeOut();
                            if (xhr.status == 422) {
                                alert('invalid template name');
                            } else if (xhr.status == 419 || xhr.status == 401) {
                                window.location.href = "";
                            }
                            // console.log(xhr);
                        }
                    });
                }
            });

            $('#mail_templates_form').submit(function(e) {
                e.preventDefault();
                // console.log(editor.getData());
                // return false;
                $('.loader').fadeIn();
                var form = $(this);
                form.prev('.alert').remove();
                form.find('.text-danger').remove();
                var url = form.attr('action');
                var type = form.attr('method');
                form.find('[name="content"]').val(editor.getData());
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
                            // form[0].reset();
                            // window.location.href = "";
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        $('.loader').fadeOut();
                        if (xhr.status == 422) {
                            var errors_html = "";
                            errors_html += '<p class="text-uppercase font-weight-bolder">' + xhr.responseJSON.message + '</p>';
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                // form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                                errors_html += '<p class="m-0"><b class="font-weight-bolder text-uppercase">' + k + '</b>: ' + v + ' </p>';
                            });
                            form.before('<div class="alert alert-danger">' + errors_html + '</div>');
                        } else if (xhr.status == 419 || xhr.status == 401) {
                            window.location.href = "";
                        }
                    }
                });
            });

            $('#mail_settings_form').submit(function(e) {
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
                                form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                            });
                        } else if (xhr.status == 419) {
                            window.location.href = "";
                        }
                        // console.log(xhr);
                    }
                });
            });


            var TempEditor = ClassicEditor
                .create(
                    document.querySelector('#ckEditor'), {
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
                                // 'superscript',
                                // 'subscript',
                                // 'numberedList',
                                // 'bulletedList',
                                '|',
                                'imageUpload',
                                // 'indent',
                                // 'outdent',
                                // 'blockQuote',
                                // 'codeblock',
                                // 'insertTable',
                                // 'mediaEmbed',
                                // 'pageBreak',
                                'link',
                                // 'CKFinder',
                                // 'specialCharacters',
                                // 'code',
                                // 'undo',
                                // 'redo'
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
                    }
                )
                .then(editor => {
                    window.editor = editor;
                    const wordCountPlugin = editor.plugins.get('WordCount');
                    const wordCountWrapper = document.getElementById('word-count');
                    wordCountWrapper.appendChild(wordCountPlugin.wordCountContainer);
                })
                .catch(error => {
                    console.warn('Please contact developer for support.');
                    console.error(error);
                });






        });

        function update_footer_links() {
            var mail_footer_links = [];
            $('.loader').fadeIn();
            $('.footer_links_block').not('#footer_links').each(function() {
                mail_footer_links.push({
                    text: $(this).find('input[name="text"]').val(),
                    url: $(this).find('input[name="url"]').val()
                });
            })

            $.ajax({
                url: "{{ route('admin.update-website-settings') }}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    mail_footer_links: mail_footer_links
                },
                success: function(result) {
                    $('.loader').fadeOut();
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
        }
    </script>
@endsection
