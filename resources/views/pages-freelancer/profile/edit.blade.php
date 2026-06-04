@extends('layouts.master')
@section('title', __('edit_freelancer_profile'))
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- عنوان الصفحة -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold flex items-center">
                        <i class="ti ti-user-circle me-2 text-primary"></i> {{ __('edit_freelancer_profile') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('edit_freelancer_profile') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-body">
                            <form action="{{ route('freelancer.profile.update', $freelancer->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- صورة الأفتار -->
                                <div class="text-center mb-6">
                                    <img src="{{ $freelancer->avatar ? asset($freelancer->avatar) : asset('build/assets/images/faces/9.jpg') }}"
                                        alt="{{ $freelancer->username }}"
                                        class="mx-auto rounded-full w-32 h-32 object-cover mb-4 border-4 border-primary shadow-md" />
                                    <input type="file" name="avatar" class="block mx-auto mt-2" />
                                    @error('avatar')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- الاسم و الايميل -->
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="font-semibold" for="username">{{ __('username') }}:</label>
                                        <input type="text" name="username" id="username"
                                            value="{{ old('username', $freelancer->username) }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" />
                                        @error('username')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="font-semibold" for="email">{{ __('email') }}:</label>
                                        <input type="email" name="email" id="email"
                                            value="{{ old('email', $freelancer->email) }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" />
                                        @error('email')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                                <!-- prefix و phone -->
                                <div class="grid grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label class="font-semibold">{{ __('prefix') }}</label>
                                        <select name="prefix" id="prefix"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                            @foreach ($countries as $country)
                                                <option value="{{ $country['phone_code'] }}"
                                                    {{ old('prefix', $freelancer->prefix) == $country['phone_code'] ? 'selected' : '' }}>
                                                    {{ $country['phone_code'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="font-semibold">{{ __('phone') }}</label>
                                        <input type="text" name="phone" id="phone"
                                            value="{{ old('phone', $freelancer->phone) }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" />
                                    </div>
                                </div>

                                <!-- gender و profession -->
                                <div class="grid grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label class="font-semibold">{{ __('gender') }}</label>
                                        <select name="gender" id="gender"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">

                                            <option value="male"
                                                {{ old('gender', $freelancer->gender->value ?? '') == 'male' ? 'selected' : '' }}>
                                                {{ __('male') }}
                                            </option>

                                            <option value="female"
                                                {{ old('gender', $freelancer->gender->value ?? '') == 'female' ? 'selected' : '' }}>
                                                {{ __('female') }}
                                            </option>

                                            <option value="prefer_not_say"
                                                {{ old('gender', $freelancer->gender->value ?? '') == 'prefer_not_say' ? 'selected' : '' }}>
                                                {{ __('prefer_not_say') }}
                                            </option>
                                        </select>
                                    </div>


                                    <div>
                                        <label class="font-semibold">{{ __('profession') }}</label>
                                        <select name="profession_id" id="profession_id"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                            @foreach ($professions as $profession)
                                                <option value="{{ $profession->id }}"
                                                    {{ old('profession_id', $freelancer->profession_id) == $profession->id ? 'selected' : '' }}>
                                                    {{ $profession->translations->firstWhere('language', app()->getLocale())?->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- bio -->
                                <div class="mt-6">
                                    <label class="font-semibold">{{ __('bio') }}</label>
                                    <textarea name="bio" id="bio" rows="4"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('bio', $freelancer->freelancer->translation?->bio) }}</textarea>
                                </div>

                                <!-- باقي الحقول -->
                                <div class="mt-6">
                                    <label class="font-semibold">{{ __('country') }}</label>
                                    <select name="country_id" id="country_id"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country['id'] }}"
                                                {{ old('country_id', $freelancer->country_id) == $country['id'] ? 'selected' : '' }}>
                                                {{ $country['title'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-6">
                                    <label class="font-semibold">{{ __('languages') }}</label>
                                    <select name="languages[]" id="languages"
                                        class="js-example-basic-multiple mt-1 block w-full rounded-lg border-gray-300"
                                        multiple>
                                        @foreach ($languages as $language)
                                            <option value="{{ $language->id }}"
                                                {{ in_array($language->id, old('languages', $freelancer->languages->pluck('language_id')->toArray())) ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $language['title_ar'] : $language['title_en'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>




                                <div class="mt-6">
                                    <label class="font-semibold">{{ __('categories') }}</label>
                                    <select name="category_ids[]" id="categories"
                                        class="js-example-basic-multiple mt-1 block w-full rounded-lg border-gray-300"
                                        multiple>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ in_array($category->id, old('category_ids', $freelancer->categories->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                                {{ $category->translation->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- الشهادات -->
                                <div class="mt-6 p-4 border rounded-lg bg-gray-50">
                                    <label class="block text-sm font-medium mb-2">{{ __('certificates') }}</label>
                                    <div id="certificate-wrapper" class="space-y-4">

                                        {{-- عرض الشهادات المحفوظة سابقاً --}}
                                        @foreach ($freelancer->certificates as $certificate)
                                            <div class="certificate-group grid grid-cols-4 gap-4 items-center"
                                                data-id="{{ $certificate->id }}">
                                                <a href="{{ asset($certificate->file_path) }}" target="_blank"
                                                    class="text-blue-600 underline truncate">
                                                    {{ __('view_certificate') }}
                                                </a>
                                                <input type="text" name="old_description[{{ $certificate->id }}]"
                                                    value="{{ old('old_description.' . $certificate->id, $certificate->translation->description) }}"
                                                    placeholder="{{ __('certificate_description') }}"
                                                    class="block w-full rounded border-gray-300 col-span-2" />
                                                <button type="button"
                                                    class="delete-old-certificate-btn p-2 text-white bg-red-600 rounded text-sm flex items-center justify-center w-8 h-8"
                                                    data-id="{{ $certificate->id }}" title="{{ __('delete') }}">
                                                    <!-- أيقونة حذف (Trash SVG) -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                            </div>
                                        @endforeach

                                        @php
                                            $oldFiles = old('file', []);
                                            $oldDescriptions = old('description', []);
                                        @endphp
                                        @foreach ($oldDescriptions as $index => $oldDesc)
                                            <div class="certificate-group grid grid-cols-2 gap-4">
                                                <input type="file" name="file[]"
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded" />
                                                <input type="text" name="description[]" value="{{ $oldDesc }}"
                                                    placeholder="{{ __('certificate_description') }}"
                                                    class="block w-full rounded border-gray-300" />
                                            </div>
                                        @endforeach

                                        {{-- لو ما فيه old inputs جديده اضف حقل واحد افتراضي --}}
                                        @if (count($oldDescriptions) === 0)
                                            <div class="certificate-group grid grid-cols-2 gap-4">
                                                <input type="file" name="file[]"
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded" />
                                                <input type="text" name="description[]"
                                                    placeholder="{{ __('certificate_description') }}"
                                                    class="block w-full rounded border-gray-300" />
                                            </div>
                                        @endif

                                    </div>

                                    <button type="button" id="add-certificate-btn"
                                        class="mt-3 px-4 py-1 bg-secondary text-sm rounded text-white">
                                        {{ __('add_more_certificates') }}
                                    </button>
                                </div>

                                <!-- زر الحفظ -->
                                <div class="mt-6 text-center">
                                    <button type="submit"
                                        class="gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                        <i class="ti ti-check me-1"></i> {{ __('save_changes') }}
                                    </button>
                                    <a href="{{ route('freelancer.profile.show') }}"
                                        class="ms-4 gap-2 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg shadow">
                                        {{ __('cancel') }}
                                    </a>
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
    {!! JsValidator::formRequest('App\\Http\\Requests\\Freelancer\\FreelancerRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "{{ __('select_options') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#country_id').select2({
                placeholder: "{{ __('select_country') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#prefix').select2({
                placeholder: "{{ __('select_prefix') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#add-certificate-btn').on('click', function(e) {
                e.preventDefault();

                const certificateGroup = `
                <div class="certificate-group grid grid-cols-12 gap-4 mt-2">
                    <div class="col-span-12 md:col-span-6">
                        <input type="file" name="file[]" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <input type="text" name="description[]" placeholder="{{ __('certificate_description') }}"
                            class="block w-full rounded-lg border-gray-300">
                    </div>
                </div>
            `;

                $('#certificate-wrapper').append(certificateGroup);
            });
        });
    </script>

    <script>
        document.querySelectorAll('.delete-old-certificate-btn').forEach(button => {
            button.addEventListener('click', function() {
                let certId = this.dataset.id;
                if (confirm('{{ __('are_you_sure_delete_certificate') }}')) {
                    fetch('{{ route('freelancer.profile.deleteCertificate', ':id') }}'.replace(':id',
                            certId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert('{{ __('certificate_deleted_successfully') }}');
                            this.closest('.certificate-group').remove();
                        })
                        .catch(error => {
                            console.error(error);
                            alert('{{ __('something_went_wrong') }}');
                        });
                }
            });
        });
    </script>
@endpush
