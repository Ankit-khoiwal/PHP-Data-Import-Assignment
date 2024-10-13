<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <form action="{{ route('admin.dashboard') }}">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Search..."
                        aria-label="Search..." name="q" value="{{ request('q') }}" />
                </div>
            </form>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ asset('backend') }}/assets/img/avatars/user.jpg" alt
                            class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('backend') }}/assets/img/avatars/user.jpg" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                @if(auth()->check())
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ ucFirst(auth()->user()->name) }}</span>
                                    <small class="text-muted">{{ auth()->user()->role->name }}</small>
                                </div>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

            </li>
            <li>
                {{-- <a class="dropdown-item" href="{{ route('auth.logout') }}">
                    <i class="bx bx-power-off me-2"></i>
                    <span class="align-middle">Log
                        Out</span>
                </a> --}}
            </li>
        </ul>
        </li>
        <!--/ User -->
        </ul>
    </div>
</nav>
