@extends('layouts.master')
@section('title', __('add_portfolio'))
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('add_portfolio') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.portfolios.index') }}">
                            {{ __('portfolios') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('add_portfolio') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('add_portfolio') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('freelancer.portfolios.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-12 gap-4 my-3">
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title') }}</label>
                                        <input type="text" name="title" value="{{ old('title') }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('description') }}</label>
                                        <textarea name="description" id="description" cols="30" rows="5"
                                            class="summernote mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('cover') }}</label>
                                        <input type="file" name="cover"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('media') }}</label>
                                        <input type="file" name="media[]"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                                            multiple>
                                    </div>
                                    {{-- <div class="col-span-12 md:col-span-6">
                                        <label for="freelancer_id"
                                            class="block text-sm font-medium text-gray-700">{{ __('freelancers') }}</label>
                                        <select name="user_id" id="freelancer_id"
                                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">{{ __('freelancers') }}</option>
                                            @foreach ($freelancers as $freelancer)
                                                <option value="{{ $freelancer->id }}">{{ $freelancer->username }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                                    <div class="col-span-12 md:col-span-12">
                                        <label for="service_ids"
                                            class="block text-sm font-medium text-gray-700">{{ __('services') }}</label>
                                        <select name="service_ids[]" id="service_ids"
                                            class="js-example-basic-multiple mt-1 block w-full rounded-lg border-gray-300"
                                            multiple>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ collect(old('service_ids'))->contains($service->id) ? 'selected' : '' }}>
                                                    {{ $service->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="mt-6 text-center">
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
    {!! JsValidator::formRequest('App\Http\Requests\Freelancer\PortfolioRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let oldSelectedServices = {!! json_encode(old('service_ids', [])) !!};

            function initializeSelect2() {
                $('#service_ids').select2({
                    placeholder: "{{ __('select_services') }}",
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false
                });
            }

            initializeSelect2();

            $('#freelancer_id').on('change', function() {
                console.log(oldSelectedServices);

                let freelancerId = $(this).val();
                if (!freelancerId) return;

                $.ajax({
                    url: '{{ url('api/v1/services/get-by-user') }}',
                    type: 'GET',
                    data: {
                        user_id: freelancerId
                    },
                    success: function(response) {
                        if (response.status) {
                            let options = '';
                            response.data.services.forEach(service => {
                                let selected = oldSelectedServices.includes(service.id
                                    .toString()) ? 'selected' : '';
                                options +=
                                    `<option value="${service.id}" ${selected}>${service.title}</option>`;
                            });
                            $('#service_ids').html(options).trigger('change');
                        }
                    }
                });
            });

            @if (old('freelancer_id'))
                $('#freelancer_id').val('{{ old('freelancer_id') }}').trigger('change');
            @endif
        });
    </script>



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

            // add validation method
            // $.validator.addMethod("summernoteRequired", function(value, element) {
            //     let content = $(element).summernote('code');
            //     content = content.replace(/<br\/?>/gi, '')
            //         .replace(/&nbsp;/gi, '')
            //         .replace(/\s+/g, '')
            //         .trim();
            //     return content.length > 0;
            // }, "{{ __('validation.description.required') }}");

            // initialize validation
            // $('form').validate({
            //     ignore: ':hidden:not(.summernote)',
            //     rules: {
            //         'description': {
            //             summernoteRequired: true
            //         }
            //     },
            //     errorElement: 'p',
            //     errorClass: 'text-red-600 mt-1 text-sm',
            //     errorPlacement: function(error, element) {
            //         if (element.hasClass('summernote')) {
            //             error.insertAfter(element.next('.note-editor'));
            //         } else {
            //             error.insertAfter(element);
            //         }
            //     }
            // });

            // // on submit, update hidden textarea value with summernote content
            // $('form').on('submit', function() {
            //     $('#description').val($('#description').summernote('code'));
            //     return true;
            // });
        });
    </script>
@endpush
