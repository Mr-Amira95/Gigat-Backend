  <!-- HEADER -->

  @php
      if (auth()->guard('freelancer')->check()) {
          $logoutRoute = route('freelancer.logout');
          $profileRoute = route('freelancer.profile.show');
      } elseif (auth()->guard('admin')->check()) {
          $logoutRoute = route('logout');
          //   $profileRoute = '#';
          $profileRoute = route('profile.edit');
      } else {
          $logoutRoute = '#';
      }
  @endphp


  <header class="app-header">
      <nav class="main-header !h-[3.75rem]" aria-label="Global">
          <div class="main-header-container ps-[0.725rem] pe-[1rem] ">
              <div class="header-content-left">
                  <!-- Start::header-element -->
                  <div class="header-element">
                      <div class="horizontal-logo">
                          <a href="{{ auth()->guard('freelancer')->check() ? route('freelancer.home.index') : route('home.index') }}" class="header-logo">
                              <img src="{{ asset($logo) }}" alt="logo" class="desktop-logo">
                              <img src="{{ asset($logo) }}" alt="logo" class="toggle-logo">
                              <img src="{{ asset($logo) }}" alt="logo" class="desktop-dark">
                              <img src="{{ asset($logo) }}" alt="logo" class="toggle-dark">
                              <img src="{{ asset($logo) }}" alt="logo" class="desktop-white">
                              <img src="{{ asset($logo) }}" alt="logo" class="toggle-white">
                          </a>
                      </div>
                  </div>
                  <!-- End::header-element -->
                  <!-- Start::header-element -->
                  <div class="header-element md:px-[0.325rem] !items-center">
                      <!-- Start::header-link -->
                      <a aria-label="Hide Sidebar"
                          class="sidemenu-toggle animated-arrow  hor-toggle horizontal-navtoggle inline-flex items-center"
                          href="javascript:void(0);"><span></span></a>
                      <!-- End::header-link -->
                  </div>
                  <!-- End::header-element -->
              </div>
              <div class="header-content-right">

                  <!-- start header country -->
                  <div
                      class="header-element py-[1rem] md:px-[0.65rem] px-2 header-country hs-dropdown ti-dropdown hidden sm:block [--placement:bottom-left]">
                      <button id="dropdown-flag" type="button"
                          class="hs-dropdown-toggle ti-dropdown-toggle !p-0 flex-shrink-0 !border-0 !rounded-full !shadow-none">
                          @if (app()->getLocale() == 'ar')
                              <img src="{{ asset('build/assets/images/flags/ksa_flag.svg') }}" alt="flag-img"
                                  class="h-[1.25rem] w-[1.25rem] rounded-full">
                          @else
                              <img src="{{ asset('build/assets/images/flags/us_flag.jpg') }}" alt="flag-img"
                                  class="h-[1.25rem] w-[1.25rem] rounded-full">
                          @endif
                      </button>
                      <div class="hs-dropdown-menu ti-dropdown-menu min-w-[10rem] hidden !-mt-3"
                          aria-labelledby="dropdown-flag">
                          <div class="ti-dropdown-divider divide-y divide-gray-200 dark:divide-white/10">
                              <div class="py-2 first:pt-0 last:pb-0">
                                  <a href="{{ route('locale.change', 'en') }}"
                                      class="ti-dropdown-item !p-[0.65rem] {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                      <div class="flex items-center space-x-2 rtl:space-x-reverse w-full">
                                          <div class="h-[1.375rem] flex items-center w-[1.375rem] rounded-full">
                                              <img src="{{ asset('build/assets/images/flags/us_flag.jpg') }}"
                                                  alt="flag-img" class="h-[1rem] w-[1rem] rounded-full">
                                          </div>
                                          <div>
                                              <p class="!text-[0.8125rem] font-medium">{{ __('english') }}</p>
                                          </div>
                                      </div>
                                  </a>

                                  <a href="{{ route('locale.change', 'ar') }}"
                                      class="ti-dropdown-item !p-[0.65rem] {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                      <div class="flex items-center space-x-2 rtl:space-x-reverse w-full">
                                          <div class="h-[1.375rem] w-[1.375rem] flex items-center  rounded-sm">
                                              <img src="{{ asset('build/assets/images/flags/ksa_flag.svg') }}"
                                                  alt="flag-img" class="h-[1rem] w-[1rem] rounded-full">
                                          </div>
                                          <div>
                                              <p class="!text-[0.8125rem] font-medium">{{ __('arabic') }}</p>
                                          </div>
                                      </div>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- end header country -->
                  <!-- light and dark theme -->
                  {{-- <div
                      class="header-element header-theme-mode hidden !items-center sm:block !py-[1rem] md:!px-[0.65rem] px-2">
                      <a aria-label="anchor"
                          class="hs-dark-mode-active:hidden flex hs-dark-mode group flex-shrink-0 justify-center items-center gap-2  rounded-full font-medium transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                          href="javascript:void(0);" data-hs-theme-click-value="dark">
                          <i class="bx bx-moon header-link-icon"></i>
                      </a>
                      <a aria-label="anchor"
                          class="hs-dark-mode-active:flex hidden hs-dark-mode group flex-shrink-0 justify-center items-center gap-2  rounded-full font-medium text-defaulttextcolor  transition-all text-xs dark:bg-bodybg dark:bg-bgdark dark:hover:bg-black/20 dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                          href="javascript:void(0);" data-hs-theme-click-value="light">
                          <i class="bx bx-sun header-link-icon"></i>
                      </a>
                  </div> --}}
                  <!-- End light and dark theme -->

                  @if (auth()->guard('freelancer')->check())
                      <!-- start header currency -->
                      <div
                          class="header-element py-[1rem] md:px-[0.65rem] px-2 header-currency hs-dropdown ti-dropdown hidden sm:block [--placement:bottom-left]">
                          <button id="dropdown-currency" type="button"
                              class="hs-dropdown-toggle ti-dropdown-toggle !p-0 flex-shrink-0 !border-0 !rounded-full !shadow-none">
                              <img src="{{ asset('build/assets/images/icons/cash.png') }}" alt="currency icon"
                                  class="h-[1.25rem] w-[1.25rem] rounded-full">
                          </button>

                          <div class="hs-dropdown-menu ti-dropdown-menu min-w-[8rem] hidden !-mt-3"
                              aria-labelledby="dropdown-currency">
                              <div class="ti-dropdown-divider divide-y divide-gray-200 dark:divide-white/10">
                                  <div class="py-2 first:pt-0 last:pb-0">
                                      @foreach ($allcurrencies as $currency)
                                          <a href="{{ route('currency.change', $currency['code']) }}"
                                              class="ti-dropdown-item !p-[0.65rem] {{ session('currency', 'usd') == $currency['code'] ? 'active' : '' }}">
                                              <div class="flex items-center space-x-2 rtl:space-x-reverse w-full">
                                                  <div>
                                                      <p class="!text-[0.8125rem] font-medium">
                                                          {{ $currency['symbol'] }}
                                                      </p>
                                                  </div>
                                              </div>
                                          </a>
                                      @endforeach
                                  </div>
                              </div>
                          </div>
                      </div>
                      <!-- end header currency -->



                      <!-- FAQ Icon Button -->

                      <div class="header-element header-theme-mode !items-center sm:block">
                          <!-- FAQ Button -->
                          <a href="{{ route('freelancer.faqs.index') }}"
                              class="sidemenu-toggle animated-arrow  hor-toggle horizontal-navtoggle inline-flex items-center"
                              aria-label="FAQs">
                              <img src="{{ asset('build/assets/images/icons/frequently-asked-questions.png') }}"
                                  alt="FAQ Icon" class="h-[1.25rem] w-[1.25rem]">
                          </a>
                      </div>

                      <!-- chat Icon Button -->

                      <div class="header-element header-theme-mode  !items-center sm:block">
                          <a href="{{ route('freelancer.chat.index') }}"
                              class="sidemenu-toggle animated-arrow  hor-toggle horizontal-navtoggle inline-flex items-center"
                              aria-label="Chat">
                              <img src="{{ asset('build/assets/images/icons/chat.png') }}" alt="Chat Icon"
                                  class="h-[1.25rem] w-[1.25rem]">
                          </a>
                      </div>
                      {{-- whatsapp --}}
                      <div class="header-element header-theme-mode  !items-center sm:block">
                          <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsappNumber) }}" target="_blank"
                              class="sidemenu-toggle animated-arrow hor-toggle horizontal-navtoggle inline-flex items-center"
                              aria-label="whatsapp">
                              <img src="{{ asset('build/assets/images/icons/whatsapp.png') }}" alt="whatsapp Icon"
                                  class="h-[1.25rem] w-[1.25rem]">
                          </a>
                      </div>

                      {{-- notification --}}
                      <div class="header-element header-theme-mode !items-center sm:block" style="margin-right: 10px;">
                          <a href="{{ route('freelancer.notification.index') }}"
                              class="relative inline-flex items-center" aria-label="Notification">
                              <img src="{{ asset('build/assets/images/icons/bell.png') }}" alt="Notification Icon"
                                  class="h-[1.25rem] w-[1.25rem]">

                              <span id="notification-count"
                                  style="transform: translate(30%, -50%); display: {{ ($notificationCount ?? 0) > 0 ? 'inline-flex' : 'none' }}"
                                  class="absolute top-0 left-0 inline-flex items-center justify-center px-1 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                  {{ $notificationCount ?? 0 }}
                              </span>
                          </a>
                      </div>
                      {{-- <div class="header-element header-theme-mode !items-center sm:block" style="margin-right: 10px;">
                          <a href="{{ route('freelancer.notification.index') }}"
                              class="relative inline-flex items-center" aria-label="Notification">
                              <img src="{{ asset('build/assets/images/icons/bell.png') }}" alt="Notification Icon"
                                  class="h-[1.25rem] w-[1.25rem]">

                              <span id="notification-count"
                                  style="transform: translate(30%, -50%); display: {{ !empty($notificationCount) && $notificationCount > 0 ? 'inline-flex' : 'none' }}"
                                  class="absolute top-0 left-0 inline-flex items-center justify-center
            px-1 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                  {{ $notificationCount ?? 0 }}
                              </span>
                          </a>
                      </div> --}}





                  @endif

                  <!-- Header Profile -->
                  <div
                      class="header-element md:!px-[0.65rem] px-2 hs-dropdown !items-center ti-dropdown [--placement:bottom-left]">
                      <button id="dropdown-profile" type="button"
                          class="hs-dropdown-toggle ti-dropdown-toggle !gap-2 !p-0 flex-shrink-0 sm:me-2 me-0 !rounded-full !shadow-none text-xs align-middle !border-0 !shadow-transparent ">
                          <img class="inline-block rounded-full" src="{{ asset('build/assets/images/faces/9.jpg') }}"
                              width="32" height="32" alt="Image Description">
                      </button>
                      <div class="md:block hidden dropdown-profile">
                          <p class="font-semibold mb-0 leading-none text-[#536485] text-[0.813rem] ">
                              @if (auth()->guard('freelancer')->check())
                                  {{ auth()->guard('freelancer')->user()->username }}
                              @elseif(auth()->guard('admin')->check())
                                  {{ auth()->guard('admin')->user()->username }}
                              @else
                                  Guest
                              @endif
                          </p>
                      </div>

                      <div class="hs-dropdown-menu ti-dropdown-menu !-mt-3 border-0 w-[11rem] !p-0 border-defaultborder hidden main-header-dropdown pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                          aria-labelledby="dropdown-profile">
                          <ul class="text-defaulttextcolor font-medium dark:text-[#8c9097] dark:text-white/50">
                              <li>
                                  <a class="w-full ti-dropdown-item !text-[0.8125rem] !gap-x-0  !p-[0.65rem] !inline-flex"
                                      href="{{ $profileRoute }}">
                                      <i
                                          class="ti ti-user-circle text-[1.125rem] me-2 opacity-[0.7]"></i>{{ __('profile') }}
                                  </a>
                              </li>
                              <li>
                                  <form id="logout-form" action="{{ $logoutRoute }}" method="POST"
                                      class="w-full">
                                      @csrf
                                      <input type="hidden" name="player_id" id="player_id">
                                      <input type="hidden" id="platform" name="platform" value="web">

                                      <button type="submit"
                                          class="w-full ti-dropdown-item !text-[0.8125rem] !p-[0.65rem] !gap-x-0 !inline-flex logout-btn">
                                          <i class="ti ti-logout text-[1.125rem] me-2 opacity-[0.7]"></i>
                                          {{ __('log_out') }}
                                      </button>
                                  </form>
                              </li>
                          </ul>
                      </div>
                  </div>

                  <!-- End Header Profile -->
                  <!-- Switcher Icon -->
                  {{-- <div class="header-element md:px-[0.48rem]">
                      <button aria-label="button" type="button"
                          class="hs-dropdown-toggle switcher-icon inline-flex flex-shrink-0 justify-center items-center gap-2  rounded-full font-medium  align-middle transition-all text-xs dark:text-[#8c9097] dark:text-white/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                          data-hs-overlay="#hs-overlay-switcher">
                          <i class="bx bx-cog header-link-icon animate-spin-slow"></i>
                      </button>
                  </div> --}}
                  <!-- Switcher Icon -->
                  <!-- End::header-element -->
              </div>
          </div>
      </nav>
  </header>
  <!-- END HEADER -->


  {{-- AI Button (freelancer only) --}}
  @if (auth()->guard('freelancer')->check())
  <a href="{{ route('freelancer.ai.index') }}"
      style="
     position: fixed;
     bottom: 1rem;
     right: 1rem;
     background-color: rgb(59, 59, 113);
     color: white;
     width: 60px;
     height: 60px;
     border-radius: 50%;
     display: flex;
     align-items: center;
     justify-content: center;
     z-index: 50;
     cursor: pointer;
     box-shadow: 0 2px 6px rgba(0,0,0,0.3);
     transition: background-color 0.3s ease;
   "
      onmouseover="this.style.backgroundColor='rgb(45, 45, 85)'"
      onmouseout="this.style.backgroundColor='rgb(59, 59, 113)'">
      <img src="{{ asset('build/assets/images/icons/ai.png') }}" alt="AI icon"
          style="width: 40px; height: 40px; object-fit: contain;">
  </a>
  @endif



  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>

  <script>
      //   Pusher.logToConsole = true;

      window.Echo = new Echo({
          broadcaster: 'pusher',
          key: '{{ config('broadcasting.connections.pusher.key') }}',
          cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
          forceTLS: true,
          encrypted: true,
      });
  </script>


  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const currentUserId = {{ auth()->id() }};
          console.log("Authenticated User ID:", currentUserId);
          //   window.Echo.private('notifications.' + currentUserId)
          window.Echo.channel('notifications.' + currentUserId)
              .listen('.new.notification', (e) => {
                  //   console.log('Notification received:', e);

                  const badge = document.getElementById('notification-count');
                  if (badge) {
                      badge.textContent = e.unreadCount;
                      badge.style.display = e.unreadCount > 0 ? 'inline-block' : 'none';
                  }
              });
      });
  </script>
  {{-- <script>
      document.addEventListener('DOMContentLoaded', function() {
          const currentUserId = {{ auth()->id() }};
          console.log("Authenticated User ID:", currentUserId);

          window.Echo.channel('notifications.' + currentUserId)
              .listen('.new.notification', (e) => {
                  const badge = document.getElementById('notification-count');

                  if (badge) {
                      if (e.unreadCount > 0) {
                          badge.textContent = e.unreadCount;
                          badge.style.display = 'inline-flex'; // show badge
                      } else {
                          badge.style.display = 'none'; // hide badge
                      }
                  }
              });
      });
  </script> --}}
