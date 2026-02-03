@extends('layouts.master')
@section('title', __('request_details'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('request_details') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.requests.index') }}">
                            {{ __('requests') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('request_details') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('request_details') }}</h5>
                        </div>
                        <div class="box-body">
                            <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md space-y-6">
                                <h1 class="text-2xl font-bold text-gray-800">{{ __('request_details') }}:
                                    {{ $request->order_number }}</h1>
                                <div class="my-3">
                                    @if ($request->status === 'pending')
                                        <button type="button" onclick="openUpdateModalPending('in_progress')"
                                            class="flex items-center gap-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Approve Request
                                        </button>

                                        {{-- model for pending --}}
                                        <div id="update-modal-pending"
                                            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <div class="bg-white p-6 rounded w-96 relative">
                                                <button type="button" onclick="closeUpdateModalPending()"
                                                    class="absolute top-2 right-2 text-gray-600 hover:text-gray-900"
                                                    style="position: absolute; top: 0.5rem; right: 0.5rem;">×</button>

                                                <h3 class="text-lg font-semibold mb-4">Add Comment & Attachment</h3>

                                                <form id="update-form"
                                                    action="{{ route('freelancer.requests.changeStatus', ['id' => $request->id]) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="status" id="status-input-pending"
                                                        value="">
                                                    <div class="mb-4">
                                                        <label for="comment" class="block mb-1 font-medium">Comment</label>
                                                        <textarea name="comment" id="comment" rows="3" class="w-full border rounded p-2" required></textarea>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="attachment" class="block mb-1 font-medium">Attachment
                                                        </label>
                                                        <input type="file" name="attachment" id="attachment"
                                                            class="w-full" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                    </div>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-blue-600 text-white rounded">Submit
                                                        Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif ($request->status === 'in_progress')
                                        <div class="flex flex-wrap items-center gap-3 mt-4">
                                            <!-- Complete Request Form -->
                                            <button type="button" onclick="openUpdateModalProgress('completed')"
                                                class="flex items-center gap-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded shadow transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Complete Request
                                            </button>

                                            <!-- Modal for change status -->
                                            <div id="update-modal-progress"
                                                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                                <div class="bg-white p-6 rounded w-96 relative">
                                                    <button type="button" onclick="closeUpdateModalProgress()"
                                                        class="absolute top-2 right-2 text-gray-600 hover:text-gray-900"
                                                        style="position: absolute; top: 0.5rem; right: 0.5rem;">×</button>

                                                    <h3 class="text-lg font-semibold mb-4">Add Comment & Attachment</h3>

                                                    <form id="update-form"
                                                        action="{{ route('freelancer.requests.changeStatus', ['id' => $request->id]) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf

                                                        <input type="hidden" name="status" id="status-input-progress"
                                                            value="">
                                                        <div class="mb-4">
                                                            <label for="comment"
                                                                class="block mb-1 font-medium">Comment</label>
                                                            <textarea name="comment" id="comment" rows="3" class="w-full border rounded p-2" required></textarea>
                                                        </div>
                                                        <div class="mb-4">
                                                            <label for="attachment"
                                                                class="block mb-1 font-medium">Attachment
                                                            </label>
                                                            <input type="file" name="attachment" id="attachment"
                                                                class="w-full" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                                required>
                                                        </div>
                                                        <button type="submit"
                                                            class="px-4 py-2 bg-blue-600 text-white rounded">Submit
                                                            Update</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Modal for add update-->
                                            <div id="update-modal-add-update"
                                                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                                <div class="bg-white p-6 rounded w-96 relative">
                                                    <button type="button"
                                                        onclick="document.getElementById('update-modal-add-update').classList.add('hidden')"
                                                        class="absolute top-2 right-2 text-gray-600 hover:text-gray-900"
                                                        style="position: absolute; top: 0.5rem; right: 0.5rem;">×</button>


                                                    <h3 class="text-lg font-semibold mb-4">Add Comment & Attachment
                                                    </h3>

                                                    <form
                                                        action="{{ route('freelancer.requests.addUpdate', $request->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="mb-4">
                                                            <label for="comment"
                                                                class="block mb-1 font-medium">Comment</label>
                                                            <textarea name="comment" id="comment" rows="3" class="w-full border rounded p-2" required></textarea>
                                                        </div>
                                                        <div class="mb-4">
                                                            <label for="attachment"
                                                                class="block mb-1 font-medium">Attachment
                                                                (optional)</label>
                                                            <input type="file" name="attachment" id="attachment"
                                                                class="w-full" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                        </div>
                                                        <button type="submit"
                                                            class="px-4 py-2 bg-blue-600 text-white rounded">Submit
                                                            Update</button>
                                                    </form>
                                                </div>
                                            </div>


                                            <!-- Add Update Button -->
                                            <button type="button"
                                                onclick="document.getElementById('update-modal-add-update').classList.remove('hidden')"
                                                class="flex items-center gap-1 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded shadow transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Update
                                            </button>




                                            <!-- Download Contract Button -->
                                            <a href="{{ route('freelancer.requests.downloadContract', $request->id) }}"
                                                class="flex items-center gap-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 4v16h16V4H4zm4 8h8m-8 4h4" />
                                                </svg>
                                                Download Contract
                                            </a>
                                        </div>
                                    @elseif($request->status !== 'cancelled')
                                        <!-- Download Contract Button -->
                                        {{-- @dd($request->contract_path) --}}
                                        <div class="flex flex-wrap items-center gap-3 mt-4">
                                            <a href="{{ route('freelancer.requests.downloadContract', $request->id) }}"
                                                class="flex items-center gap-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 4v16h16V4H4zm4 8h8m-8 4h4" />
                                                </svg>
                                                Download Contract
                                            </a>
                                        </div>
                                    @endif
                                </div>




                                {{-- Order Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('request_details') }}</h2>
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-6">
                                            <div class="my-3"><strong>{{ __('title') }}:</strong>
                                                {{ $request->translation->title }}
                                            </div>
                                            <div class="my-3"><strong>{{ __('status') }}:</strong>
                                                {{-- {{ ucfirst($request->status) }} --}}
                                                @if (isset($request->status) && $request->status !== '')
                                                    <span
                                                        class="{{ \App\Enums\RequestStatusEnum::from($request->status)->badge() }}">
                                                        {{ \App\Enums\RequestStatusEnum::from($request->status)->label() }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="my-3"><strong>{{ __('start_date') }}:</strong>
                                                {{ $request->start_date ?? 'N/A' }}</div>
                                            <div class="my-3"><strong>{{ __('end_date') }}:</strong>
                                                {{ $request->end_date }}</div>
                                            {{-- <div class="my-3">
                                                @if (isset($request->status) && $request->status !== '')
                                                    <span
                                                        class="{{ \App\Enums\RequestStatusEnum::from($request->status)->badge() }}">
                                                        {{ \App\Enums\RequestStatusEnum::from($request->status)->label() }}
                                                    </span>
                                                @endif

                                            </div> --}}

                                            {{-- Status Change Actions --}}




                                        </div>
                                        <div class="col-span-6"><strong>{{ __('image') }}:</strong><br>
                                            <img src="{{ asset($request->image) }}" alt="Service Image"
                                                class="mt-2 h-32 w-full object-cover rounded-md">
                                        </div>
                                    </div>
                                </div>

                                {{-- User Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">User Information</h2>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><strong>{{ __('username') }}:</strong> {{ $request->user->username }}</div>
                                        <div><strong>{{ __('email') }}:</strong> {{ $request->user->email }}</div>
                                        <div><strong>{{ __('phone') }}:</strong> {{ $request->user->full_phone }}</div>
                                        <div><strong>{{ __('gender') }}:</strong> {{ $request->user->gender_label }}
                                        </div>
                                        <div><strong>{{ __('avatar') }}:</strong><br>
                                            <img src="{{ asset($request->user->avatar) }}" alt="Avatar"
                                                class="mt-2 h-24 w-24 rounded-full object-cover">
                                        </div>
                                        <div><strong>{{ __('languages') }}:</strong><br>
                                            @foreach ($request->user->languages as $lang)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 bg-gray-100 text-sm rounded-md">
                                                    <img src="{{ $lang->language->flag }}"
                                                        alt="{{ $lang->language->title }}" class="h-4 w-6 mr-1">
                                                    {{ $lang->language->title }} ({{ ucfirst($lang->level) }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Service Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('service_information') }}
                                    </h2>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><strong>{{ __('title') }}:</strong>
                                            {{ $request->service->translation->title }}</div>
                                        <div class="col-span-2"><strong>{{ __('description') }}:</strong>
                                            {{ $request->service->translation->description }}</div>
                                    </div>
                                </div>

                                {{-- Plan Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('plan_information') }}</h2>
                                    <div><strong>{{ __('plan') }}:</strong> {{ $request->plan->translation->title }}
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        {{-- @foreach ($request->plan->features as $feature) --}}
                                        @foreach ($request->features as $feature)
                                            <div class="flex justify-between bg-gray-200 px-4 py-2 rounded">
                                                <span>{{ $feature->translation->title }}</span>
                                                @if ($feature->translation->title == 'Price')
                                                    <span class="font-medium"> {{ $currencySymbol }}
                                                        {{ \App\Utilities\CurrencyConverter::convert($feature->value, 'USD', $currentCurrency) }}
                                                    </span>
                                                @else
                                                    {{-- {{ dd($feature->translation->title); }} --}}
                                                    <span class="font-medium">{{ $feature->value }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Logs --}}
                                <div class="mt-4 text-right">
                                    <a href="{{ route('freelancer.requests.logs', $request->id) }}"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded">
                                        See Logs History
                                    </a>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // فتح مودال الـ pending مع تعيين الحالة
        function openUpdateModalPending(newStatus) {
            document.getElementById('status-input-pending').value = newStatus;
            document.getElementById('update-modal-pending').classList.remove('hidden');
        }

        // إغلاق مودال الـ pending ومسح الحقول
        function closeUpdateModalPending() {
            document.getElementById('update-modal-pending').classList.add('hidden');
            document.getElementById('comment-pending').value = '';
            document.getElementById('attachment-pending').value = '';
        }

        // فتح مودال الـ progress مع تعيين الحالة
        function openUpdateModalProgress(newStatus) {
            document.getElementById('status-input-progress').value = newStatus;
            document.getElementById('update-modal-progress').classList.remove('hidden');
        }

        // إغلاق مودال الـ progress ومسح الحقول
        function closeUpdateModalProgress() {
            document.getElementById('update-modal-progress').classList.add('hidden');
            document.getElementById('comment-progress').value = '';
            document.getElementById('attachment-progress').value = '';
        }
    </script>
@endpush
