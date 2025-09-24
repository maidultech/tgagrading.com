<!-- ======================= header start  ============================ -->
<header class="header_section home_page">
    <div class="container">
        <nav class="navbar navbar-expand-lg p-0">
            <div class="container-fluid p-0">
                <a class="navbar-brand" href="{{ route('frontend.index') }}">
                    <img src="{{ file_exists(public_path($setting->site_logo)) ? asset($setting->site_logo) : asset('assets/default.png') }}"
                        style="max-height: 60px" alt="TGA Grading">
                </a>
                <div class="d-flex align-items-center d-block d-lg-none">
                    @if (auth()->check())
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="ms-3">
                            <div class="dropdown py-2">
                                <button class="dropdown-toggle border rounded-pill p-2 bg-light head-button" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    @php
                                        $firstname  = Auth::user()->name;
                                        $lastname   = Auth::user()->last_name;
                                        $name = strtoupper(Str::substr($firstname, 0, 1) . Str::substr($lastname, 0, 1),);
                                    @endphp
                                    {{ $name }}
                                </button>
                                <ul class="dropdown-menu p-0 overflow-hidden dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                            <img src="{{ asset('frontend/assets/images/icons/user2.svg') }}"
                                                alt="Orders">
                                            Account
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.orders') }}">
                                            <img src="{{ asset('frontend/assets/images/icons/order.svg') }}"
                                                alt="Orders">
                                            Orders
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.ticket.index') }}">
                                            <img src="{{ asset('frontend/assets/images/icons/support.svg') }}" 
                                                alt="Support Tickets">
                                            Support Tickets
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('logout') }}" class="dropdown-item" id="logoutLink-mobile">
                                            <img src="{{ asset('frontend/assets/images/icons/sign_out.svg') }}"
                                                style="margin-left: 2px;" alt="Sign Out">
                                            Sign Out
                                        </a>
                                        <form action="{{ route('logout') }}" method="post" id="logout-form-mobile"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="ms-2">
                            <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
                        </div>
                    @endif

                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto d-flex align-items-center">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.index') ? 'active' : '' }}" href="{{ route('frontend.index') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.howToOrder') ? 'active' : '' }}" href="{{ route('frontend.howToOrder') }}">How to Order</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.pricing') ? 'active' : '' }}" href="{{ route('frontend.pricing') }}">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.certification') ? 'active' : '' }}" href="{{ route('frontend.certification') }}">Certificate Verification</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.partners') ? 'active' : '' }}" href="{{ route('frontend.partners') }}">Partners</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.programs') ? 'active' : '' }}" href="{{ route('frontend.programs') }}">Programs</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.blogs') ? 'active' : '' }}" href="{{ route('frontend.blogs') }}">News</a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" href="{{ route('frontend.contact') }}">Contact Us</a>
                        </li>
                       
                        <li class="nav-item ms-2">
                            @if (auth()->check())
                                <div class="dropdown">
                                    <button class="dropdown-toggle border rounded-pill p-2 bg-light" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $name }}
                                    </button>
                                    <ul class="dropdown-menu p-0 overflow-hidden dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                                <img src="{{ asset('frontend/assets/images/icons/user2.svg') }}"
                                                    alt="Orders">
                                                Account
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.orders') }}">
                                                <img src="{{ asset('frontend/assets/images/icons/order.svg') }}"
                                                    alt="Orders">
                                                Orders
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.ticket.index') }}">
                                                <img src="{{ asset('frontend/assets/images/icons/support.svg') }}" 
                                                    alt="Support Tickets">
                                                Support Tickets
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}" class="dropdown-item" id="logoutLink-desktop">
                                                <img src="{{ asset('frontend/assets/images/icons/sign_out.svg') }}"
                                                    style="margin-left: 2px;" alt="Sign Out">
                                                Sign Out
                                            </a>
                                            <form action="{{ route('logout') }}" method="post" id="logout-form-desktop"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- mobile menu -->
        <div class="mobile_menu offcanvas offcanvas-start" tabindex="-1" id="mobileMenu"
            aria-labelledby="mobileMenuLabel">
            <div class="offcanvas-header pb-0">
                <div class="offcanvas-title" id="mobileMenuLabel">
                    <img src="{{ asset('frontend/assets/images/logo.png') }}" alt="TGA Grading">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pt-0">
                <div class="dropdown mt-3">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.index') ? 'active' : '' }}" href="{{ route('frontend.index') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.howToOrder') ? 'active' : '' }}" href="{{ route('frontend.howToOrder') }}">How to Order</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.pricing') ? 'active' : '' }}" href="{{ route('frontend.pricing') }}">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.certification') ? 'active' : '' }}" href="{{ route('frontend.certification') }}">Certification
                                Verification</a>
                        </li>
                        <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('frontend.partners') ? 'active' : '' }}" href="{{ route('frontend.partners') }}">Partners</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.programs') ? 'active' : '' }}" href="{{ route('frontend.programs') }}">Programs</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.blogs') ? 'active' : '' }}" href="{{ route('frontend.blogs') }}">News</a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" href="{{ route('frontend.contact') }}">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</header>
<!-- ======================= header end  ============================ -->
