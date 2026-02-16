@extends('layouts.master')
@section('title', __('document_categories'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('document_categories') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('document_categories') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between items-center">
                            <h5 class="box-title">{{ __('document_categories') }}</h5>

                            @can('create_document_categories')
                                <a href="{{ route('document-categories.create') }}"
                                    class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i>{{ __('add_category') }}
                                </a>
                            @endcan
                        </div>

                        <div class="box-body">

                            <ul class="space-y-2">
                                @foreach ($categories as $category)
                                    @include('pages.system-documents.categories.tree-node', [
                                        'category' => $category,
                                        'level' => 0,
                                    ])
                                @endforeach
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('toggle-btn')) {
                let target = document.getElementById(e.target.dataset.target);
                target.classList.toggle('hidden');
                e.target.textContent = target.classList.contains('hidden') ? '+' : '-';
            }
        });
    </script>
@endpush
