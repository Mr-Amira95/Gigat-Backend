@extends('layouts.master')
@section('title', __('companies'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('companies') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('companies') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('companies') }}</h5>
                            <div class="ms-auto flex items-center gap-2">
                                @can('create_companies')
                                    <a href="{{ route('companies.create') }}"
                                        class="flex items-center gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                        <i class="las la-plus-circle text-lg"></i>{{ __('create_company') }}
                                    </a>
                                @endcan

                            </div>
                        </div>

                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('logo') }}</th>
                                        <th>{{ __('company_name') }}</th>
                                        <th>{{ __('email') }}</th>
                                        <th>{{ __('phone') }}</th>
                                        <th>{{ __('country') }}</th>
                                        <th>{{ __('registration_number') }}</th>
                                        <th>{{ __('freelancers') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($companies as $company)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($company->logo)
                                                    <img src="{{ asset($company->logo) }}" alt="logo"
                                                        class="w-12 h-12 rounded-full object-cover">
                                                @endif
                                            </td>
                                            <td>{{ $company->translation?->name }}</td>
                                            <td>{{ $company->contact_email }}</td>
                                            <td>{{ $company->contact_phone_number }}</td>
                                            <td>{{ $company->translation?->country_of_registration }}</td>
                                            <td>{{ $company->registration_number }}</td>

                                            <td>
                                                @if ($company->freelancers->isNotEmpty())
                                                    <ul class="ps-4 text-sm">
                                                        @foreach ($company->freelancers as $freelancer)
                                                            <li>{{ $freelancer->user->username ?? '-' }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-400">{{ __('no_freelancers') }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                @can('show_companies')
                                                    <a aria-label="anchor" href="{{ route('companies.show', $company->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-info mx-1 rounded-pill">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit_companies')
                                                    <a aria-label="anchor" href="{{ route('companies.edit', $company->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- box-body -->
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
