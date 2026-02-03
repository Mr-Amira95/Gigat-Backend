@extends('layouts.auth')

@section('title', 'Gigat Platform | Login')

@section('content')
    <div class="w-full max-w-md relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-4 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                {{-- Replace with real logo --}}
                <img src="{{ asset($logo ?? 'images/logo.png') }}" alt="Gigat" class="w-20 h-12 object-contain">
            </div>
        </div>

        {{-- Card --}}
        <div class="glass-effect rounded-2xl p-8 shadow-2xl fade-in" style="animation-delay:0.2s;">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('sign_in') }}</h2>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                @csrf

                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>{{ __('email') }}
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 @error('email') border-red-500 @enderror"
                        placeholder="{{ __('email') }}" required autocomplete="username">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>{{ __('password') }}
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 pr-12 @error('password') border-red-500 @enderror"
                            placeholder="{{ __('password') }}" required autocomplete="current-password">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Forgot --}}
                {{-- <div class="flex items-center justify-end">
                    <a href="{{ route('forgot.form') }}"
                        class="text-sm text-purple-600 hover:text-purple-800 transition-colors duration-300">
                        {{ __('forgot_password') }}
                    </a>
                </div> --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('forgot.form') }}"
                        class="text-sm text-purple-600 hover:text-purple-800 transition-colors duration-300">
                        {{ __('forgot_password') }} </a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>{{ __('sign_in') }}
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\LoginRequest') !!}

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPwd);
            icon.classList.toggle('fa-eye-slash', isPwd);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            if (!form) return;
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('sign_in') }}…';
                    btn.disabled = true;
                }
            });
        });
    </script>
@endpush
