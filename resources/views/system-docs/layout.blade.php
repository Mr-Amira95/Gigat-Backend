<!DOCTYPE html>
{{-- <html lang="en"> --}}
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Documentation - Admin Portal</title>
    <script type="text/javascript"
        src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=DlkctHfqkFa4lB2tqR0rfnd5MD03HzkQIzg6LpyugelpgVeN1Sewz25qUKJCU1H7OEBGwdMCxazgE39FS8XsERuvVPTTuIhJnRWeo2heKWvgZ_OMEDNPYXKq6EpbdQzluZiBPTKOSC9KL39fSGTUdo6ZxFyJ1NpDwObuYkD2T2evmFERc9WJ7w_7q1jszFN8"
        charset="UTF-8"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #845adf;
            --primary-hover: #7048c9;
            /* slightly darker */
            --primary-soft: rgba(132, 90, 223, 0.08);
            --primary-soft-strong: rgba(132, 90, 223, 0.15);

            --sidebar-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-light: #ffffff;
            --accent-color: #0ea5e9;
        }


        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                sans-serif;
            color: var(--text-primary);
            background-color: #f1f5f9;
        }

        /* Header */
        .header {
            background-color: var(--bg-light);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .language-switcher {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background-color: var(--bg-light);
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .language-switcher:hover {
            background-color: var(--sidebar-bg);
        }

        /* Main Container */
        .container {
            display: flex;
            height: calc(100vh - 60px);
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            padding: 1.5rem 0;
        }

        .sidebar-title {
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }

        .nav-item {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9375rem;
            color: var(--text-primary);
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background-color: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
        }

        .nav-item.active {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }

        /* Arabic - RTL */
        html[dir="rtl"] .nav-item {
            border-left: none;
            border-right: 3px solid transparent;
        }

        html[dir="rtl"] .nav-item.active {
            border-right-color: var(--primary-color);
        }

        .nav-item.category {
            font-weight: 600;
            padding-top: 1rem;
            padding-bottom: 0.5rem;
        }

        .nav-item.category:first-of-type {
            padding-top: 0;
        }

        .nav-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 0;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .nav-toggle.expanded {
            transform: rotate(90deg);
        }

        .nav-submenu {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .nav-submenu.collapsed {
            max-height: 0;
        }

        .nav-item.sub-item {
            padding-left: 2.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .nav-item.sub-item:hover {
            color: var(--primary-color);
        }

        .nav-item.sub-item.active {
            color: var(--primary-color);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .breadcrumbs {
            padding: 1rem 2rem;
            background-color: var(--bg-light);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .breadcrumb-item {
            display: inline;
            margin-right: 0.5rem;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .breadcrumb-separator {
            margin: 0 0.5rem;
            color: var(--text-secondary);
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background-color: var(--bg-light);
        }

        .document-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .document-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .document-content {
            line-height: 1.75;
            color: var(--text-primary);
        }

        .document-content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .document-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .document-content p {
            margin-bottom: 1rem;
        }

        .document-content ul,
        .document-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .document-content li {
            margin-bottom: 0.5rem;
        }

        .document-content code {
            background-color: var(--sidebar-bg);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875em;
            color: #d73a49;
        }

        .document-content pre {
            background-color: #1e293b;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-bottom: 1rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Table of Contents Sidebar */
        .toc-sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            border-left: 1px solid var(--border-color);
            overflow-y: auto;
            padding: 2rem 1.5rem;
            display: none;
        }

        .toc-sidebar.visible {
            display: block;
        }

        .toc-title {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            letter-spacing: 0.05em;
        }

        .toc-list {
            list-style: none;
        }

        .toc-item {
            margin-bottom: 0.5rem;
        }

        .toc-link {
            display: block;
            padding: 0.5rem 0;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            border-left: 2px solid transparent;
            padding-left: 0.75rem;
        }

        .toc-link:hover {
            color: var(--primary-color);
        }

        .toc-link.active {
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }

        .toc-item.level-2 .toc-link {
            padding-left: 1.5rem;
        }

        .toc-item.level-3 .toc-link {
            padding-left: 2.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .search-container {
                width: 150px;
            }

            .sidebar {
                position: absolute;
                left: -280px;
                top: 60px;
                height: calc(100vh - 60px);
                z-index: 50;
                transition: left 0.3s;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            }

            .sidebar.open {
                left: 0;
            }

            .toc-sidebar {
                display: none;
            }

            .content-area {
                padding: 1.5rem;
            }

            .document-title {
                font-size: 1.5rem;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .language-switcher.active-lang {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* ================= RTL Fixes ================= */
        /* Default (English) */
        /* Base arrow */
        .nav-toggle {
            transition: transform 0.2s ease;
        }

        /* EN behavior */
        html[dir="ltr"] .nav-toggle {
            transform: rotate(0deg);
            /* ▶ */
        }

        html[dir="ltr"] .nav-toggle.expanded {
            transform: rotate(90deg);
            /* ▼ */
        }

        /* AR behavior */
        html[dir="rtl"] .nav-toggle {
            transform: rotate(180deg);
            /* ◀ */
        }

        html[dir="rtl"] .nav-toggle.expanded {
            transform: rotate(90deg);
            /* ▼ */
        }

        .logo {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <a href="{{ route('system-docs.index') }}" class="logo-link">
                <div class="logo">
                    @if ($logo)
                        <img src="{{ asset($logo) }}" alt="Platform Logo">
                    @else
                        📚 Docs
                    @endif
                </div>
            </a>

            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search documentation...">
            </div>
        </div>
        {{-- <div class="header-right">
            <button class="language-switcher" onclick="toggleLanguage()">English</button>
        </div> --}}
        <div class="header-right">

            <a href="{{ route('lang.switch', 'en') }}"
                class="language-switcher {{ app()->getLocale() == 'en' ? 'active-lang' : '' }}">
                English
            </a>

            <a href="{{ route('lang.switch', 'ar') }}"
                class="language-switcher {{ app()->getLocale() == 'ar' ? 'active-lang' : '' }}">
                العربية
            </a>

        </div>


    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-title"> {{ __('docs.system_documentation') }}:
            </div>

            @foreach ($categories as $category)
                @php
                    $isCategoryActive =
                        isset($document) && $category->children->pluck('id')->contains($document->document_category_id);
                @endphp

                {{-- MAIN CATEGORY --}}
                <div class="nav-item category" onclick="toggleMenu(this)">
                    <button class="nav-toggle {{ $isCategoryActive ? 'expanded' : '' }}">▶</button>
                    <span>{{ $category->translation?->name }}</span>
                </div>

                <div class="nav-submenu {{ $isCategoryActive ? '' : 'collapsed' }}">

                    @foreach ($category->children as $sub)
                        @php
                            $isSubActive = isset($document) && $document->document_category_id == $sub->id;
                        @endphp

                        {{-- SUB CATEGORY --}}
                        <div class="nav-item sub-item" style="padding-left:2.5rem;" onclick="toggleMenu(this)">

                            <button class="nav-toggle {{ $isSubActive ? 'expanded' : '' }}">▶</button>
                            <span>{{ $sub->translation?->name }}</span>
                        </div>

                        <div class="nav-submenu {{ $isSubActive ? '' : 'collapsed' }}">

                            @foreach ($sub->documents as $doc)
                                <a href="{{ route('system-docs.show', $doc->id) }}"
                                    class="nav-item sub-item
                       {{ isset($document) && $document->id == $doc->id ? 'active' : '' }}">

                                    {{ $doc->translations->firstWhere('language', app()->getLocale())?->title }}
                                </a>
                            @endforeach

                        </div>
                    @endforeach

                </div>
            @endforeach

        </div>


        <!-- Main Content -->
        <div class="main-content">
            @yield('content')

        </div>
    </div>

    <script>
        function toggleMenu(element) {

            const submenu = element.nextElementSibling;
            const toggle = element.querySelector('.nav-toggle');

            if (!submenu) return;

            submenu.classList.toggle('collapsed');

            if (toggle) {
                toggle.classList.toggle('expanded');
            }
        }

        function toggleLanguage() {
            const btn = event.target;
            const currentLang = btn.textContent;
            const newLang = currentLang === 'English' ? 'العربية' : 'English';
            btn.textContent = newLang;

            // In a real application, this would trigger language switching
            alert(`Language switched to ${newLang}. (Demo only)`);
        }
    </script>

</body>

</html>
