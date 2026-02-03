@extends('layouts.auth')

@section('title', 'Gigat Platform | Reset Password')

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
                <p class="text-2xl font-bold text-gray-800 mb-2">{{ __('Reset Password') }}</p>
                <p class="text-base text-gray-500">{{ __('Enter your new password below.') }}</p>
            </div>

            <form method="POST" action="{{ route('reset.submit') }}" class="space-y-6">
                @csrf

                {{-- New Password --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>{{ __('New Password') }}
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 pr-12 @error('password') border-red-500 @enderror"
                            placeholder="••••••••" required autocomplete="new-password">
                        <button type="button" onclick="togglePwd('password','toggleIconPwd')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIconPwd"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-check-double mr-2 text-purple-600"></i>{{ __('Confirm Password') }}
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus transition-all duration-300 pr-12"
                            placeholder="••••••••" required autocomplete="new-password">
                        <button type="button" onclick="togglePwd('password_confirmation','toggleIconConfirm')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" id="resetBtn"
                    class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200">
                    <i class="fas fa-undo mr-2"></i>{{ __('Reset Password') }}
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
        function togglePwd(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPwd);
            icon.classList.toggle('fa-eye-slash', isPwd);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            if (!form) return;
            form.addEventListener('submit', function() {
                const btn = document.getElementById('resetBtn');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Resetting…') }}';
                    btn.disabled = true;
                    btn.classList.add('opacity-75');
                }
            });
        });
    </script>
@endpush
