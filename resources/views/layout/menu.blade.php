<div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <style>
        .sidenav, #sidenav-collapse-main {
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
            height: calc(100vh - 100px) !important;
            padding-bottom: 2rem !important;
        }
        .sidenav::-webkit-scrollbar,
        .navbar-collapse::-webkit-scrollbar,
        #sidenav-collapse-main::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            background: transparent !important;
        }

        /* Jaga jarak vertikal biar padat dan muat di layar */
        #sidenav-collapse-main .nav-link {
            padding-top: 0.45rem !important;
            padding-bottom: 0.45rem !important;
            margin-top: 1px !important;
            margin-bottom: 1px !important;
        }

        /* GEDEIN KOTAK ICON (Dari 30px ke 36px) */
        #sidenav-collapse-main .icon.icon-sm {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            min-height: 36px !important;
        }

        /* GEDEIN GAMBAR SVG DI DALAM ICON */
        #sidenav-collapse-main .icon.icon-sm svg {
            width: 20px !important;
            height: 20px !important;
        }

        /* Responsif untuk layar HP/Tablet */
        @media (max-width: 991.98px) {
            #sidenav-collapse-main .nav-link {
                padding-top: 0.6rem !important;
                padding-bottom: 0.6rem !important;
            }
            #sidenav-collapse-main .icon.icon-sm {
                width: 38px !important;
                height: 38px !important;
                min-width: 38px !important;
                min-height: 38px !important;
            }
            #sidenav-collapse-main .icon.icon-sm svg {
                width: 22px !important;
                height: 22px !important;
            }
        }
    </style>
    <ul class="navbar-nav">

        {{-- ========================================================= --}}
        {{-- AREA UMUM (ADMIN & OWNER) --}}
        {{-- ========================================================= --}}

        {{-- DASHBOARD --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('dashboard*') || Request::is('/') ? 'active' : '' }}"
                href="{{ route('dashboard.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('dashboard*') || Request::is('/') ? 'white' : '#67748e' }}"
                        class="bi bi-menu-app-fill" viewBox="0 0 16 16">
                        <path
                            d="M0 1.5A1.5 1.5 0 0 1 1.5 0h2A1.5 1.5 0 0 1 5 1.5v2A1.5 1.5 0 0 1 3.5 5h-2A1.5 1.5 0 0 1 0 3.5zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5" />
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Dashboard</span>
            </a>
        </li>

        {{-- PRODUCTS --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('products*') ? 'white' : '#67748e' }}" viewBox="0 0 18 18">
                        <path
                            d="M11.25,5.5c-.414,0-.75-.336-.75-.75v-1.75c0-.827-.673-1.5-1.5-1.5s-1.5,.673-1.5,1.5v1.75c0,.414-.336,.75-.75,.75s-.75-.336-.75-.75v-1.75c0-1.654,1.346-3,3-3s3,1.346,3,3v1.75c0,.414-.336,.75-.75,.75Z">
                        </path>
                        <path
                            d="M15.406,6.512c-.125-1.432-1.302-2.512-2.739-2.512H5.333c-1.437,0-2.615,1.08-2.739,2.512l-.652,7.5c-.067,.766,.193,1.53,.712,2.097s1.258,.892,2.027,.892H13.318c.769,0,1.508-.325,2.027-.892s.779-1.331,.712-2.097l-.652-7.5Z">
                        </path>
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Products</span>
            </a>
        </li>

        {{-- CLIENTS --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('clients*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('clients*') ? 'white' : '#67748e' }}" class="bi bi-people-fill"
                        viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Clients</span>
            </a>
        </li>

        {{-- PURCHASE --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('purchase*') ? 'active' : '' }}" href="{{ route('purchase.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)"
                                fill="{{ Request::is('purchase*') ? 'white' : '#67748e' }}" fill-rule="nonzero">
                                <g transform="translate(1716.000000, 291.000000)">
                                    <g transform="translate(453.000000, 454.000000)">
                                        <path class="color-background opacity-6"
                                            d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z">
                                        </path>
                                        <path class="color-background"
                                            d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z">
                                        </path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Purchase</span>
            </a>
        </li>

        {{-- ORDER --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('orders*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('orders*') ? 'white' : '#67748e' }}" class="bi bi-basket2-fill"
                        viewBox="0 0 16 16">
                        <path
                            d="M5.929 1.757a.5.5 0 1 0-.858-.514L2.217 6H.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h.623l1.844 6.456A.75.75 0 0 0 3.69 15h8.622a.75.75 0 0 0 .722-.544L14.877 8h.623a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1.717L10.93 1.243a.5.5 0 1 0-.858.514L12.617 6H3.383zM4 10a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0zm4-1a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1" />
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Order</span>
            </a>
        </li>

        {{-- SALE --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('sales*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('sales*') ? 'white' : '#67748e' }}" class="bi bi-cash-stack"
                        viewBox="0 0 16 16">
                        <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                        <path
                            d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2z" />
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Sale</span>
            </a>
        </li>

        {{-- DISTRIBUTOR --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('distributors*') ? 'active' : '' }}"
                href="{{ route('distributors.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('distributors*') ? 'white' : '#67748e' }}" class="bi bi-truck"
                        viewBox="0 0 16 16">
                        <path
                            d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Distributor</span>
            </a>
        </li>

        {{-- COURIER MANAGEMENT --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('courier-management*') || Request::is('expeditions*') ? 'active' : '' }}" href="{{ route('courier-management.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="{{ Request::is('courier-management*') || Request::is('expeditions*') ? 'white' : '#67748e' }}" class="bi bi-bicycle"
                        viewBox="0 0 16 16">
                        <path
                            d="M4 4.5a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1v.5h4.14l.386-1.158A.5.5 0 0 1 11 4h1a.5.5 0 0 1 0 1h-.64l-.311.935.807 1.29a3 3 0 1 1-.848.53l-.508-.812-2.076 3.322A.5.5 0 0 1 8 10.5H5.959a3 3 0 1 1-1.815-3.274L5 5.856V5h-.5a.5.5 0 0 1-.5-.5m1.5 2.443-.508.814c.5.444.85 1.054.967 1.743h1.139zM8 9.057 9.598 6.5H6.402zM4.937 9.5a2 2 0 0 0-.487-.877l-.548.877zM3.603 8.092A2 2 0 1 0 4.937 10.5H3a.5.5 0 0 1-.424-.765zm7.947.53a2 2 0 1 0 .848-.53l1.026 1.643a.5.5 0 1 1-.848.53z" />
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Courier</span>
            </a>
        </li>


        {{-- ========================================================= --}}
        {{-- AREA PRIVASI OWNER (HANYA OWNER YANG BISA LIHAT) --}}
        {{-- ========================================================= --}}

        @if (auth()->user()?->role == 'owner')
            {{-- USERS --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="{{ Request::is('users*') ? 'white' : '#67748e' }}" class="bi bi-person-fill"
                            viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>
                    </div>
                    <span class="nav-link-text ms-1">Users</span>
                </a>
            </li>
        @endif



    </ul>
</div>
