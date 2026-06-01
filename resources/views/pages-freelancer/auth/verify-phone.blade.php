@extends('layouts.auth')
@section('title', __('verify_your_phone'))

@section('content')
  <div class="w-full max-w-md relative z-10">
    {{-- Logo (optional – remove if not needed) --}}
    <div class="text-center mb-4 fade-in">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
        <img src="{{ asset($logo ?? 'images/logo.png') }}" alt="Gigat" class="w-20 h-12 object-contain">
      </div>
    </div>

    {{-- Card --}}
    <div class="glass-effect rounded-2xl p-8 shadow-2xl fade-in" style="animation-delay:.2s;">
      <div class="text-center mb-2">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('verify_your_phone') }}</h2>
        <p class="text-gray-500 text-sm mt-1">
          {{ __('Enter the 6-digit code we sent to your phone.') }}
        </p>
      </div>

      {{-- Verify form --}}
      <form method="POST" action="{{ route('freelancer.verify.phone.submit') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="player_id" id="player_id">
        <input type="hidden" name="platform" id="platform" value="web">
        <input type="hidden" name="prefix" value="{{ $prefix }}">
        <input type="hidden" name="phone"  value="{{ $phone }}">

        <div>
          <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-shield-alt mr-2 text-purple-600"></i>{{ __('verification_code') }}
          </label>
          <input
            type="text"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="6"
            id="code"
            name="code"
            placeholder="••••••"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('code') border-red-500 @enderror"
            required
            autocomplete="one-time-code"
          >
          @error('code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit"
          class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200"
          id="verifyBtn">
          <i class="fas fa-check-circle mr-2"></i>{{ __('verify') }}
        </button>
      </form>

      {{-- Divider + Resend --}}
      <div class="my-6 flex items-center">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-4 text-sm text-gray-500">{{ __('Or') }}</span>
        <div class="flex-1 border-t border-gray-300"></div>
      </div>

      <form method="POST" action="{{ route('freelancer.resend.phone.code') }}" class="text-center">
        @csrf
        <input type="hidden" name="prefix" value="{{ $prefix }}">
        <input type="hidden" name="phone"  value="{{ $phone }}">
        <button type="submit" class="text-purple-700 hover:text-purple-900 font-medium underline">
          <i class="fas fa-paper-plane mr-1"></i>{{ __('resend_code') }}
        </button>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Disable button on submit for nicer UX --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('form[action="{{ route('freelancer.verify.phone.submit') }}"]');
      if (form) {
        form.addEventListener('submit', function () {
          const btn = document.getElementById('verifyBtn');
          if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('verify') }}…';
            btn.disabled = true;
          }
        });
      }
    });
  </script>

  {{-- OneSignal (unchanged) --}}
  <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
  <script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
      await OneSignal.init({
        appId: "{{ config('onesignal.app_id') }}",
        onesignalId: "{{ config('onesignal.app_id') }}",
        allowLocalhostAsSecureOrigin: true,
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
          const newId = await OneSignal.User.PushSubscription.id;
          if (newId) document.getElementById("player_id").value = newId;
        });
      }
    });
  </script>
@endpush
