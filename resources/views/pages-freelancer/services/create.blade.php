@extends('layouts.master')
@section('title', __('create_service'))
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('create_service') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.services.index') }}">
                            {{ __('services') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('create_service') }}</li>
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
                            <h5 class="box-title">{{ __('create_service') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('freelancer.services.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @php
                                    $planIndex = 0;
                                @endphp
                                <div class="grid grid-cols-12 gap-4">
                                    <!-- Category Selection -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('categories') }}</label>
                                        <select id="category-filter" name="category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-red-600 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Subcategory Selection -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('select_subcategory') }}</label>
                                        <select id="subcategory-filter" name="sub_category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_subcategory') }}</option>
                                            {{-- subcategories will be loaded dynamically via JS based on selected category --}}
                                        </select>
                                    </div>


                                    <!-- Title -->
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title') }}</label>
                                        <input type="text" name="title" value="{{ old('title') }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <!-- Freelancer -->
                                    {{-- <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('freelancer') }}</label>
                                        <select name="user_id" id="user_id" class="form-control mt-1">
                                            <option value="" selected disabled>{{ __('select_options') }}</option>
                                            @foreach ($freelancers as $freelancer)
                                                <option value="{{ $freelancer->id }}">{{ $freelancer->username }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                                    <!-- Description -->
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('description') }}</label>
                                        <textarea name="description" rows="3"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-4 mt-4">
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('select_currency') }}</label>
                                        <select name="currency_id" id="currency-filter" class="form-control" required>
                                            <option value="">{{ __('select_currency') }}</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}">
                                                    {{ $currency->name }} ({{ $currency->symbol }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="grid grid-cols-12 gap-4 mt-4">
                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('tags') }}</label>
                                        <select name="tags[]" id="tags"
                                            class="js-example-basic-multiple mt-1 block w-full rounded-lg border-gray-300"
                                            multiple>
                                        </select>
                                    </div>
                                </div>
                                <!-- Media Uploads -->
                                <div class="grid grid-cols-12 gap-4 mt-4">
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('cover') }}</label>
                                        <input type="file" name="cover" accept="image/*"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('thumbnails_images') }}</label>
                                        <input type="file" name="media[]" accept="image/*" multiple
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                </div>

                                <!-- Plans and Features -->
                                {{-- <div class="plans-wrapper mt-3" style="display:none;">
                                    <div class="features-wrapper mt-5">
                                    </div>
                                </div> --}}
                                <div class="plans-wrapper mt-3">
                                    <div class="plan-item border p-4 rounded-md mb-6 shadow-sm">
                                        <div class="flex justify-end items-center mb-3">
                                            <!-- Hidden remove button for the first plan -->
                                            <button type="button"
                                                class="remove-plan bg-red-500 text-white px-3 py-1 rounded-md hidden">
                                                <i class="ti ti-trash"></i> {{ __('remove_plan') }}
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-3">
                                                <label>{{ __('plan') }}</label>
                                                <select name="plans[0][plan_id]"
                                                    class="form-select w-full mt-1 plan-select">
                                                    <option value="">{{ __('select_plan') }}</option>
                                                    @foreach ($plans as $plan)
                                                        <option value="{{ $plan->id }}">
                                                            {{ $plan->translation->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="features-wrapper mt-5">
                                            <!-- Price -->
                                            <div class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
                                                <div class="col-span-5">
                                                    <input readonly type="text" name="plans[0][features][0][title]"
                                                        value="{{ __('price') }}"
                                                        class="form-input w-full border-gray-300 rounded-md bg-gray-100" />
                                                </div>
                                                <div class="col-span-5">
                                                    <input type="number" name="plans[0][features][0][value]"
                                                        class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" />
                                                </div>
                                                <input type="hidden" name="plans[0][features][0][type]"
                                                    value="price" />
                                            </div>

                                            <!-- Revisions -->
                                            <div class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
                                                <div class="col-span-5">
                                                    <input readonly type="text" name="plans[0][features][1][title]"
                                                        value="{{ __('revisions') }}"
                                                        class="form-input w-full border-gray-300 rounded-md bg-gray-100" />
                                                </div>
                                                <div class="col-span-5">
                                                    <input type="number" name="plans[0][features][1][value]"
                                                        class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" />
                                                </div>
                                                <input type="hidden" name="plans[0][features][1][type]"
                                                    value="revisions" />
                                            </div>

                                            <!-- Delivery Days -->
                                            <div class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
                                                <div class="col-span-5">
                                                    <input readonly type="text" name="plans[0][features][2][title]"
                                                        value="{{ __('delivery_days') }}"
                                                        class="form-input w-full border-gray-300 rounded-md bg-gray-100" />
                                                </div>
                                                <div class="col-span-5">
                                                    <input type="number" name="plans[0][features][2][value]"
                                                        class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary" />
                                                </div>
                                                <input type="hidden" name="plans[0][features][2][type]"
                                                    value="delivery_days" />
                                            </div>

                                            <!-- Source Files -->
                                            <div class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
                                                <div class="col-span-5">
                                                    <input type="text" name="plans[0][features][3][title]"
                                                        value="{{ __('source_files') }}" readonly
                                                        class="form-input w-full border-gray-300 rounded-md bg-gray-100" />
                                                </div>
                                                <div class="col-span-5">
                                                    <select name="plans[0][features][3][value]"
                                                        class="form-select w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                                        <option value="0">{{ __('without_source_files') }}</option>
                                                        <option value="1">{{ __('with_source_files') }}</option>
                                                    </select>
                                                </div>
                                                <input type="hidden" name="plans[0][features][3][type]"
                                                    value="source_files" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 text-end">
                                    <button type="button" class="add-plan bg-primary px-4 py-2 rounded-md text-white">
                                        <i class="ti ti-plus"></i> {{ __('add_plan') }}
                                    </button>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-6 flex justify-center">
                                    {{-- <div></div> --}}
                                    {{-- <div> --}}
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('save') }}
                                    </button>
                                    {{-- </div> --}}
                                    {{-- <div>
                                        <button type="button"
                                            class="add-plan bg-secondary px-4 py-2 rounded-md text-white"><i
                                                class="ti ti-plus">{{ __('add_plan') }}</i></button>
                                    </div> --}}

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
    {{-- {!! JsValidator::formRequest('App\Http\Requests\Admin\ServiceRequest') !!} --}}
    {!! JsValidator::formRequest('App\Http\Requests\Freelancer\ServiceRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select2 init
            $('.js-example-basic-multiple').select2({
                placeholder: "",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });

            $('#subcategory-filter').prop('disabled', true);

            $('#category-filter').on('change', function() {
                const categoryId = $(this).val();

                if (categoryId) {
                    fetchSubCategories(categoryId);
                    $('#subcategory-filter').prop('disabled', false);
                } else {
                    $('#subcategory-filter')
                        .html('<option value="">{{ __('select_subcategory') }}</option>')
                        .prop('disabled', true);
                }
            });
    
            function fetchSubCategories(categoryId) {
                $.ajax({
                    url: "{{ url('/api/v1/sub-categories') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function(response) {
                        if (response.status) {
                            let options = '<option value="">{{ __('select_subcategory') }}</option>';
                            response.data.forEach(sub => {
                                options += `<option value="${sub.id}">${sub.title}</option>`;
                            });
                            $('#subcategory-filter').html(options);
                        } else {
                            $('#subcategory-filter').html(
                                '<option value="">{{ __('no_subcategories_found') }}</option>');
                        }
                    },

                    error: function() {
                        $('#subcategory-filter').html(
                            '<option value="">{{ __('no_subcategories_found') }}</option>');
                    }
                });
            }

            function fetchTags(subCategoryId) {
                $.ajax({
                    url: "{{ url('/api/v1/tags') }}",
                    type: "GET",
                    data: {
                        subcategory_id: subCategoryId
                    },
                    success: function(response) {
                        if (response.status) {
                            let options = '<option value="">{{ __('select_tags') }}</option>';
                            response.data.forEach(tag => {
                                options +=
                                    `<option value="${tag.id}">${tag.translation.title}</option>`;
                            });
                            $('#tags').html(options);
                        } else {
                            $('#tags').html('<option value="">{{ __('no_tags_found') }}</option>');
                        }
                    },
                    error: function() {
                        $('#tags').html('<option value="">{{ __('no_tags_found') }}</option>');
                    }
                });
            }
            $('#subcategory-filter').on('change', function() {
                const subCategoryId = $(this).val();
                fetchTags(subCategoryId);
            });

            // Add New Feature
            // $(document).on('click', '.add-feature', function() {
            //     const planItem = $(this).closest('.plan-item');
            //     const planIndex = $('.plan-item').index(planItem);
            //     const featureCount = planItem.find('.feature-item').length;

            //     const featureHTML = `
        //     <div class="feature-item grid grid-cols-12 gap-4 mt-3">
        //         <div class="col-span-5">
        //             <input type="text" name="plans[${planIndex}][features][${featureCount}][title]" placeholder="{{ __('Title') }}" class="form-control w-full" />
        //         </div>
        //         <div class="col-span-5">
        //             <input type="text" name="plans[${planIndex}][features][${featureCount}][value]" placeholder="{{ __('Value') }}" class="form-control w-full" />
        //         </div>

        //     </div>`;
            //     planItem.find('.features-wrapper').append(featureHTML);
            // });

            // Remove Feature
            $(document).on('click', '.remove-feature', function() {
                $(this).closest('.feature-item').remove();
            });
            // Add Plan
            $(document).on('click', '.add-plan', function() {
                // $('.plans-wrapper').show();

                const planIndex = $('.plan-item').length;


                const newPlanHTML = `
                <div class="plan-item border p-4 rounded-md mb-6 shadow-sm">
                    <div class="flex justify-end items-center mb-3">
                        <button type="button" class="remove-plan bg-red-500 text-white px-3 py-1 rounded-md">
                            <i class="ti ti-trash"></i> {{ __('remove_plan') }}
                        </button>
                    </div>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-3">
                            <label>{{ __('plan') }}</label>
                            <select name="plans[${planIndex}][plan_id]" class="form-select w-full mt-1 plan-select">
                                <option value="">{{ __('select_plan') }}</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->translation->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="features-wrapper mt-5">
                        ${generateFixedFeature(planIndex, 0, 'price')}
                        ${generateFixedFeature(planIndex, 1, 'revisions')}
                        ${generateFixedFeature(planIndex, 2, 'delivery_days')}
                        ${generateFixedFeature(planIndex, 3, 'source_files')}
                    </div>

                </div>
            `;
                $('.plans-wrapper').append(newPlanHTML);
                if ($('.plan-item').length >= 3) {
                    $('.add-plan').hide();
                }
                updatePlanOptions();
            });
            $(document).on('click', '.remove-plan', function() {
                $(this).closest('.plan-item').remove();
                updatePlanOptions();
            });

            function updatePlanOptions() {
                const selectedPlans = [];
                $('.plan-select').each(function() {
                    const val = $(this).val();
                    if (val) selectedPlans.push(val);
                });
                $('.plan-select').each(function() {
                    const currentSelect = $(this);
                    const currentVal = currentSelect.val();
                    currentSelect.find('option').each(function() {
                        const optionVal = $(this).val();
                        if (optionVal && optionVal !== currentVal && selectedPlans.includes(
                                optionVal)) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                });
            }
            $(document).on('change', '.plan-select', function() {
                updatePlanOptions();
            });

            function generateFixedFeature(planIndex, featureIndex, type) {
                const labelMap = {
                    price: "{{ __('price') }}",
                    revisions: "{{ __('revisions') }}",
                    delivery_days: "{{ __('delivery_days') }}",
                    source_files: "{{ __('source_files') }}"
                };

                if (type === 'source_files') {
                    return `
                    <div class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
                        <div class="col-span-5">
                            <input type="text" name="plans[${planIndex}][features][${featureIndex}][title]"
                                value="${labelMap[type]}"
                                readonly
                                class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px] bg-gray-100" />
                        </div>
                        <div class="col-span-5">
                            <select id="source_file${planIndex}_${featureIndex}"
                                    name="plans[${planIndex}][features][${featureIndex}][value]"
                                    class="form-select w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px]">
                                <option value="0">{{ __('without_source_files') }}</option>
                                <option value="1">{{ __('with_source_files') }}</option>
                            </select>
                        </div>
                        <input type="hidden" name="plans[${planIndex}][features][${featureIndex}][type]" value="${type}" />
                    </div>
                        `;
                }

                // باقي الأنواع
                return `
                    <div class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
                        <div class="col-span-5">
                            <input readonly type="text" name="plans[${planIndex}][features][${featureIndex}][title]"
                                value="${labelMap[type]}"
                                placeholder="${labelMap[type]}"
                                class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px]  bg-gray-100" />
                        </div>
                        <div class="col-span-5">
                            <input type="number" name="plans[${planIndex}][features][${featureIndex}][value]"
                                class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px]" />
                        </div>
                        <input type="hidden" name="plans[${planIndex}][features][${featureIndex}][type]" value="${type}" />
                    </div>
                    `;
            }
        });
    </script>
@endpush
