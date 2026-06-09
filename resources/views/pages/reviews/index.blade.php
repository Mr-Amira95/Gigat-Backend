@extends('layouts.master')
@section('title', __('reviews'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('reviews') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('reviews') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('reviews') }}</h5>
                        </div>
                        <div class="box-body">
                            <table class="table" id="basic-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('rater') }}</th>
                                        <th>{{ __('ratee') }}</th>
                                        <th>{{ __('review') }}</th>
                                        <th>{{ __('rating') }}</th>
                                        <th>{{ __('date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reviews as $review)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $review->rater->username ?? __('Anonymous') }}</td>
                                            <td>{{ $review->rated->username ?? __('Anonymous') }}</td>
                                            <td>{{ $review->review ?? '-' }}</td>
                                            <td>
                                                @if ($review->rating)
                                                    <div class="flex" style="color: gold">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="las la-star{{ $i <= $review->rating ? '' : '-o' }} text-yellow-400"></i>
                                                        @endfor
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $review->created_at }}</td>
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
