@extends('layouts.master')
@section('title', __('create_ticket_for_user'))
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('create_ticket_for_user') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('tickets.index') }}">
                            {{ __('tickets') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('create_ticket_for_user') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
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
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('create_ticket_for_user') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-12 gap-4 my-3">
                                    <div class="col-span-12 md:col-span-6">
                                        <label for="user_id"
                                            class="block text-sm font-medium text-gray-700">{{ __('select_user') }}</label>
                                        <select name="user_id" id="user_id"
                                            class="mt-1 block w-full rounded-lg border-gray-300" style="width:100%">
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label for="priority"
                                            class="block text-sm font-medium text-gray-700">{{ __('priority') }}</label>
                                        <select name="priority" id="priority"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                            <option value="low">{{ __('low') }}</option>
                                            <option value="medium" selected>{{ __('medium') }}</option>
                                            <option value="high">{{ __('high') }}</option>
                                            <option value="urgent">{{ __('urgent') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('subject') }}</label>
                                        <input type="text" name="subject" value="{{ old('subject') }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label for="link_type"
                                            class="block text-sm font-medium text-gray-700">{{ __('link_type') }}</label>
                                        <select name="link_type" id="link_type"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                            <option value="">{{ __('none') }}</option>
                                            <option value="request">{{ __('request') }}</option>
                                            <option value="service">{{ __('service') }}</option>
                                            <option value="portfolio">{{ __('portfolio') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label for="link_id"
                                            class="block text-sm font-medium text-gray-700">{{ __('select_link_item') }}</label>
                                        <select name="link_id" id="link_id"
                                            class="js-link-id mt-1 block w-full rounded-lg border-gray-300" style="width:100%"
                                            disabled>
                                            <option value="">{{ __('select_link_item') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('message') }}</label>
                                        <textarea name="message" id="message" cols="30" rows="5"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('message') }}</textarea>
                                    </div>

                                    <div class="col-span-12 md:col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('attachment') }}</label>
                                        <input type="file" name="attachment[]" multiple
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#user_id').select2({
                placeholder: "{{ __('search_user') }}",
                allowClear: true,
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('tickets.searchUsers') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response.data
                        };
                    }
                }
            });

            $('.js-link-id').select2({
                placeholder: "{{ __('select_link_item') }}",
                allowClear: true,
                width: '100%'
            });

            function fetchLinkOptions() {
                const userId = $('#user_id').val();
                const linkType = $('#link_type').val();
                const $linkId = $('#link_id');

                $linkId.html('<option value="">{{ __('select_link_item') }}</option>').trigger('change');

                if (!userId || !linkType) {
                    $linkId.prop('disabled', true);
                    return;
                }

                $linkId.prop('disabled', false);

                $.ajax({
                    url: "{{ url('tickets') }}/" + userId + "/link-options",
                    type: 'GET',
                    data: {
                        type: linkType
                    },
                    success: function(response) {
                        let options = '<option value="">{{ __('select_link_item') }}</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.id}">${item.text}</option>`;
                        });
                        $linkId.html(options).trigger('change');
                    },
                    error: function() {
                        console.error('Failed to fetch link options.');
                    }
                });
            }

            $('#user_id').on('change', fetchLinkOptions);
            $('#link_type').on('change', fetchLinkOptions);
        });
    </script>
@endpush
