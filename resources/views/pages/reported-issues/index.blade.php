@extends('layouts.master')

@section('title', __('reported_issues'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('reported_issues') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('reported_issues') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('reported_issues') }}</h5>
                        </div>
                        <div class="box-footer border-t p-4">
                            <div class="grid grid-cols-12 gap-4 mb-4">

                                {{-- Status Filter --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="status-filter"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('status') }}</label>
                                    <select id="status-filter" name="status"
                                        class="w-full px-2 py-2 rounded-lg border-gray-300 text-gray-500">
                                        <option value="">{{ __('all_statuses') }}</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            {{ __('pending') }}
                                        </option>
                                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                            {{ __('resolved') }}
                                        </option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                            {{ __('cancelled') }}
                                        </option>
                                    </select>
                                </div>

                                {{-- Buttons --}}
                                <div class="col-span-12 md:col-span-4 flex items-end gap-2">
                                    <button type="button" id="filter-submit"
                                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">
                                        {{ __('submit_filter') }}
                                    </button>


                                    <a href="{{ route('reported-issues.index') }}"
                                        class="px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600">
                                        {{ __('reset_filter') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('user') }}</th>
                                        <th>{{ __('type') }}</th>
                                        <th>{{ __('type_id') }}</th>
                                        <th>{{ __('message') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('created_at') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($issues as $issue)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $issue->user->username ?? __('guest') }}</td>
                                            {{-- <td>{{ ucfirst($issue->type) }}</td> --}}
                                            <td> {!! \App\Enums\ReportIssueTypeEnum::tryFrom($issue->type)?->label() !!}
                                            </td>
                                            <td>{{ $issue->type_id ?? '-' }}</td>
                                            <td>{{ Str::limit($issue->message, 50) }}</td>
                                            <td> {!! \App\Enums\ReportIssueStatusEnum::tryFrom($issue->status)?->badge() !!}
                                            </td>

                                            <td>{{ $issue->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @can('view_reports')
                                                    @if ($issue->type === 'service')
                                                        <a href="{{ route('services.show', $issue->type_id) }}"
                                                            class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill"
                                                            title="{{ __('view') }}">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                    @elseif ($issue->type === 'portfolio')
                                                        <a href="{{ route('portfolios.show', $issue->type_id) }}"
                                                            class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill"
                                                            title="{{ __('view') }}">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                    @elseif ($issue->type === 'freelancer')
                                                        <a href="{{ route('freelancers.show', $issue->type_id) }}"
                                                            class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill"
                                                            title="{{ __('view') }}">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                    @else
                                                        {{-- General reports don’t have a details page --}}
                                                        {{-- <span class="text-gray-400"></span> --}}
                                                    @endif
                                                @endcan


                                                @can('resolve_cancel_reports')
                                                    @if ($issue->status == 'pending')
                                                        <form
                                                            action="{{ route('reported-issues.updateStatus', [$issue->id, 'resolved']) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill"
                                                                title="{{ __('resolve') }}">
                                                                <i class="las la-check"></i>
                                                            </button>
                                                        </form>

                                                        <form
                                                            action="{{ route('reported-issues.updateStatus', [$issue->id, 'cancelled']) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill"
                                                                title="{{ __('cancel') }}">
                                                                <i class="las la-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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
    </script>

    <script>
        $(document).ready(function() {
            $('#filter-submit').click(function() {
                const query = new URLSearchParams();

                // Only one filter (status) for reported issues
                if ($('#status-filter').val()) {
                    query.append('status', $('#status-filter').val());
                }

                const baseUrl = "{{ route('reported-issues.index') }}";
                window.location.href = `${baseUrl}?${query.toString()}`;
            });

            $('#filter-reset').click(function() {
                window.location.href = "{{ route('reported-issues.index') }}";
            });
        });
    </script>

@endpush
