<div class="nk-sidebar is-light nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="/author/dashboard" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="/images/logo.png" srcset="/images/logo2x.png 2x" alt="logo">
                <img class="logo-dark logo-img" src="/images/logo-dark.png" srcset="/images/logo-dark2x.png 2x" alt="logo-dark">
                <img class="logo-small logo-img logo-img-small" src="/images/logo-small.png" srcset="/images/logo-small2x.png 2x" alt="logo-small">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
        </div>
    </div>
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Dashboards</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a href="/dashboard" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-presentation"></em></span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="/author/books" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-folder-list"></em></span>
                            <span class="nk-menu-text">Books</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Earnings</h6>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('author.wallet.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-wallet"></em></span>
                            <span class="nk-menu-text">Wallet</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('author.payouts.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-wallet-out"></em></span>
                            <span class="nk-menu-text">Payouts</span>
                        </a>
                    </li>
                    <!-- Add more menu items as needed -->
                </ul>
            </div>
        </div>
    </div>
</div>