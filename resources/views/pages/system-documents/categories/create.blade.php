@extends('layouts.master')
@section('title', __('create_document_category'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">
                        {{ __('create_document_category') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('document-categories.index') }}">
                            {{ __('document_categories') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">
                        {{ __('create_document_category') }}
                    </li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">
                                {{ __('create_document_category') }}
                            </h5>
                        </div>

                        <div class="box-body">
                            <form action="{{ route('document-categories.store') }}" method="POST">
                                @csrf

                                <div class="grid grid-cols-12 gap-4">

                                    {{-- Parent Category --}}
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('parent_category') }}
                                        </label>

                                        <select name="parent_id"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">{{ __('main_category') }}</option>
                                            @foreach ($parents as $parent)
                                                <option value="{{ $parent->id }}"
                                                    {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->translation?->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('parent_id')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Name EN --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('name_en') }}
                                        </label>

                                        <input type="text" name="name_en" value="{{ old('name_en') }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">

                                        @error('name_en')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Name AR --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('name_ar') }}
                                        </label>

                                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" dir="rtl"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">

                                        @error('name_ar')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>

                                <div class="mt-6 flex justify-center">
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
    </div>
@endsection
