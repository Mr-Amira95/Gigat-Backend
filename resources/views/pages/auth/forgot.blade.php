@extends('layouts.auth')

@section('title', 'Gigat Platform | Forgot Password')

@section('content')
    <div class="w-full max-w-md relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-4 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                <img src="{{ asset($logo) }}" alt="logo" class="w-20 h-12 object-contain">
            </div>
        </div>

        {{-- Card --}}
        <div class="glass-effect rounded-2xl p-8 shadow-2xl fade-in" style="animation-delay:0.2s;">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                    <i class="fas fa-key text-2xl text-purple-600"></i>
                </div>
                <p class="text-2xl font-bold mb-2 text-gray-800">{{ __('Forgot Password') }}</p>
                {{-- <p class="text-base text-gray-500">{{ __('Enter your email to receive a code') }}</p> --}}
                <p class="text-gray-600 text-sm leading-relaxed">
                    No worries! Enter your email address and we'll send you a secure link to reset your password.
                </p>
            </div>

            <form method="POST" action="{{ route('forgot.submit') }}" class="space-y-6">
                @csrf

                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>{{ __('email') }}
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 @error('email') border-red-500 @enderror"
                        placeholder="admin@example.com" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1 flex items-center">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" id="submitBtn"
                    class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200">
                    <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Code') }}
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-4 text-sm text-gray-500">Or</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <!-- Back to Login Button -->
            <a href="{{ route('login') }}"
                class="w-full btn-secondary font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200 text-center block">
                <i class="fas fa-arrow-left mr-2"></i>Back to Sign In
            </a>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            if (!form) return;
            form.addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Sending…') }}';
                    btn.disabled = true;
                    btn.classList.add('opacity-75');
                }
            });
        });
    </script>
@endpush
