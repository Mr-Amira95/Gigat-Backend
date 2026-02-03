@extends('layouts.master')

@section('title', __('add_comment'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content">
    <div class="main-content">
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-[1.125rem] font-semibold">{{ __('add_comment') }}</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap">
                <li class="text-[0.813rem] ps-[0.5rem]">
                    <a class="flex items-center text-primary hover:text-primary" href="{{ route('freelancer.home.index') }}">
                        <i class="ti ti-home me-1"></i> {{ __('home') }}
                        <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-[0.813rem] ps-[0.5rem]">
                    <a class="flex items-center text-primary hover:text-primary" href="{{ route('freelancer.quotations.show', $quotation->id) }}">
                        {{ __('quotation_information') }}
                        <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-[0.813rem] font-semibold text-primary">{{ __('add_comment') }}</li>
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
                        <h5 class="box-title">{{ __('add_comment') }}</h5>
                    </div>
                    <div class="box-body">
                        <form action="{{ route('freelancer.quotations.comment.store', $quotation->id) }}" method="POST">
                            @csrf
                            <div class="col-span-12 md:col-span-12">
                                <label class="block text-sm font-medium text-gray-700">{{ __('comment') }}</label>
                                <textarea id="comment" name="comment" class="w-full">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4 text-center">
                                <button type="submit" class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
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
        }, "{{ __('validation.required') }}");

        $('form').validate({
            ignore: ':hidden:not(.summernote)',
            rules: {
                'comment': {
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
            $('#comment').val($('#comment').summernote('code'));
            return true;
        });
    });
</script>
@endpush
