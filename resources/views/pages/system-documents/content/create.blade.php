@extends('layouts.master')
@section('title', __('create_document_content'))
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
                        {{ __('create_document_content') }}
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
                        {{ __('create_document_content') }}
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
                                {{ __('create_document_content') }}
                            </h5>
                        </div>

                        <div class="box-body">
                            <form action="{{ route('document-contents.store') }}" method="POST">
                                @csrf

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
                                                <option value="{{ $category->id }}">
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
                                            <option value="">{{ __('select_sub_category') }}</option>
                                        </select>
                                    </div>

                                    {{-- Title EN --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('title_en') }}
                                        </label>

                                        <input type="text" name="title_en" value="{{ old('title_en') }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                                            >

                                        @error('title_en')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Title AR --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('title_ar') }}
                                        </label>

                                        <input type="text" name="title_ar" value="{{ old('title_ar') }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                                            >

                                        @error('title_ar')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>


                                    {{-- Content EN --}}
                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('content_en') }}
                                        </label>

                                        <textarea name="content_en" id="content_en"
                                            class="summernote mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                                    {{ old('content_en') }}
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

                                        <textarea name="content_ar" id="content_ar"
                                            class="summernote mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                                    {{ old('content_ar') }}
                                                </textarea>

                                        @error('content_ar')
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
@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\DocumentContentRequest') !!}

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents, $editable) {
                        let textareaId = $(this).attr('id');
                        $('#' + textareaId).val(contents);
                        $('form').validate().element('#' + textareaId);
                    }
                }
            });

        });
    </script>
    <script>
        document.getElementById('main-category').addEventListener('change', function() {

            let parentId = this.value;
            let subSelect = document.getElementById('sub-category');

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

                        subSelect.disabled = true; // disable selection
                        return;
                    }

                    subSelect.innerHTML = `<option value="">{{ __('select_sub_category') }}</option>`;

                    data.forEach(function(child) {
                        subSelect.innerHTML +=
                            `<option value="${child.id}">${child.translation?.name ?? ''}</option>`;
                    });

                    subSelect.disabled = false;
                });
        });
    </script>
@endpush
