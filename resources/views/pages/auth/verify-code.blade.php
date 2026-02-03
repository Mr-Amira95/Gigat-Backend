@extends('layouts.auth')

@section('title', 'Gigat Platform | Verify Code')

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
                <p class="text-2xl font-bold text-gray-800 mb-2">{{ __('Verify Code') }}</p>
                <p class="text-base text-gray-500">{{ __('Enter the 6-digit code sent to your email.') }}</p>
            </div>

            <form method="POST" action="{{ route('verify.code.submit') }}" class="space-y-6">
                @csrf

                {{-- Code --}}
                <div class="space-y-2">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-shield-alt mr-2 text-purple-600"></i>{{ __('Verification Code') }}
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" inputmode="numeric"
                        pattern="[0-9]*" maxlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 @error('code') border-red-500 @enderror"
                        placeholder="123456" required autofocus>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" id="verifyBtn"
                    class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200">
                    <i class="fas fa-check mr-2"></i>{{ __('Verify Code') }}
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
                const btn = document.getElementById('verifyBtn');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Verifying…') }}';
                    btn.disabled = true;
                    btn.classList.add('opacity-75');
                }
            });
        });
    </script>
@endpush
