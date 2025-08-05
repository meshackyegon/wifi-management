<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <a class="menu-link" href="{{ route('dashboard') }}" wire:navigate>
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div class="text-truncate">{{ __('Dashboard') }}</div>
      </a>
    </li>

    @auth
      @if(auth()->user()->isCustomer())
        <!-- Customer Menu -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Customer</span>
        </li>
        
        <li class="menu-item {{ request()->is('buy-voucher*') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('voucher.buy') }}">
            <i class="menu-icon tf-icons bx bx-cart-add"></i>
            <div class="text-truncate">{{ __('Buy Voucher') }}</div>
          </a>
        </li>
        
        <li class="menu-item {{ request()->is('mobile-money/history') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('mobile-money.history') }}">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div class="text-truncate">{{ __('Purchase History') }}</div>
          </a>
        </li>

      @elseif(auth()->user()->isAgent())
        <!-- Agent Menu -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Agent Panel</span>
        </li>
        
        <li class="menu-item {{ request()->is('vouchers*') && !request()->is('voucher-plans*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-receipt"></i>
            <div class="text-truncate">{{ __('Vouchers') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('vouchers.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('vouchers.index') }}" wire:navigate>{{ __('All Vouchers') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('vouchers.create') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('vouchers.create') }}" wire:navigate>{{ __('Generate Vouchers') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('mobile-money/history') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('mobile-money.history') }}">
            <i class="menu-icon tf-icons bx bx-credit-card"></i>
            <div class="text-truncate">{{ __('Payments') }}</div>
          </a>
        </li>
        
        <li class="menu-item {{ request()->is('reports*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div class="text-truncate">{{ __('Reports') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('reports.commissions') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('reports.commissions') }}" wire:navigate>{{ __('Commissions') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('reports.vouchers') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('reports.vouchers') }}" wire:navigate>{{ __('Voucher Sales') }}</a>
            </li>
          </ul>
        </li>

      @elseif(auth()->user()->isAdmin())
        <!-- Admin Menu -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Administration</span>
        </li>
        
        <li class="menu-item {{ request()->is('analytics') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('analytics') }}">
            <i class="menu-icon tf-icons bx bx-line-chart"></i>
            <div class="text-truncate">{{ __('Analytics') }}</div>
          </a>
        </li>
        
        <li class="menu-item {{ request()->is('vouchers*') && !request()->is('voucher-plans*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-receipt"></i>
            <div class="text-truncate">{{ __('Vouchers') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('vouchers.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('vouchers.index') }}" wire:navigate>{{ __('All Vouchers') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('vouchers.create') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('vouchers.create') }}" wire:navigate>{{ __('Generate Vouchers') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('voucher-plans*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-package"></i>
            <div class="text-truncate">{{ __('Voucher Plans') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('voucher-plans.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('voucher-plans.index') }}" wire:navigate>{{ __('All Plans') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('voucher-plans.create') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('voucher-plans.create') }}" wire:navigate>{{ __('Create Plan') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('mobile-money*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-credit-card-alt"></i>
            <div class="text-truncate">{{ __('Mobile Money') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('mobile-money.history') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('mobile-money.history') }}" wire:navigate>{{ __('All Payments') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('mobile-money.cash-payments') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('mobile-money.cash-payments') }}" wire:navigate>{{ __('Cash Payments') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('mobile-money.stats') ? 'active' : '' }}">
              <a class="menu-link" href="#" onclick="loadPaymentStats()">{{ __('Statistics') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('routers*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-router"></i>
            <div class="text-truncate">{{ __('Routers') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('routers.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('routers.index') }}" wire:navigate>{{ __('All Routers') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('routers.create') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('routers.create') }}" wire:navigate>{{ __('Add Router') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('users*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div class="text-truncate">{{ __('Users') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('users.index') }}" wire:navigate>{{ __('All Users') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('users.create') }}" wire:navigate>{{ __('Create User') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('sms*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-message-dots"></i>
            <div class="text-truncate">{{ __('SMS') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('sms.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('sms.index') }}" wire:navigate>{{ __('Send SMS') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('sms.logs') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('sms.logs') }}" wire:navigate>{{ __('SMS Logs') }}</a>
            </li>
          </ul>
        </li>
        
        <li class="menu-item {{ request()->is('reports*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div class="text-truncate">{{ __('Reports') }}</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('reports.index') }}" wire:navigate>{{ __('Overview') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('reports.revenue') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('reports.revenue') }}" wire:navigate>{{ __('Revenue') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('reports.vouchers') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('reports.vouchers') }}" wire:navigate>{{ __('Vouchers') }}</a>
            </li>
            <li class="menu-item {{ request()->routeIs('reports.commissions') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('reports.commissions') }}" wire:navigate>{{ __('Commissions') }}</a>
            </li>
          </ul>
        </li>
      @endif
      
      <!-- Common Menu Items for All Authenticated Users -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Account</span>
      </li>
    @endauth

    <!-- Settings (Available for all authenticated users) -->
    @auth
    <li class="menu-item {{ request()->is('settings/*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div class="text-truncate">{{ __('Settings') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('settings.profile') }}" wire:navigate>{{ __('Profile') }}</a>
        </li>
        <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('settings.password') }}" wire:navigate>{{ __('Password') }}</a>
        </li>
        @if(auth()->user()->isAdmin())
          <li class="menu-item {{ request()->routeIs('settings.system') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.system') }}" wire:navigate>{{ __('System') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('settings.mobile-money') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.mobile-money') }}" wire:navigate>{{ __('Mobile Money') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('settings.sms') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.sms') }}" wire:navigate>{{ __('SMS Settings') }}</a>
          </li>
        @endif
      </ul>
    </li>
    @endauth

    <!-- Guest Menu Items -->
    @guest
    <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
      <a class="menu-link" href="{{ route('home') }}">
        <i class="menu-icon tf-icons bx bx-home"></i>
        <div class="text-truncate">{{ __('Home') }}</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->is('buy-voucher*') ? 'active' : '' }}">
      <a class="menu-link" href="{{ route('voucher.buy') }}">
        <i class="menu-icon tf-icons bx bx-cart-add"></i>
        <div class="text-truncate">{{ __('Buy Voucher') }}</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->is('login') ? 'active' : '' }}">
      <a class="menu-link" href="{{ route('login') }}">
        <i class="menu-icon tf-icons bx bx-log-in"></i>
        <div class="text-truncate">{{ __('Login') }}</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->is('register') ? 'active' : '' }}">
      <a class="menu-link" href="{{ route('register') }}">
        <i class="menu-icon tf-icons bx bx-user-plus"></i>
        <div class="text-truncate">{{ __('Register') }}</div>
      </a>
    </li>
    @endguest
  </ul>
</aside>
<!-- / Menu -->

<script>
  // Toggle the 'open' class when the menu-toggle is clicked
  document.querySelectorAll('.menu-toggle').forEach(function(menuToggle) {
    menuToggle.addEventListener('click', function() {
      const menuItem = menuToggle.closest('.menu-item');
      // Toggle the 'open' class on the clicked menu-item
      menuItem.classList.toggle('open');
    });
  });

  // Load payment statistics modal/page
  function loadPaymentStats() {
    // This will be implemented when we create the stats component
    window.location.href = '{{ route("mobile-money.history") }}?view=stats';
  }
</script>
