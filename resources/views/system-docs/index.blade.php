@extends('system-docs.layout')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        {{-- <span class="breadcrumb-item">
            <a href="{{ route('system-docs.index') }}">Home</a>
        </span> --}}

        @if (isset($subCategory))
            {{-- <span class="breadcrumb-separator">/</span> --}}
            <span class="breadcrumb-item">
                {{ $subCategory->translation?->name }}
            </span>
        @endif

        @if (isset($category))
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item">
                {{ $category->translation?->name }}
            </span>
        @endif

        @if (isset($document))
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item">
                {{ $document->translations->firstWhere('language', app()->getLocale())?->title }}
            </span>
        @endif
    </div>


    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <div class="content-area">

            @if (isset($document))
                <div id="document-container">

                    <!-- Title -->
                    <h1 class="document-title">
                        {{ $document->translations->firstWhere('language', app()->getLocale())?->title }}
                    </h1>

                    <!-- Meta -->
                    <div class="document-meta">
                        <span>
                            {{ __('docs.last_updated') }}:
                            <strong>
                                {{ $document->updated_at->translatedFormat('M d, Y') }}
                            </strong>
                        </span>

                        <span>
                            {{ __('docs.category') }}:
                            <strong>
                                {{ $category->translation?->name ?? '-' }}
                            </strong>
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="document-content">
                        {!! $document->translations->firstWhere('language', app()->getLocale())?->content !!}
                    </div>

                </div>
            @else
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">📖</div>
                    <h2>{{ __('docs.select_document') }}</h2>
                    <p>{{ __('docs.choose_topic') }}</p>
                </div>
            @endif

        </div>

    </div>
@endsection
