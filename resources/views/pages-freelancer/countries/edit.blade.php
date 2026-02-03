@extends('layouts.master')
@section('title', __('edit_country'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('edit_country') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('countries.index') }}">
                            {{ __('countries') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('edit_country') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('edit_country') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('countries.update', $country->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title') }}</label>
                                        <input type="text" name="title" value="{{ old('title', $country->title) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('flag') }}</label>
                                        <input type="url" name="flag" value="{{ old('flag', $country->flag) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                        @if ($country->flag)
                                            <div class="mt-2">
                                                <img src="{{ $country->flag }}" alt="{{ $country->title }}"
                                                    class="w-16 h-12 object-cover rounded">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
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
@endsection

@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\CountryRequest') !!}
@endpush 