@extends('layouts.master')
@section('title', __('pending_question_details'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('pending_question_details') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('pending-questions.index') }}">
                            {{ __('pending_questions') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('pending_question_details') }}</li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box bg-white p-6 shadow rounded-lg">
                        <div class="box-header flex justify-between items-center border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i> {{ __('pending_question_details') }}
                            </h5>
                            <a href="{{ route('pending-questions.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                <i class="fas fa-arrow-left"></i> {{ __('back') }}
                            </a>
                        </div>

                        <div class="box-body">

                            <div class="col-span-12 md:col-span-6">
                                <div
                                    class="p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow transition-shadow duration-200">
                                    <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                        <i class="fas fa-language text-blue-500"></i> {{ __('question') }}
                                    </h6>
                                    <div class="space-y-6">
                                        <div class="bg-white p-4 rounded-lg shadow-sm">
                                            <p class="text-gray-800 font-semibold">
                                                {{ $question->question }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('pending-questions.convert', $question->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <h6 class="text-lg font-semibold text-gray-700 mb-4 mt-4">{{ __('convert_to_faq') }}</h6>

                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('category') }}</label>
                                        <select name="category_id" class="form-select w-full rounded-lg border-gray-300">
                                            <option value="general">{{ __('general') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('question_en') }}</label>
                                        <input type="text" name="question_en" value="{{ old('question_en') }}"
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>

                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('question_ar') }}</label>
                                        <input type="text" name="question_ar" value="{{ old('question_ar') }}"
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>

                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('answer_en') }}</label>
                                        <textarea id="answer_en" name="answer_en" class="summernote">{{ old('answer_en') }}</textarea>
                                    </div>

                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('answer_ar') }}</label>
                                        <textarea id="answer_ar" name="answer_ar" class="summernote">{{ old('answer_ar') }}</textarea>
                                    </div>

                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('media') }}</label>
                                        <input type="file" name="media"
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('convert_to_faq') }}
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
    {!! JsValidator::formRequest('App\\Http\\Requests\\Admin\\FaqRequest') !!}
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
                ]
            });
        });
    </script>
@endpush
