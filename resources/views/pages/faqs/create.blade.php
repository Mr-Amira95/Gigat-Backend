@extends('layouts.master')
@section('title', __('create_faq'))
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('create_faq') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold text-primary">{{ __('create_faq') }}</li>
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
                            <h5 class="box-title">{{ __('create_faq') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('faqs.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('category') }}</label>
                                        <select name="category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="general">{{ __('general') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('question_en') }}</label>
                                        <input type="text" name="question_en" value="{{ old('question_en') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('question_ar') }}</label>
                                        <input type="text" name="question_ar" value="{{ old('question_ar') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('answer_en') }}</label>
                                        <textarea id="answer_en" name="answer_en" class="summernote">{{ old('answer_en') }}</textarea>
                                        @error('answer_en')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('answer_ar') }}</label>
                                        <textarea id="answer_ar" name="answer_ar" class="summernote">{{ old('answer_ar') }}</textarea>
                                        @error('answer_ar')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('media') }}</label>
                                        <input type="file" name="media" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                </div>
                                <div class="mt-4 text-center">
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\FaqRequest') !!}

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

            $.validator.addMethod("summernoteRequired", function(value, element) {
                let content = $(element).summernote('code');
                content = content.replace(/<br\/?>/gi, '')
                    .replace(/&nbsp;/gi, '')
                    .replace(/\s+/g, '')
                    .trim();
                return content.length > 0;
            }, function(params, element) {
                return $(element).attr('id') === 'answer_en' ?
                    "{{ __('validation.answer_en.required') }}" :
                    "{{ __('validation.answer_ar.required') }}";
            });

            $('form').validate({
                ignore: ':hidden:not(.summernote)',
                rules: {
                    'answer_en': {
                        summernoteRequired: true
                    },
                    'answer_ar': {
                        summernoteRequired: true
                    }
                },
                errorElement: 'p',
                errorClass: 'text-red-600 mt-1 text-sm',
                errorPlacement: function(error, element) {
                    if (element.hasClass('summernote')) {
                        error.insertAfter(element.next('.note-editor'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            $('form').on('submit', function() {
                $('#answer_en').val($('#answer_en').summernote('code'));
                $('#answer_ar').val($('#answer_ar').summernote('code'));
                return true;
            });
        });
    </script>
@endpush
