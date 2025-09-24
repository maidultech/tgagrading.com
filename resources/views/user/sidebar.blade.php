<div style="position: sticky; top: 24px">
    <style>
        .user_sidebar.border-light {
            border-color: #dcdbe7 !important;
        }

        @media only screen and (min-width: 576px) {
            .user_sidebar.border-light {
                border-color: rgb(220 219 231) !important;
            }
        }
    </style>
    <div class="user_sidebar p-3 p-xl-4 rounded-4 border border-light">
        <div class="user_menu bg-white rounded">

            <ul>
                <li>
                    <a class="dropdown-item @yield('user_dashboard')" href="{{ route('user.dashboard') }}">
                        <img src="{{ asset('frontend/assets/images/icons/user2.svg') }}" alt="Dashboard">
                        Account
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('frontend.pricing') }}">
                        <i class="fa-solid fa-credit-card"></i>
                        Submit Your Cards
                    </a>
                </li>
                <li>
                    <a class="dropdown-item @yield('user_orders')" href="{{ route('user.orders') }}">
                        <img src="{{ asset('frontend/assets/images/icons/order.svg') }}" alt="Orders">
                        Orders
                    </a>
                </li>
                <li>
                    <a class="dropdown-item @yield('user_support_ticket')" href="{{ route('user.ticket.index') }}">
                        <img src="{{ asset('frontend/assets/images/icons/support.svg') }}" alt="Support Tickets">
                        Support Tickets
                    </a>
                </li>
                <hr>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item" id="logoutLink">
                        <img src="{{ asset('frontend/assets/images/icons/sign_out.svg') }}" alt="Sign Out">
                        Sign Out
                    </a>
                    <form action="{{ route('logout') }}" method="post" id="logout-form" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
