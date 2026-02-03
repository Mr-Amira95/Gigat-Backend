@extends('layouts.master')
@section('title', __('faqs'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('faqs') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('faqs') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('faqs') }}</h5>
                            <div class="actions flex items-center gap-4">
                                <select id="category-filter" class="px-2 py-2 border rounded-lg">
                                    <option value="">{{ __('all_categories') }}</option>
                                    <option value="general" {{ request('category_id') == 'general' ? 'selected' : '' }}>
                                        {{ __('general') }}
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->translation->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @can('create_faqs')
                                    <a href="{{ route('faqs.create') }}"
                                        class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                        <i class="las la-plus-circle text-lg"></i> {{ __('add_faq') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('question') }}</th>
                                        <th>{{ __('answer') }}</th>
                                        <th>{{ __('category') }}</th>
                                        <th>{{ __('media') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faqs as $faq)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $faq->translation->question }}</td>
                                            <td>{!! Str::limit($faq->translation->answer, 50) !!}</td>
                                            <td>{{ $faq->faqable ? $faq->faqable->translation->title : __('general') }}
                                            </td>
                                            <td>
                                                @if ($faq->translation->media_path)
                                                    @if ($faq->translation->media_type === 'image')
                                                        <img src="{{ asset($faq->translation->media_path) }}"
                                                            alt="FAQ Media" class="w-10 h-10 object-cover rounded mx-auto">
                                                    @else
                                                        <i class="las la-video text-2xl"></i>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" id="hs-small-switch-{{ $faq->id }}"
                                                        class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                        data-item-id="{{ $faq->id }}"
                                                        data-route="{{ route('faqs.updateActivation') }}"
                                                        {{ $faq->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('faqs.show', $faq->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                @can('edit_faqs')
                                                    <a href="{{ route('faqs.edit', $faq->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_faqs')
                                                    <a href="javascript:void(0);"
                                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $faq->id }})"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $faq->id }}"
                                                        action="{{ route('faqs.destroy', $faq->id) }}" method="POST"
                                                        class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if (app()->getLocale() == 'en')
        <script src="{{ asset('build/assets/datatable/datatables-en.min.js') }}"></script>
    @else
        <script src="{{ asset('build/assets/datatable/datatables-ar.min.js') }}"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();

            $('#category-filter').on('change', function() {
                const categoryId = $(this).val();
                const currentUrl = new URL(window.location.href);

                if (categoryId) {
                    currentUrl.searchParams.set('category_id', categoryId);
                } else {
                    currentUrl.searchParams.delete('category_id');
                }

                window.location.href = currentUrl.toString();
            });
        });
    </script>
@endpush
