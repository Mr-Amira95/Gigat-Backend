@extends('layouts.master')
@section('title', __('terms'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('terms') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold text-primary">{{ __('terms') }}</li>
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
                            <h5 class="text-lg font-semibold">{{ __('terms') }}</h5>
                        </div>
                        <form action="{{ route('general.updateTerms') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-12 gap-4">
                                @foreach ($terms as $key => $value)
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            {{ $key === 'terms_en' ? __('terms_en') : __('terms_ar') }}
                                        </label>
                                        <textarea id="{{ $key }}" name="{{ $key }}" class="summernote">{{ $value }}</textarea>

                                    </div>
                                @endforeach
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
                return $(element).attr('id') === 'terms_en' ?
                    "{{ __('validation.terms_en.required') }}" :
                    "{{ __('validation.terms_ar.required') }}";
            });

            $('form').validate({
                ignore: ':hidden:not(.summernote)',
                rules: {
                    'terms_en': {
                        summernoteRequired: true
                    },
                    'terms_ar': {
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
                $('#terms_en').val($('#terms_en').summernote('code'));
                $('#terms_ar').val($('#terms_ar').summernote('code'));
                return true;
            });
        });
    </script>
@endpush
