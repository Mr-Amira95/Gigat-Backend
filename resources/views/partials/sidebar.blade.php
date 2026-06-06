<aside class="app-sidebar" id="sidebar">
    {{-- <div class="main-sidebar-header">
        <a href="{{ route('home.index') }}" class="header-logo">
            <img src="{{ asset('build/assets/images/media/temp-logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('build/assets/images/media/temp-logo.png') }}" alt="logo" class="toggle-logo">
            <img src="{{ asset('build/assets/images/media/temp-logo.png') }}" alt="logo" class="desktop-dark">
            <img src="{{ asset('build/assets/images/media/temp-logo.png') }}" alt="logo" class="toggle-dark">
            <img src="{{ asset('build/assets/images/media/temp-logo.png') }}" alt="logo" class="desktop-white">
            <img src="{{ asset('build/assets/images/media/temp-logo.png') }}" alt="logo" class="toggle-white">
        </a>
    </div> --}}
    <div style="height:90px" class="main-sidebar-header">
        <div class="flex justify-center my-4">
            @if (auth()->guard('freelancer')->check())
                <a href="{{ route('freelancer.home.index') }}" class="logo">
                    <img src="{{ asset($logo) }}" alt="logo"
                        class="desktop-logo w-[150px] sm:w-[150px] md:w-[300px] lg:w-[350px] h-auto ">
                </a>
            @elseif (auth()->guard('admin')->check())
                <a href="{{ route('home.index') }}" class="logo">
                    <img src="{{ asset($logo) }}" alt="logo"
                        class="desktop-logo w-[150px] sm:w-[150px] md:w-[300px] lg:w-[350px] h-auto ">
                </a>
            @endif
        </div>
    </div>

    <div style="margin-top: 50px" class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column">

            @if (auth()->guard('freelancer')->check())
                <ul class="main-menu">
                    <li class="slide__category"><span class="category-name">{{ __('main') }}</span></li>
                    <li class="slide">
                        <a href="{{ route('freelancer.home.index') }}" class="side-menu__item">
                            <i class="bx bx-home side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('dashboard') }}</span>
                        </a>
                    </li>

                    <li class="slide__category"><span class="category-name">{{ __('pages') }}</span></li>


                    <li class="slide">
                        <a href="{{ route('freelancer.services.index') }}" class="side-menu__item">
                            <i class="bx bx-cube side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('services') }}</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('freelancer.portfolios.index') }}" class="side-menu__item">
                            <i class="bx bx-image side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('portfolios') }}</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('freelancer.requests.index') }}" class="side-menu__item">
                            <i class="bx bx-clipboard side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('requests') }}</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('freelancer.quotations.index') }}" class="side-menu__item">
                            <i class="bx bx-file side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('quotations') }}</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('freelancer.finances.index') }}" class="side-menu__item">
                            <i class="bx bx-coin side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('finances') }}</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('freelancer.reviews.index') }}" class="side-menu__item">
                            <i class="bx bx-star side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('reviews') }}</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('freelancer.tickets.index') }}" class="side-menu__item">
                            <i class="bx bx-message side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('support_tickets') }} </span>
                        </a>
                    </li>


                </ul>
            @elseif(auth()->guard('admin')->check())
                <ul class="main-menu">
                    <li class="slide__category"><span class="category-name">{{ __('main') }}</span></li>
                    <li class="slide">
                        <a href="{{ route('home.index') }}" class="side-menu__item">
                            <i class="bx bx-home side-menu__icon"></i>
                            <span class="side-menu__label">{{ __('dashboard') }}</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('analytics.index') }}" class="side-menu__item">
                            <i class="bx bx-bar-chart-alt-2 side-menu__icon"></i>
                            <span class="side-menu__label">Analytics</span>
                        </a>
                    </li>

                    <li class="slide__category"><span class="category-name">{{ __('pages') }}</span></li>
                    @canany(['view_admins', 'view_clients', 'view_freelancers', 'view_roles', 'view_companies'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="bx bx-user side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('users') }}</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu">
                                @can('view_admins')
                                    <li class="slide">
                                        <a href="{{ route('admins.index') }}" class="side-menu__item">
                                            <i class="bx bx-shield-quarter side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('administrators') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_clients')
                                    <li class="slide">
                                        <a href="{{ route('clients.index') }}" class="side-menu__item">
                                            <i class="bx bx-user side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('clients') }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('view_freelancers')
                                    <li class="slide">
                                        <a href="{{ route('freelancers.index') }}" class="side-menu__item">
                                            <i class="bx bx-briefcase side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('freelancers') }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('view_companies')
                                    <li class="slide">
                                        <a href="{{ route('companies.index') }}" class="side-menu__item">
                                            <i class="bx bx-buildings side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('companies') }}</span>
                                        </a>
                                    </li>
                                @endcan


                                @can('view_roles')
                                    <li class="slide">
                                        <a href="{{ route('roles.index') }}" class="side-menu__item">
                                            <i class="bx bx-key side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('roles') }}</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </li>
                    @endcanany



                    @canany(['view_categories', 'view_sub_categories', 'view_tags', 'view_plans'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="bx bx-category-alt side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('categories_management') }}</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu">
                                @can('view_categories')
                                    <li class="slide">
                                        <a href="{{ route('categories.index') }}" class="side-menu__item">
                                            <i class="bx bx-category side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('categories') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_sub_categories')
                                    <li class="slide">
                                        <a href="{{ route('subCategories.index') }}" class="side-menu__item">
                                            <i class="bx bx-layer side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('sub_categories') }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('view_tags')
                                    <li class="slide">
                                        <a href="{{ route('tags.index') }}" class="side-menu__item">
                                            <i class="bx bx-tag side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('tags') }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('view_plans')
                                    <li class="slide">
                                        <a href="{{ route('plans.index') }}" class="side-menu__item">
                                            <i class="bx bx-file side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('plans') }}</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany


                    @can('view_services')
                        <li class="slide">
                            <a href="{{ route('services.index') }}" class="side-menu__item">
                                <i class="bx bx-cube side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('services') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_portfolio')
                        <li class="slide">
                            <a href="{{ route('portfolios.index') }}" class="side-menu__item">
                                <i class="bx bx-image side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('portfolios') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_requests')
                        <li class="slide">
                            <a href="{{ route('requests.index') }}" class="side-menu__item">
                                <i class="bx bx-clipboard side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('requests') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_quotations')
                        <li class="slide">
                            <a href="{{ route('quotations.index') }}" class="side-menu__item">
                                <i class="bx bx-file side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('quotations') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_finances')
                        <li class="slide">
                            <a href="{{ route('finances.index') }}" class="side-menu__item">
                                <i class="bx bx-coin side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('finances') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_chats')
                        <li class="slide">
                            <a href="{{ route('chats.index') }}" class="side-menu__item">
                                <i class="bx bx-chat side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('chats') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_reviews')
                        <li class="slide">
                            <a href="{{ route('reviews.index') }}" class="side-menu__item">
                                <i class="bx bx-star side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('reviews') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_notifications')
                        <li class="slide">
                            <a href="{{ route('notifications.index') }}" class="side-menu__item">
                                <i class="bx bx-bell side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('notifications') }}</span>
                            </a>
                        </li>
                    @endcan
                    {{-- <li class="slide">
                    <a href="{{ route('filters.index') }}" class="side-menu__item">
                        <i class="bx bx-filter side-menu__icon"></i>
                        <span class="side-menu__label">{{ __('filters') }}</span>
                    </a>
                </li> --}}

                    {{-- <li class="slide">
                    <a href="{{ route('features.index') }}" class="side-menu__item">
                        <i class="bx bx-check-square side-menu__icon"></i>
                        <span class="side-menu__label">{{ __('fixed_features') }}</span>
                    </a>
                </li> --}}
                    @can('view_reports')
                        <li class="slide">
                            <a href="{{ route('reported-issues.index') }}" class="side-menu__item">
                                <i class="bx bx-flag side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('reported_issues') }} </span>
                            </a>
                        </li>
                    @endcan
                    @can('view_tickets')
                        <li class="slide">
                            <a href="{{ route('tickets.index') }}" class="side-menu__item">
                                <i class="bx bx-message side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('support_tickets') }} </span>
                            </a>
                        </li>
                    @endcan

                    @canany(['view_sliders', 'view_professions', 'view_info', 'view_privacy', 'view_terms',
                        'view_currencies', 'view_faqs', 'view_pending_questions', 'view_releases'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="bx bx-cog side-menu__icon"></i>
                                <span class="side-menu__label">{{ __('general_info') }}</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu">
                                @can('view_sliders')
                                    <li class="slide has-sub">
                                        <a href="javascript:void(0);" class="side-menu__item">
                                            <i class="bx bx-image side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('sliders') }}</span>
                                            <i class="fe fe-chevron-right side-menu__angle"></i>
                                        </a>
                                        <ul class="slide-menu">
                                            <li class="slide">
                                                <a href="{{ route('sliders.mobileSliders') }}" class="side-menu__item">
                                                    <i class="bx bx-mobile side-menu__icon"></i>
                                                    <span class="side-menu__label">{{ __('mobile_intro_screens') }}</span>
                                                </a>
                                            </li>
                                            <li class="slide">
                                                <a href="{{ route('sliders.webSliders') }}" class="side-menu__item">
                                                    <i class="bx bx-desktop side-menu__icon"></i>
                                                    <span class="side-menu__label">{{ __('web_sliders') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan
                                @can('view_professions')
                                    <li class="slide">
                                        <a href="{{ route('professions.index') }}" class="side-menu__item">
                                            <i class="bx bx-hard-hat side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('professions') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_info')
                                    <li class="slide">
                                        <a href="{{ route('general.generalInfo') }}" class="side-menu__item">
                                            <i class="bx bx-cog side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('general') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_privacy')
                                    <li class="slide">
                                        <a href="{{ route('general.privacyPolicy') }}" class="side-menu__item">
                                            <i class="bx bx-shield-quarter side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('privacy_policy') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_terms')
                                    <li class="slide">
                                        <a href="{{ route('general.terms') }}" class="side-menu__item">
                                            <i class="bx bx-lock side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('terms') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_currencies')
                                    <li class="slide">
                                        <a href="{{ route('currencies.index') }}" class="side-menu__item">
                                            <i class="bx bx-dollar-circle side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('currencies') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_faqs')
                                    <li class="slide">
                                        <a href="{{ route('faqs.index') }}" class="side-menu__item">
                                            <i class="bx bx-help-circle side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('faqs') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_pending_questions')
                                    <li class="slide">
                                        <a href="{{ route('pending-questions.index') }}" class="side-menu__item">
                                            <i class="bx bx-question-mark side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('pending_questions') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_releases')
                                    <li class="slide">
                                        <a href="{{ route('admin.releases.index') }}" class="side-menu__item">
                                            <i class="bx bx-git-branch side-menu__icon"></i>
                                            <span class="side-menu__label">{{ __('releases') }}</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany


                    @canany(['view_document_categories', 'view_document_contents'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="bx bx-book-open side-menu__icon"></i> {{-- Main icon --}}
                                <span class="side-menu__label">{{ __('system_documents') }}</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>

                            <ul class="slide-menu">

                                @can('view_document_categories')
                                    <li class="slide">
                                        <a href="{{ route('document-categories.index') }}" class="side-menu__item">
                                            <i class="bx bx-folder side-menu__icon"></i> {{-- Categories --}}
                                            <span class="side-menu__label">{{ __('categories') }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('view_document_contents')
                                    <li class="slide">
                                        <a href="{{ route('document-contents.index') }}" class="side-menu__item">
                                            <i class="bx bx-file-blank side-menu__icon"></i> {{-- Documents --}}
                                            <span class="side-menu__label">{{ __('documents') }}</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </li>
                    @endcanany



                </ul>
            @endif


        </nav>
    </div>
</aside>
