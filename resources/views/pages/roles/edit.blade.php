@extends('layouts.master')
@section('title', __('edit_role'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium">
                        {{ __('edit_role') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap min-w-0">
                    <li class="text-sm">
                        <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate"
                            href="{{ route('roles.index') }}">
                            {{ __('roles') }}
                            <i class="ti ti-chevrons-right px-[0.5rem]"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                        {{ __('edit_role') }}
                    </li>
                </ol>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Page Header Close -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-body">
                            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            {{ __('role_name') }}
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            value="{{ old('name', $role->name) }}" required
                                            {{ $role->name === 'super_admin' ? 'readonly' : '' }}>
                                    </div>

                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('permissions') }}
                                        </label>

                                        @foreach ($permissions as $section => $sectionPermissions)
                                            <div class="mb-4 border-b pb-2">
                                                <h3 class="text-base font-semibold text-gray-800 mb-3">{{ __($section) }}
                                                </h3>

                                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                    @foreach ($sectionPermissions as $permission)
                                                        <div class="flex items-center">
                                                            <input type="checkbox" name="permissions[]"
                                                                id="permission_{{ $permission->id }}"
                                                                value="{{ $permission->name }}"
                                                                @if (in_array($permission->id, $rolePermissions)) checked @endif
                                                                class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 permission-checkbox permission-{{ implode('_', array_slice(explode('_', $permission->name), 1)) }}"
                                                                data-permission="{{ $permission->name }}"> <label
                                                                for="permission_{{ $permission->id }}"
                                                                class="mx-2 text-sm text-gray-700">
                                                                {{ app()->getLocale() == 'ar' ? $permission->description_ar : $permission->description_en }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\RoleRequest') !!}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');

            // لما يصير تغيير على أي checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const permissionName = this.dataset.permission;
                    const parts = permissionName.split('_');
                    const action = parts[0]; // view, create, edit...
                    const category = parts.slice(1).join('_'); // categories, services, ...

                    if (action === 'view') {
                        // كل checkboxes لها نفس الـ category
                        const relatedCheckboxes = document.querySelectorAll(
                            `.permission-${category}`);
                        relatedCheckboxes.forEach(cb => {
                            if (cb.dataset.permission !== permissionName) {
                                cb.disabled = !this.checked;
                                if (!this.checked) {
                                    cb.checked = false;
                                }
                            }
                        });
                    }
                });
            });

            // trigger مرة واحدة عند التحميل حتى يضبط الحالة الافتراضية
            checkboxes.forEach(cb => cb.dispatchEvent(new Event('change')));
        });
    </script>
@endpush
