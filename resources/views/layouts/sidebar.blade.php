<li>
    <a href="{{ route('admin.dashboard') }}">
        <i class="fe-airplay"></i>
        <!-- <span class="badge badge-danger float-right">3</span> -->
        <span>{{ __('common.sidebar.dashboard') }} </span>
    </a>
</li>
<li>
    <a href="javascript: void(0);">
        <i class="fe-settings"></i>
        <span>{{ __('common.sidebar.settings') }}</span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="{{ route('admin.settings') }}">{{ __('common.sidebar.admin_settings') }}</a></li>
        <li><a href="{{ route('admin.home-slider') }}">{{ __('common.sidebar.home_slider') }}</a></li>
        <li><a href="{{ route('admin.mail_settings') }}">{{ __('common.sidebar.mail_settings') }}</a></li>
        <li><a href="{{ route('admin.payment_settings') }}">{{ __('common.sidebar.payment_settings') }}</a></li>
        <li><a href="{{ route('admin.agora_settings') }}">{{ __('common.sidebar.agora_settings') }}</a></li>
        <li>
            <a href="javascript: void(0);">
                <span>{{ __('common.sidebar.setting_options') }}</span>
                <span class="menu-arrow"></span>
            </a>
            <ul class="nav-third-level" aria-expanded="false">
                @foreach (get_setting_fields() as $value)
                    <li><a href="{{ route('admin.setting-options', $value) }}">{{ __('common.setting_options_fields.' . $value) }}</a></li>
                @endforeach
            </ul>
        </li>
    </ul>
</li>
<li>
    <a href="javascript: void(0);">
        <i class="fe-file-text"></i>
        <span> {{ __('common.sidebar.pages') }} </span>
        <span class="menu-arrow"></span>
    </a>

    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="{{ route('admin.page.create') }}">{{ __('common.sidebar.create_page') }}</a></li>
        <li><a href="{{ route('admin.page.index') }}">{{ __('common.sidebar.all_pages') }}</a></li>
    </ul>
</li>
<li>
    <a href="javascript: void(0);">
        <i class="fe-star-on"></i>
        <span>{{ __('common.sidebar.packages') }}</span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="{{ route('admin.package.index') }}">{{ __('common.sidebar.all_packages') }}</a></li>
        <li><a href="{{ route('admin.package.create') }}">{{ __('common.sidebar.create_package') }}</a></li>
        <li><a href="{{ route('admin.package_orders') }}">{{ __('common.sidebar.package_orders') }}</a></li>
    </ul>
</li>
<li>
    <a href="{{ route('admin.events') }}">
        <i class="mdi mdi-calendar-account"></i>
        <!-- <span class="badge badge-danger float-right">3</span> -->
        <span>{{ __('common.sidebar.events') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.attendees_reports') }}">
        <i class="fe-file-text"></i>
        <span>{{ __('common.sidebar.attendees_reports') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.show_attendee_check') }}">
        <i class="fe-file-text"></i>
        <span>{{ __('common.sidebar.attendee_check') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.support.index') }}">
        <i class="fe-help-circle"></i>
        <span>{{ __('common.sidebar.support_messages') }} </span>
    </a>
</li>

<li>
    <a href="javascript: void(0);">
        <i class="fe-users"></i>
        <span>{{ __('common.sidebar.users') }} </span>
        <span class="menu-arrow"></span>
    </a>

    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="{{ route('admin.user.index') }}">{{ __('common.sidebar.all_users') }}</a></li>
        <li><a href="{{ route('admin.user.index', 'approved') }}">{{ __('common.sidebar.approved_users') }}</a></li>
        <li><a href="{{ route('admin.user.index', 'unapproved') }}">{{ __('common.sidebar.unapproved_users') }}</a></li>
        <li><a href="{{ route('admin.user.index', 'suspended') }}">{{ __('common.sidebar.suspended_users') }}</a></li>
    </ul>
</li>
