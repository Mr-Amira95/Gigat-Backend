@extends('layouts.auth')

@section('title', 'Gigat Platform | Login')

@push('styles')
    {{-- Keep CSRF meta in the <head> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('sign_in') }}</h2>
                <p class="text-base text-gray-500">{{ __('welcome_back') }}</p>
            </div>

            <form id="login-form" method="POST" action="{{ route('freelancer.login.submit') }}" class="space-y-6">
                @csrf
                <input type="hidden" id="player_id" name="player_id">
                <input type="hidden" id="platform" name="platform" value="web">

                {{-- Phone (prefix + number) --}}
                <div class="space-y-2">
                    <label for="signin-phone" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-phone mr-2 text-purple-600"></i>{{ __('phone') }}
                    </label>
                    <div class="flex gap-2">
                        <select id="prefixLogin" name="prefix" class="form-select" style="width:7rem">
                            @foreach ($countries as $country)
                                <option value="{{ $country->phone_code }}"
                                    {{ old('prefix') == $country->phone_code ? 'selected' : '' }}>
                                    {{ $country->phone_code }}
                                </option>
                            @endforeach
                        </select>

                        <input type="tel" id="signin-phone" name="phone" value="{{ old('phone') }}"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('phone') border-red-500 @enderror"
                            placeholder="{{ __('phone') }}" required>
                    </div>
                    @error('prefix')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label for="signin-password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>{{ __('password') }}
                    </label>
                    <div class="relative">
                        <input type="password" id="signin-password" name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus pr-12 @error('password') border-red-500 @enderror"
                            placeholder="{{ __('password') }}" required autocomplete="current-password">
                        <button type="button" onclick="togglePwd('signin-password','toggleIconPwd')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIconPwd"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    {{-- <div class="text-right">
                        <a href="{{ route('forgot.form') }}" class="text-sm text-purple-600 hover:text-purple-800">
                            {{ __('forgot_password') }}
                        </a>
                    </div> --}}
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>{{ __('sign_in') }}
                </button>

                {{-- Google Sign-in --}}
                <a href="{{ route('auth.google') }}"
                    class="w-full text-white font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-2 "
                    style="background:#DB4437">
                    <i class="fab fa-google text-lg"></i>{{ __('sign_in_with_google') }}
                </a>

                {{-- Signup link --}}
                <p class="text-center text-sm text-gray-600">
                    {{ __('dont_have_account') }}
                    <a href="{{ route('freelancer.register') }}" class="text-purple-700 hover:text-purple-900 font-medium">
                        {{ __('sign_up') }}
                    </a>
                </p>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- OneSignal SDK --}}
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

    <script>
        function togglePwd(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPwd);
            icon.classList.toggle('fa-eye-slash', isPwd);
        }

        // loading state
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('login-form');
            if (!form) return;
            form.addEventListener('submit', function() {
                const btns = this.querySelectorAll('button[type="submit"], a[role="button"]');
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('sign_in') }}…';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75');
                }
            });
        });

        // OneSignal init + capture player_id
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            await OneSignal.init({
                appId: "7ab59a87-79f3-46e8-af69-673331be40cc",
                onesignalId: "7ab59a87-79f3-46e8-af69-673331be40cc",
                allowLocalhostAsSecureOrigin: true,
                serviceWorkerPath: '/OneSignalSDKWorker.js'
            });

            OneSignal.Notifications.setDefaultTitle("GIGAT");

            if (Notification.permission !== 'granted') {
                await OneSignal.Notifications.requestPermission();
            }

            let playerId = await OneSignal.User.PushSubscription.id;
            if (playerId) {
                document.getElementById("player_id").value = playerId;
            } else {
                OneSignal.User.addEventListener('subscriptionChange', async () => {
                    const newPlayerId = await OneSignal.User.PushSubscription.id;
                    if (newPlayerId) {
                        document.getElementById("player_id").value = newPlayerId;
                    }
                });
            }
        });

        // Optional: handle ?token= SSO flow
        document.addEventListener("DOMContentLoaded", function() {
            const params = new URLSearchParams(window.location.search);
            const token = params.get('token');
            if (token) {
                fetch("{{ route('freelancer.weblogin') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content"),
                        },
                        body: JSON.stringify({
                            token
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(console.error);
            }
        });
    </script>
@endpush
