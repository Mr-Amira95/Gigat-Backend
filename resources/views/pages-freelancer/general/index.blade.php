@extends('layouts.master')
@section('title', __('general'))

@push('styles')
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('general') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold text-primary">{{ __('general') }}</li>
                </ol>
            </div>
        </div>

        <div class="container mx-auto px-4">
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded-md mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <div class="border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold">{{ __('general_info') }}</h5>
                        </div>
                        <form action="{{ route('general.updateGeneralInfo') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-12 gap-6">
                                @foreach ($generals as $general)
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ app()->getLocale() == 'en' ? __(Str::title(str_replace('_', ' ', $general->key))) : __($general->key) }}
                                        </label>

                                        @if ($general->key == 'platform_logo')
                                            <input type="file"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-primary focus:border-primary"
                                                name="platform_logo">

                                            @if ($general->value)
                                                <div class="mt-2">
                                                    <img src="{{ asset($general->value) }}" alt="Platform Logo"
                                                        class="h-16">
                                                </div>
                                            @endif
                                        @else
                                            <input type="text"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-primary focus:border-primary"
                                                name="settings[{{ $general->key }}]"
                                                value="{{ old('settings.' . $general->key, $general->value) }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- <div class="grid grid-cols-12 gap-6">
                                <h5 class="col-span-12 text-lg font-semibold mt-3">{{ __('currencies') }}</h5>

                                @foreach ($currencies as $currency)
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $currency->code }}
                                        </label>
                                        <input type="text"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-primary focus:border-primary"
                                            name="currencies[{{ $currency->code }}][exchange_rate]"
                                            value="{{ old('currencies.' . $currency->code . '.exchange_rate', $currency->exchange_rate) }}">
                                    </div>
                                @endforeach
                            </div> --}}


                            <div class="mt-4 flex justify-center">
                                <button type="submit"
                                    class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                    <i class="las la-save"></i> {{ __('save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
