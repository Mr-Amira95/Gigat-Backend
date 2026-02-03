@extends('layouts.master')
@section('title', __('edit_category'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('edit_category') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('categories.index') }}">
                            {{ __('categories') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('edit_category') }}</li>
                </ol>
            </div>
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
                            <h5 class="box-title">{{ __('edit_category') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('categories.update', $category->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('icon') }}</label>
                                        <input type="file" name="icon" accept=".svg"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">

                                        @if ($category->icon)
                                            <div class="mt-2">
                                                <img src="{{ asset($category->icon) }}" alt="{{ $category->translations->firstWhere('language', 'en')?->title }}"
                                                    class="w-16 h-16 object-cover rounded">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_en') }}</label>
                                        <input type="text" name="title_en"
                                            value="{{ old('title_en', $category->translations->firstWhere('language', 'en')?->title) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_ar') }}</label>
                                        <input type="text" name="title_ar"
                                            value="{{ old('title_ar', $category->translations->firstWhere('language', 'ar')?->title) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('description_en') }}</label>
                                        <textarea name="description_en" rows="3"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('description_en', $category->translations->firstWhere('language', 'en')?->description) }}</textarea>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('description_ar') }}</label>
                                        <textarea name="description_ar" rows="3"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('description_ar', $category->translations->firstWhere('language', 'ar')?->description) }}</textarea>
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateCategoryRequest') !!}
@endpush 
