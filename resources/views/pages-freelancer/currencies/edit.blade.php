@extends('layouts.master')
@section('title', __('edit_currency'))

@section('content')
<div class="content">
    <div class="main-content">
        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-[1.125rem] font-semibold">{{ __('edit_currency') }}</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap">
                <li class="text-[0.813rem] ps-[0.5rem]">
                    <a class="flex items-center text-primary hover:text-primary" href="{{ route('home.index') }}">
                        <i class="ti ti-home me-1"></i> {{ __('home') }}
                        <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-[0.813rem] font-semibold text-primary">{{ __('edit_currency') }}</li>
            </ol>
        </div>

        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('edit_currency') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('currencies.update', $currency->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-12 gap-4">
                                    <!-- Code -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('code') }}</label>
                                        <input type="text" name="code" value="{{ old('code', $currency->code) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>

                                    <!-- Symbol -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('symbol_en') }}</label>
                                        <input type="text" name="symbol_en" value="{{ old('symbol_en', $currency->symbol_en) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>


                                    <!-- Symbol AR -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('symbol_ar') }}</label>
                                        <input type="text" name="symbol_ar" value="{{ old('symbol_ar', $currency->symbol_ar) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>

                                    <!-- Exchange Rate -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('exchange_rate') }}</label>
                                        <input type="text" name="exchange_rate" value="{{ old('exchange_rate', $currency->exchange_rate) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>


                                </div>

                                <div class="mt-4 text-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-dark rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('update') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
