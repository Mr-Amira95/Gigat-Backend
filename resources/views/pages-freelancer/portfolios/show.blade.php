@extends('layouts.master')
@section('title', __('portfolio_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('portfolio_details') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.portfolios.index') }}">
                            {{ __('portfolios') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('portfolio_details') }}</li>
                </ol>
            </div>

            <div class="container mt-4">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12">
                        <div class="box">
                            <div class="box-header">
                                <h5 class="box-title">{{ $portfolio->translation->title }}</h5>
                            </div>
                            <div class="box-body space-y-4">
                                <div>
                                    <strong>{{ __('title') }}:</strong>
                                    <p>{{ $portfolio->translation->title }}</p>
                                </div>

                                <div>
                                    <strong>{{ __('description') }}:</strong>
                                    <p>{{ $portfolio->translation->description }}</p>
                                </div>

                                {{-- <div>
                                <strong>{{ __('freelancer') }}:</strong>
                                <p>{{ $portfolio->user?->username ?? '-' }}</p>
                            </div> --}}

                                <div>
                                    <strong>{{ __('services') }}:</strong>
                                    <ul class="list-disc ps-5">
                                        @foreach ($portfolio->services as $service)
                                            <li>{{ $service->translation->title }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div>
                                    <strong>{{ __('cover') }}:</strong><br>
                                    @if ($cover = $portfolio->media->where('is_cover', true)->first())
                                        <img src="{{ asset($cover->media_path) }}" alt="Cover"
                                            class="mt-2 rounded object-cover"
                                            style="max-width: 100%; height: auto; max-height: 300px;">
                                    @endif
                                </div>


                                <div>
                                    <strong>{{ __('media') }}:</strong><br>
                                    @forelse($portfolio->media as $media)
                                        <img src="{{ asset($media->media_path) }}" alt="Media"
                                            class="inline-block h-24 m-2 rounded shadow">
                                    @empty
                                        <p>{{ __('no_media_uploaded') }}</p>
                                    @endforelse
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
