@extends('layouts.master')
@section('title', __('edit_service'))
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('edit_service') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.services.index') }}">
                            {{ __('services') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('edit_service') }}</li>
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
                            <h5 class="box-title">{{ __('edit_service') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('freelancer.services.update', $service->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                @php
                                    $planIndex = 0;
                                @endphp
                                {{-- @dd( $service->subCategory->category->id) --}}

                                <div class="grid grid-cols-12 gap-4">
                                    <!-- Category Selection -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('categories') }}</label>
                                        <select id="category-filter" name="category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $service->subCategory->category->id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Subcategory Selection -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('select_subcategory') }}</label>
                                        <select id="subcategory-filter" name="sub_category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_subcategory') }}</option>
                                        </select>
                                    </div>
                                    <!-- Title -->
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title') }}</label>
                                        <input type="text" name="title" value="{{ $service->translation->title }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <!-- Freelancer -->
                                    {{-- <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('freelancer') }}</label>
                                        <select name="user_id" id="user_id" class="form-control mt-1">
                                            <option value="" selected disabled>{{ __('select_options') }}</option>
                                            @foreach ($freelancers as $freelancer)
                                                <option value="{{ $freelancer->id }}"
                                                    {{ $service->user_id == $freelancer->id ? 'selected' : '' }}>
                                                    {{ $freelancer->username }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                                    <!-- Description -->
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('description') }}</label>
                                        <textarea name="description" rows="3"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ $service->translation->description }}</textarea>
                                    </div>
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('select_currency') }}</label>
                                        @php
                                            $selectedCurrencyId =
                                                old('currency_id') ??
                                                ($service->currency_id ??
                                                    \App\Models\Currency::where(
                                                        'code',
                                                        session('currency', 'USD'),
                                                    )->value('id'));
                                        @endphp

                                        <select name="currency_id" id="currency-filter" class="form-control" required>
                                            <option value="">{{ __('select_currency') }}</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}"
                                                    {{ $selectedCurrencyId == $currency->id ? 'selected' : '' }}>
                                                    {{ $currency->name }}
                                                    ({{ app()->getLocale() == 'ar' ? $currency->symbol_ar : $currency->symbol_en }})
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

                                {{-- <div class="grid grid-cols-12 gap-4 mt-4">
                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('tags') }}</label>
                                        <select name="tags[]" id="tags"
                                            class="js-example-basic-multiple mt-1 block w-full rounded-lg border-gray-300"
                                            multiple>
                                            @foreach ($tags as $tag)
                                                <option value="{{ $tag->slug }}">{{ $tag->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}
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

                                <div class="old-plans plans-wrapper  mt-3">
                                    @foreach ($servicePlans as $index => $servicePlan)
                                        <div class="plan-item border p-4 rounded-md mb-6 shadow-sm">
                                            {{-- <div class="flex justify-end items-center mb-3">
                                                <button type="button"
                                                    class="remove-plan bg-red-500 text-white px-3 py-1 rounded-md">
                                                    <i class="ti ti-trash"></i> {{ __('remove_plan') }}
                                                </button>
                                            </div> --}}
                                            <div class="flex justify-end items-center mb-3">
                                                <button type="button"
                                                    class="remove-plan bg-red-500 text-white px-3 py-1 rounded-md {{ $loop->first ? 'hidden' : '' }}">
                                                    <i class="ti ti-trash"></i> {{ __('remove_plan') }}
                                                </button>
                                            </div>


                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-3">
                                                    <label>{{ __('Plan') }}</label>
                                                    <select name="plans[{{ $index }}][plan_id]"
                                                        class="form-select w-full mt-1 plan-select">
                                                        <option value="">{{ __('Select Plan') }}</option>
                                                        @foreach ($plans as $plan)
                                                            <option value="{{ $plan->id }}"
                                                                {{ $servicePlan->plan->id == $plan->id ? 'selected' : '' }}>
                                                                {{ $plan->translation->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="features-wrapper mt-5">

                                                @foreach ($servicePlan->features as $fIndex => $feature)
                                                    <div class="feature-item grid grid-cols-12 gap-4 mt-3">
                                                        @if ($feature->type == 'source_files')
                                                            <div class="col-span-5">
                                                                <input type="text"
                                                                    name="plans[{{ $index }}][features][{{ $fIndex }}][title]"
                                                                    value="{{ $feature->translation->title ?? __('source_files') }}"
                                                                    readonly
                                                                    class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px] bg-gray-100" />
                                                            </div>
                                                            <div class="col-span-5">
                                                                <select
                                                                    id="source_file_{{ $index }}_{{ $fIndex }}"
                                                                    name="plans[{{ $index }}][features][{{ $fIndex }}][value]"
                                                                    class="form-select w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px]">
                                                                    <option value="0"
                                                                        {{ $feature->value == 0 ? 'selected' : '' }}>
                                                                        {{ __('without_source_files') }}</option>
                                                                    <option value="1"
                                                                        {{ $feature->value == 1 ? 'selected' : '' }}>
                                                                        {{ __('with_source_files') }}</option>
                                                                </select>
                                                            </div>
                                                            <input type="hidden"
                                                                name="plans[{{ $index }}][features][{{ $fIndex }}][type]"
                                                                value="{{ $feature->type }}" />
                                                        @elseif ($feature->type == 'price')
                                                            <div class="col-span-5">
                                                                <input readonly type="text"
                                                                    name="plans[{{ $index }}][features][{{ $fIndex }}][title]"
                                                                    value="{{ $feature->translation->title ?? '' }}"
                                                                    placeholder="{{ __('Title') }}"
                                                                    class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px] bg-gray-100" />
                                                            </div>
                                                            <div class="col-span-5">
                                                                <input type="number"
                                                                    name="plans[{{ $index }}][features][{{ $fIndex }}][value]"
                                                                    value="{{ (float) str_replace(',', '', \App\Utilities\CurrencyConverter::convert($feature->value, 'USD', $currentCurrency)) }}"
                                                                    placeholder="{{ __('Value') }}"
                                                                    class="form-control w-full" />
                                                            </div>
                                                        @else
                                                            <div class="col-span-5">
                                                                <input readonly type="text"
                                                                    name="plans[{{ $index }}][features][{{ $fIndex }}][title]"
                                                                    value="{{ $feature->translation->title ?? '' }}"
                                                                    placeholder="{{ __('Title') }}"
                                                                    class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px] bg-gray-100" />
                                                            </div>
                                                            <div class="col-span-5">
                                                                <input type="number"
                                                                    name="plans[{{ $index }}][features][{{ $fIndex }}][value]"
                                                                    value="{{ $feature->value }}"
                                                                    placeholder="{{ __('Value') }}"
                                                                    class="form-control w-full" />
                                                            </div>
                                                        @endif

                                                        <input type="hidden"
                                                            name="plans[{{ $index }}][features][{{ $fIndex }}][type]"
                                                            value="{{ $feature->type }}" />
                                                    </div>
                                                @endforeach

                                            </div>


                                        </div>
                                    @endforeach
                                </div>



                                <div class="mt-3 text-end">
                                    <button type="button" class="add-plan bg-primary px-4 py-2 rounded-md text-white">
                                        <i class="ti ti-plus"></i> {{ __('add_plan') }}
                                    </button>
                                </div>
                                <!-- Submit Button -->
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
    {!! JsValidator::formRequest('App\Http\Requests\Freelancer\UpdateServiceRequest') !!}
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

            // On page load, fetch subcategories for the saved category
            const initialCategoryId = $('#category-filter').val();
            if (initialCategoryId) {
                fetchSubCategories(initialCategoryId, "{{ $service->sub_category_id }}");
            }

            // When category changes, reload subcategories
            $('#category-filter').on('change', function() {
                const categoryId = $(this).val();
                fetchSubCategories(categoryId, null);
            });

            function fetchSubCategories(categoryId, selectedSubCategoryId = null) {
                if (!categoryId) {
                    $('#subcategory-filter')
                        .html('<option value="">{{ __('select_subcategory') }}</option>')
                        .prop('disabled', true);
                    return;
                }

                $.ajax({
                    url: "{{ url('/api/v1/sub-categories') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function(response) {
                        if (response.status && response.data.length > 0) {
                            let options = '<option value="">{{ __('select_subcategory') }}</option>';
                            response.data.forEach(sub => {
                                const selected = sub.id == selectedSubCategoryId ? 'selected' :
                                    '';
                                options +=
                                    `<option value="${sub.id}" ${selected}>${sub.title}</option>`;
                            });
                            $('#subcategory-filter').html(options).prop('disabled', false);
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


            const selectedTags = @json($selectedTags);
            const subCategoryId = "{{ $service->sub_category_id }}";

            function fetchTags(subCategoryId, selectedTags = []) {
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
                                const isSelected = selectedTags.includes(tag.id) ? 'selected' :
                                    '';
                                options +=
                                    `<option value="${tag.id}" ${isSelected}>${tag.translation.title}</option>`;
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

            $(document).ready(function() {
                if (subCategoryId) {
                    fetchTags(subCategoryId, selectedTags);
                }
            });


            // Add New Feature

            // Remove Feature
            $(document).on('click', '.remove-feature', function() {
                $(this).closest('.feature-item').remove();
            });
            // Add Plan
            $(document).on('click', '.add-plan', function() {
                $('.plans-wrapper').show();

                const plansCount = "{{ $plansCount }}";
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
                <label>{{ __('Plan') }}</label>
                <select name="plans[${planIndex}][plan_id]" class="form-select w-full mt-1 plan-select">
                    <option value="">{{ __('Select Plan') }}</option>
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
                if (planIndex < plansCount) {
                    $('.plans-wrapper').append(newPlanHTML);
                    if ($('.plan-item').length >= 3) {
                        $('.add-plan').hide();
                    }
                    updatePlanOptions();
                }
            });
            $(document).on('click', '.remove-plan', function() {
                $(this).closest('.plan-item').remove();
                updatePlanOptions();
            });

            function updatePlanOptions() {
                const selectedPlans = [];
                $('.plan-select').each(function() {
                    const
                        val = $(this).val();
                    if (val) selectedPlans.push(val);
                });
                $('.plan-select').each(function() {
                    const
                        currentSelect = $(this);
                    const currentVal = currentSelect.val();
                    currentSelect.find('option').each(function() {
                        const
                            optionVal = $(this).val();
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
                    return ` <div
        class="feature-item grid grid-cols-12 gap-4 mt-3 items-center">
        <div class="col-span-5">
            <input type="text" name="plans[${planIndex}][features][${featureIndex}][title]" value="${labelMap[type]}"
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
                    value="${labelMap[type]}" placeholder="${labelMap[type]}"
                    class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary text-base h-[42px] bg-gray-100" />
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
