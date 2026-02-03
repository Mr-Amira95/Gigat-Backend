@extends('layouts.master')
@section('title', __('archived_freelancers'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('archived_freelancers') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                       <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('archived_freelancers') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('archived_freelancers') }}</h5>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('name') }}</th>
                                        <th>{{ __('email') }}</th>
                                        <th>{{ __('phone') }}</th>
                                        <th>{{ __('profession') }}</th>
                                        <th>{{ __('country') }}</th>
                                        <th>{{ __('deleted_at') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($freelancers as $freelancer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $freelancer->username }}</td>
                                            <td>{{ $freelancer->email }}</td>
                                            <td>{{ $freelancer->full_phone }}</td>
                                            <td>{{ $freelancer->profession->translation->title }}</td>
                                            <td>{{ $freelancer->country->title }}</td>
                                            <td>{{ $freelancer->deleted_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="showRestoreConfirmation('{{ __('are_you_sure_restore') }}', {{ $freelancer->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-undo"></i>
                                                </a>
                                                <form id="restore-form-{{ $freelancer->id }}"
                                                    action="{{ route('freelancers.restore', $freelancer->id) }}" method="POST"
                                                    class="hidden">
                                                    @csrf
                                                </form>
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

        function showRestoreConfirmation(message, id) {
            if (confirm(message)) {
                document.getElementById('restore-form-' + id).submit();
            }
        }
    </script>
@endpush 