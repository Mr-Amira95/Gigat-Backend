@extends('layouts.master')
@section('title', __('edit_release'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('edit_release') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('admin.releases.index') }}">
                            {{ __('releases') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('edit_release') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('edit_release') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('admin.releases.update', $release->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-12 gap-4">

                                    <div class="col-span-12 md:col-span-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('android_version') }}</label>
                                        <input type="text" name="android_version"
                                            value="{{ old('android_version', $release->android_version) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    <div class="col-span-12 md:col-span-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('ios_version') }}</label>
                                        <input type="text" name="ios_version"
                                            value="{{ old('ios_version', $release->ios_version) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    <div class="col-span-12 md:col-span-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('web_version') }}</label>
                                        <input type="text" name="web_version"
                                            value="{{ old('web_version', $release->web_version) }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>

                                    {{-- <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('release_note') }}</label>
                                        <textarea name="release_note" id="release_note"
                                            class="summernote mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('release_note', $release->release_note) }}</textarea>
                                        @error('release_note')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div> --}}
                                    <!-- English Note -->
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('release_note_en') }}</label>
                                        <textarea name="release_note_en" id="release_note_en"
                                            class="summernote mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">{{ old('release_note_en', $release->translations->firstWhere('language', 'en')?->release_note) }}</textarea>
                                        @error('release_note_en')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Arabic Note -->
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('release_note_ar') }}</label>
                                        <textarea name="release_note_ar" id="release_note_ar"
                                            class="summernote mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                                            dir="rtl">{{ old('release_note_ar', $release->translations->firstWhere('language', 'ar')?->release_note) }}</textarea>
                                        @error('release_note_ar')
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <div class="flex items-center space-x-2 mt-4">
                                            <input type="hidden" name="is_required" value="0">

                                            <input type="checkbox" name="is_required" value="1" id="is_required"
                                                class="h-4 w-4 border-gray-300 rounded text-primary focus:ring-primary"
                                                {{ old('is_required', $release->is_required) ? 'checked' : '' }}>
                                            <label for="is_required"
                                                class="block text-sm font-medium text-gray-700">{{ __('is_required') }}</label>
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-6 flex justify-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('update') }}
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\ReleaseRequest') !!}

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
    {{-- <script>
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
                    onChange: function(contents) {
                        $('#release_note').val(contents);
                        $('form').validate().element('#release_note');
                    }
                }
            });
        });
    </script> --}}
@endpush
