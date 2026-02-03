@extends('layouts.master')

@section('title', __('pending_questions'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('pending_questions') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('pending_questions') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('pending_questions') }}</h5>
                        </div>

                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('question') }}</th>
                                        <th>{{ __('created_at') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questions as $question)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $question->question }}</td>
                                            <td>{{ $question->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @can('show_pending_questions')
                                                    <a href="{{ route('pending-questions.show', $question->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill"
                                                        title="{{ __('view') }}">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_pending_questions')
                                                    <a href="javascript:void(0);"
                                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $question->id }})"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill"
                                                        title="{{ __('delete') }}">
                                                        <i class="las la-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $question->id }}"
                                                        action="{{ route('pending-questions.destroy', $question->id) }}"
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
                        </div> <!-- box-body -->
                    </div> <!-- box -->
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

        // function showDeleteConfirmation(message, id) {
        //     if (confirm(message)) {
        //         document.getElementById('delete-form-' + id).submit();
        //     }
        // }
    </script>
@endpush
