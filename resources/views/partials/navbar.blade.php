<nav class="navbar navbar-expand fixed-top">
    <div class="container-fluid">
        <button class="btn toggle-sidebar-btn" id="toggle-sidebar-btn" type="button" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand" href="/">Deming</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="/bob/index?attribute=none&period=0&scope=none&domain=0&status=2" class="nav-icon"
               title="{{ __('menu.new') }}" aria-label="{{ __('menu.new') }}">
                <i class="bi bi-envelope"></i>
                @if (Session::get("planed_controls_this_month_count")!=null)
                <span class="badge rounded-pill bg-success">{{ Session::get("planed_controls_this_month_count") }}</span>
                @endif
            </a>
            <a href="/bob/index?attribute=none&period=99&scope=none&domain=0&status=1&late=1" class="nav-icon"
               title="{{ __('menu.late') }}" aria-label="{{ __('menu.late') }}">
                <i class="bi bi-bell"></i>
                @if (Session::get("late_controls_count")!=null)
                <span class="badge rounded-pill bg-danger">{{ Session::get("late_controls_count") }}</span>
                @endif
            </a>
            <a href="/actions" class="nav-icon"
               title="{{ __('menu.action_plan') }}" aria-label="{{ __('menu.action_plan') }}">
                <i class="bi bi-flag"></i>
                @if (Session::get("action_plans_count")!=null)
                <span class="badge rounded-pill bg-primary">{{ Session::get("action_plans_count") }}</span>
                @endif
            </a>
            @if(auth()->user()->isAdmin() || auth()->user()->isUser())
            <a href="/group/toggle" class="nav-icon group-view-toggle {{ auth()->user()->seesAllData() ? 'group-view-active' : '' }}"
               title="{{ auth()->user()->seesAllData() ? __('common.my_data')  : __('common.all_data') }}"
               aria-label="{{ auth()->user()->seesAllData() ? __('common.my_data') : __('common.all_data') }}">
                <i class="bi bi-people"></i>
            </a>
            @endif
            <a href="/users/{{ Auth::User()->id }}/edit" class="nav-icon"
               title="{{ __('menu.profile') }}" aria-label="{{ __('menu.profile') }}">
                <i class="bi bi-person"></i>
                <span class="badge rounded-pill bg-dark">{{ Auth::User()->initiales() }}</span>
            </a>
            <a href="/about" target="_blank" rel="noopener noreferrer" class="nav-icon" aria-label="About">
                <i class="bi bi-question-circle"></i>
            </a>
        </div>
    </div>
</nav>
