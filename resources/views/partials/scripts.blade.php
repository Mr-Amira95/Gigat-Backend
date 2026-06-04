<!-- SCRIPTS -->
<!-- SCROLL-TO-TOP -->
<div class="scrollToTop">
    <span class="arrow"><i class="ri-arrow-up-s-fill text-xl"></i></span>
</div>
<div id="responsive-overlay"></div>

<!-- jquery — SEC-19: replaced cdn-script.com (unverified) with official jQuery CDN + SRI hash -->
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>


<!-- POPPER JS -->
<script src="{{ asset('build/assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

<!-- NODE WAVES JS -->
<script src="{{ asset('build/assets/libs/node-waves/waves.min.js') }}"></script>

<!-- COLOR PICKER JS -->
<script src="{{ asset('build/assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

<!-- SWITCH JS -->
<script src="{{ asset('build/assets/switch.js') }}"></script>

<!-- PRELINE JS -->
<script src="{{ asset('build/assets/libs/preline/preline.js') }}"></script>

<!-- SIMPLEBAR JS -->
<script src="{{ asset('build/assets/libs/simplebar/simplebar.min.js') }}"></script>

<!-- STICKY JS -->
<script src="{{ asset('build/assets/sticky.js') }}"></script>

<!-- APP JS -->
<link rel="modulepreload" href="{{ asset('build/assets/app-23e8aa1f.js') }}" />
<script type="module" src="{{ asset('build/assets/app-23e8aa1f.js') }}"></script>

<!-- CUSTOM-SWITCHER JS -->
<link rel="modulepreload" href="{{ asset('build/assets/custom-switcher-c2a0a9d1.js') }}" />
<script type="module" src="{{ asset('build/assets/custom-switcher-c2a0a9d1.js') }}"></script>

<!-- TOASTR-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


<!-- jsvalidation -->
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    @if (Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif
    @if (Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif
    @if (Session::has('warning'))
        toastr.warning("{{ Session::get('warning') }}");
    @endif
    @if (Session::has('info'))
        toastr.info("{{ Session::get('info') }}");
    @endif
</script>
<script>
    $(document).ready(function() {
        // Set RTL/LTR based on current locale
        @if (app()->getLocale() == 'ar')
            localStorage.setItem('ynexrtl', 'true');
            localStorage.removeItem('ynexltr');
            $('html').attr('dir', 'rtl');
        @else
            localStorage.setItem('ynexltr', 'true');
            localStorage.removeItem('ynexrtl');
            $('html').attr('dir', 'ltr');
        @endif

        // Handle language change
        $('.language-switcher a, .ti-dropdown-item').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var isArabic = url.includes('locale/ar');

            if (isArabic) {
                localStorage.setItem('ynexrtl', 'true');
                localStorage.removeItem('ynexltr');
            } else {
                localStorage.setItem('ynexltr', 'true');
                localStorage.removeItem('ynexrtl');
            }

            window.location.href = url;
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".logout-btn").addEventListener("click", function(e) {
            e.preventDefault(); // Prevent default only if necessary
            document.getElementById("logout-form").submit();
        });
    });
</script>
<script>
    function showDeleteConfirmation(message, itemId) {
        Swal.fire({
            title: message,
            text: "{{ __('you_wont_be_able_to_revert_this') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('yes_delete_it') }}",
            cancelButtonText: "{{ __('no_cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteItem(itemId);
            }
        });
    }

    function deleteItem(itemId) {
        document.getElementById('delete-form-' + itemId).submit();
    }
</script>
<script>
    $(document).ready(function() {
        $(document).on('change', 'input[type="checkbox"].ti-switch', function() {
            var itemId = $(this).data('item-id');
            var checked = $(this).is(':checked');
            var route = $(this).data('route');
            $.ajax({
                url: route,
                type: 'POST',
                data: {
                    id: itemId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('success') }}',
                        text: '{{ __('status_updated_successfully') }}',
                    });
                },
                error: function(xhr) {
                    console.log(xhr)
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Oops') }}',
                        text: xhr.responseJSON.message || 'Something went wrong!',
                    });
                    $('input[type="checkbox"].ti-switch[data-item-id="' + itemId + '"]')
                        .prop('checked', true);
                }
            });
        });
    });
</script>

<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

<script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
        // Initialize OneSignal
        await OneSignal.init({
            appId: "{{ config('onesignal.app_id') }}",
            onesignalId: "{{ config('onesignal.app_id') }}",
            // Optional: allow localhost for testing
            allowLocalhostAsSecureOrigin: true,
        });
        // var notificationUrl = "{{ url('/freelancer/notification') }}";
        // OneSignal.Notifications.setDefaultUrl(notificationUrl);

        OneSignal.Notifications.setDefaultTitle("GIGAT");
        console.log("Notification permission status:", Notification.permission);

        // Request push permission if not already granted
        if (Notification.permission !== 'granted') {
            await OneSignal.Notifications.requestPermission();
        }


        let playerId = await OneSignal.User.PushSubscription.id;
        if (playerId) {
            console.log("User is subscribed, player ID:", playerId);
            document.getElementById("player_id").value = playerId;

        } else {
            console.log("Notifications permission not granted or no player ID yet.");

            OneSignal.User.addEventListener('subscriptionChange', async (event) => {
                const newPlayerId = await OneSignal.User.PushSubscription.id;
                console.log("Subscription changed — new Player ID:", newPlayerId);

                if (newPlayerId) {
                    document.getElementById("player_id").value = newPlayerId;
                }
            });

        }
        console.log("User's OneSignal Player ID:", playerId);
    });
</script>

<!-- END SCRIPTS -->
