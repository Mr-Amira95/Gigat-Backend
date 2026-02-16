@extends('layouts.master')
@section('title', __('documents'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">

            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('documents') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('documents') }}</li>
                </ol>
            </div>

        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">

                        <div class="box-header flex justify-between items-center">
                            <h5 class="box-title">{{ __('documents') }}</h5>

                            @can('create_document_contents')
                                <a href="{{ route('document-contents.create') }}"
                                    class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i>{{ __('add_document') }}
                                </a>
                            @endcan
                        </div>

                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('category') }}</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('created_at') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <!-- Category -->
                                            <td>
                                                {{ optional($document->category->translation)->name ?? '-' }}
                                            </td>

                                            <!-- EN Preview -->
                                            <td>
                                                {{-- {!! Str::limit(strip_tags($document->translation->content), 50) !!} --}}
                                                {{ optional($document->translation)->title ?? '-' }}

                                            </td>


                                            <td>{{ $document->created_at->format('Y-m-d') }}</td>

                                            <td>

                                                @can('show_document_contents')
                                                    <a href="{{ route('document-contents.show', $document->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('edit_document_contents')
                                                    <a href="{{ route('document-contents.edit', $document->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete_document_contents')
                                                    <a href="javascript:void(0);"
                                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $document->id }})"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                        <i class="las la-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $document->id }}"
                                                        action="{{ route('document-contents.destroy', $document->id) }}"
                                                        method="POST" class="hidden">
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

    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();
        });
    </script>
@endpush
