<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ $page_title ?? '' }} - {{ get_setting('website_title') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="BCplus News" name="description" />
    <meta content="BCplus News" name="developers" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('/favicon.png') }}">
    @yield('css')
    <link href="{{ asset('libs/spinkit/spinkit.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('libs/tooltipster/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/custom-style.css') }}" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="loader">
        <div class="sk-wave">
            <div class="sk-rect sk-rect1"></div>
            <div class="sk-rect sk-rect2"></div>
            <div class="sk-rect sk-rect3"></div>
            <div class="sk-rect sk-rect4"></div>
            <div class="sk-rect sk-rect5"></div>
        </div>
    </div>
    <div id="wrapper">
        <div class="left-side-menu">
            <div class="slimscroll-menu">
                <div class="logo-box">
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <span class="logo-lg">
                            <img src="{{ storage_url(get_setting('logo')) }}" alt="{{ get_setting('website_title') }}">
                        </span>
                        <span class="logo-sm">
                            <img src="{{ storage_url(get_setting('logo')) }}" alt="{{ get_setting('website_title') }}" height="24">
                        </span>
                    </a>
                </div>
                <div class="user-box">
                    {{-- <img src="{{ asset('admin/images/users/avatar-1.jpg') }}" alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail avatar-md">
                    <div class="dropdown">
                        <a href="{{ route('admin.dashboard') }}" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown">{{ auth()->guard('admin')->user()->name }}</a>
                    </div>
                    <p class="text-muted">Admin Head</p> --}}
                </div>
                <div id="sidebar-menu">
                    <ul class="metismenu" id="side-menu">
                        @include('layouts.sidebar')
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="content-page">
            <div class="navbar-custom">
                <ul class="list-unstyled topnav-menu float-right mb-0">
                    <li class="notification-list">
                        <a class="nav-link dropdown-toggle nav-user mr-0" href="{{ route('admin.app_cache_update') }}" role="button">
                            <span class="btn btn-info btn-sm" title="{{ __('common.app_cache_update') }}" data-toggle="tooltip">
                                <i class="fe-refresh-cw"></i>
                            </span>
                        </a>
                    </li>
                    <li class="dropdown language-list">
                        <a class="nav-link dropdown-toggle  nav-user mr-0 waves-effect" data-toggle="dropdown" href="javscript:void(0)" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ asset('admin/images/lang/en.png') }}" alt="user-image" class="rounded-circle">
                            <span class="pro-user-name ml-1"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right language-dropdown ">
                            @php
                                $langs = get_app_language_details();
                                $langs_locale = app()->getLocale();
                            @endphp
                            @foreach ($langs as $key => $value)
                                @php
                                    if ($langs_locale == $key) {
                                        $class = 'active';
                                    } else {
                                        $class = '';
                                    }
                                @endphp

                                <a href="{{ route('admin.lang', $key) }}" data-locale="{{ $value['name'] }}" class="{{ $class }} dropdown-item notify-item">
                                    <img src="{{ $value['icon'] }}" alt="user-image" class="rounded-circle" height="25px">
                                    <span>{{ $value['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </li>
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="javscript:void(0)" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ asset('admin/images/users/avatar-1.jpg') }}" alt="user-image" class="rounded-circle">
                            <span class="pro-user-name ml-1">
                                {{ auth()->guard('admin')->user()->name }} <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                            <div class="dropdown-item noti-title">
                                <h6 class="text-overflow m-0">{{ __('common.user_menu.welcome') }}</h6>
                            </div>
                            <a href="{{ route('admin.settings') }}" class="dropdown-item notify-item">
                                <i class="fe-settings"></i> <span>{{ __('common.user_menu.settings') }}</span>
                            </a>
                            <a class="dropdown-item notify-item" href="{{ route('admin.logout') }}" id="admin_logout" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                <i class="fe-power"></i> <span>{{ __('common.user_menu.logout') }}</span>
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile disable-btn waves-effect">
                            <i class="fe-menu"></i>
                        </button>
                    </li>
                    <li>
                        @yield('breadcrumb')
                    </li>
                </ul>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            {!! error_alerts($errors) !!}
                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            2019 - {{ date('Y') }} © {{ get_setting('website_title') }} Powered By @ <span class="d-none d-sm-inline-block"><a href="https://www.cosonas.com/">Cosonas.com</a></span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('admin/js/vendor.min.js') }}"></script>
    <script src="{{ asset('libs/tooltipster/tooltipster.bundle.min.js') }}"></script>

    <script type="text/javascript">
        var src = $('.language-dropdown .active img').attr('src');
        var locale = $('.language-dropdown .active').data('locale');
        $('.language-list .dropdown-toggle').append(locale);
        $('.language-list .dropdown-toggle img').attr('src', src);

        //datatable sort currency column
        function dt_sort_currency(jQuery) {
            jQuery.extend(jQuery.fn.dataTableExt.oSort, {
                "currency-pre": function(a) {
                    return parseFloat(a.replace(/ /gi, ''));
                },
                "currency-asc": function(a, b) {
                    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                },
                "currency-desc": function(a, b) {
                    return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                }
            });
        }

        //datable custom search
        function dt_search(dt_table_object) {
            var container = dt_table_object.table().container();
            var dt_search = $(container).find('div.dataTables_filter input');
            dt_search.unbind();
            var conatiner_id = $(container).attr('id')
            var search_id = conatiner_id.replace('wrapper', 'search');
            dt_search.after('<button class="btn btn-sm btn-primary" id="' + search_id + '"><i class="fe-search"></i></button>');
            $("#" + search_id).click(function() {
                var keyword = dt_search.val();
                dt_table_object.search(keyword).draw();
            });
            dt_search.keyup(function(e) {
                if (e.keyCode == 13) {
                    dt_table_object.search(this.value).draw();
                }
            });
        }

        function previewImage(input) {
            var width = $(input).data('width');
            var height = $(input).data('height');
            var id = $(input).attr('id');
            if (input.files && input.files[0]) {
                var fileType = input.files[0].type;
                if (fileType == 'image/png' || fileType == 'image/jpeg' || fileType == 'image/gif') {
                    $(input).siblings('.previewImage').remove();
                    $(input).siblings('.text-danger').remove();
                    $('input[name="' + id + '"]').remove();
                    var reader = new FileReader();
                    var preview;
                    reader.onload = function(e) {
                        preview = '<img style="max-width:100%;" class="mt-2 mb-2 previewImage" width="' + width + '" height="' + height + '" src="' + e.target.result + '"><input type="hidden" name="' + id + '" value="' + e.target.result + '">';
                        $(input).after(preview);
                        // console.log(e.target);
                    }
                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                } else {
                    $('<div class="text-danger">Unsupported image type, only png, gif and jpg images supported</div>').insertAfter(input);
                }
            }
        }

        function session_error(xhr) {
            if (xhr.status == 419 || xhr.status == 401) {
                $('.loader').hide();
                Swal.fire({
                    type: "error",
                    title: "Error!",
                    text: "Oops!!! Session Expired, Please Login Again.",
                    confirmButtonClass: "btn btn-confirm mt-2"
                }).then(function() {
                    window.location.href = "";
                });
            }
        }

        $(function() {
            $('.loader').fadeOut();
            // $(document).find('[data-toggle="tooltip"]').tooltip();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#admin_logout').click(function() {
                $.ajax({
                    url: "{{ route('admin.logout') }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        console.log(result);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });

            modify_number_inputs($);

            $(document).on('click', '.btn-plus, .btn-minus', function(e) {
                const isNegative = $(e.target).closest('.btn-minus').is('.btn-minus');
                const input = $(e.target).closest('.input-group').find('input');
                if (input.is('input')) {
                    input[0][isNegative ? 'stepDown' : 'stepUp']()
                }
            });
        });

        function modify_number_inputs($) {
            $(document).find('input[type="number"]').each(function(index, element) {
                if ($(this).hasClass('changed')) {
                    return true;
                }
                $(this).addClass('changed');
                var before = '<div class="input-group inline-group">' +
                    '<div class="input-group-prepend">' +
                    '<button type="button" class="btn btn-outline-secondary btn-minus" tabindex="-1">' +
                    '<i class="fa fa-minus"></i>' +
                    '</button>' +
                    '</div>';

                var after = '<div class="input-group-append">' +
                    '<button type="button" class="btn btn-outline-secondary btn-plus" tabindex="-1">' +
                    '<i class="fa fa-plus"></i>' +
                    '</button>' +
                    '</div>' +
                    '</div>';
                $(element).after(before + element.outerHTML + after);
                element.remove();
            });
        }
    </script>
    @yield('js')
    <!-- App js -->
    <script src="{{ asset('admin/js/app.min.js') }}"></script>
</body>

</html>
