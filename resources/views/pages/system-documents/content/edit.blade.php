@extends('layouts.master')
@section('title', __('edit_document_content'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">

            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">
                        {{ __('edit_document_content') }}
                    </h3>
                </div>

                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('document-contents.index') }}">
                            {{ __('documents') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>

                    <li class="text-[0.813rem] font-semibold">
                        {{ __('edit_document_content') }}
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
                                {{ __('edit_document_content') }}
                            </h5>
                        </div>

                        <div class="box-body">

                            <form action="{{ route('document-contents.update', $document->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-12 gap-4">

                                    {{-- Main Category --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('category') }}
                                        </label>

                                        <select id="main-category"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">

                                            <option value="">{{ __('main_category') }}</option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('main_category', $document->category->parent_id ?? $document->category->id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->translation?->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Sub Category --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('sub_category') }}
                                        </label>

                                        <select name="document_category_id" id="sub-category"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">

                                            <option value="{{ $document->document_category_id }}">
                                                {{ $document->category->translation?->name }}
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Title EN --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('title_en') }}
                                        </label>

                                        <input type="text" name="title_en"
                                            value="{{ old('title_en', $document->translations->firstWhere('language', 'en')?->title) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                                            placeholder="{{ __('enter_title_en') }}">

                                        @error('title_en')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Title AR --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('title_ar') }}
                                        </label>

                                        <input type="text" name="title_ar"
                                            value="{{ old('title_ar', $document->translations->firstWhere('language', 'ar')?->title) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                                            placeholder="{{ __('enter_title_ar') }}">

                                        @error('title_ar')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Content EN --}}
                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('content_en') }}
                                        </label>

                                        <textarea name="content_en" id="content_en" class="summernote">
                                        {{ old('content_en', $document->translations->firstWhere('language', 'en')?->content) }}
                                    </textarea>

                                        @error('content_en')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Content AR --}}
                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('content_ar') }}
                                        </label>

                                        <textarea name="content_ar" id="content_ar" class="summernote">
                                        {{ old('content_ar', $document->translations->firstWhere('language', 'ar')?->content) }}
                                    </textarea>

                                        @error('content_ar')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\DocumentContentRequest') !!}

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {

            $('.summernote').summernote({
                height: 250,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'table']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let mainSelect = document.getElementById('main-category');
            let subSelect = document.getElementById('sub-category');

            let selectedSubId = "{{ old('document_category_id', $document->document_category_id) }}";

            function loadChildren(parentId, autoSelect = false) {

                subSelect.innerHTML = `<option value="">{{ __('loading') }}</option>`;
                subSelect.disabled = false;

                if (!parentId) {
                    subSelect.innerHTML = `<option value="">{{ __('select_sub_category') }}</option>`;
                    subSelect.disabled = true;
                    return;
                }

                fetch(`/document-categories/${parentId}/children`)
                    .then(response => response.json())
                    .then(data => {

                        if (data.length === 0) {

                            // ❌ Do NOT assign parent as sub
                            subSelect.innerHTML =
                                `<option value="">{{ __('no_sub_categories_add_one') }}</option>`;

                            subSelect.disabled = true;
                            return;
                        }

                        subSelect.innerHTML = `<option value="">{{ __('select_sub_category') }}</option>`;

                        data.forEach(function(child) {

                            let selected = (autoSelect && child.id == selectedSubId) ? 'selected' : '';

                            subSelect.innerHTML +=
                                `<option value="${child.id}" ${selected}>
                            ${child.translation?.name ?? ''}
                        </option>`;
                        });

                        subSelect.disabled = false;
                    });
            }

            // 🔥 On main change
            mainSelect.addEventListener('change', function() {
                loadChildren(this.value);
            });

            // 🔥 Auto load children on page load (important for edit)
            if (mainSelect.value) {
                loadChildren(mainSelect.value, true);
            }

        });
    </script>
@endpush
