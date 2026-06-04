@extends('layouts.master')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        #local-player,
        #remote-playerlist {
            display: none;
        }

        .remote-player {
            height: 400px;
            width: 288px;
            background-color: black;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        #local-player {
            height: 400px;
        }

        .remote-screen {
            width: 100%;
            height: 80vh;
            /* or full height */
            background-color: black;
        }
    </style>
@endpush
@section('content')
    <div class="content">
        <div class="main-content bg-gray-900 text-white min-h-screen flex flex-col items-center justify-center p-6">

            <h2 class="text-2xl font-bold mb-6">{{ __('call.call_with') }} {{ $receiver->username ?? __('call.peer_left') }}
            </h2>

            <div class="flex flex-wrap justify-center gap-4 mb-6">
                <div id="local-player" class="relative w-72 h-56 bg-black rounded-xl overflow-hidden">
                </div>

                <div id="remote-playerlist" class="relative w-72 h-56 bg-black rounded-xl overflow-hidden"></div>
                {{-- <div id="remote-screen" class="remote-screen hidden"></div> --}}

            </div>

            <p id="status" class="mb-4 text-green-400">{{ __('call.connecting') }}</p>

            <div class="flex gap-6 mt-6 justify-center">
                <!-- End Call -->
                <form action="{{ route('freelancer.call.end', $call->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-xl text-2xl transition duration-300 ease-in-out"
                        title="{{ __('call.end_call') }}">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                </form>

                <!-- Mute Audio -->
                <button id="toggle-audio"
                    class="bg-gray-700 hover:bg-gray-600 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-xl text-2xl transition duration-300 ease-in-out"
                    title="{{ __('call.mute_audio') }}">
                    <i id="audio-icon" class="fas fa-microphone"></i>
                </button>

                <!-- Mute Video -->
                <button id="toggle-video"
                    class="bg-gray-700 hover:bg-gray-600 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-xl text-2xl transition duration-300 ease-in-out"
                    title="{{ __('call.mute_video') }}">
                    <i id="video-icon" class="fas fa-video-slash"></i>
                </button>

                {{-- share button --}}
                <button id="toggle-screen-share"
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-xl text-2xl transition duration-300 ease-in-out"
                    title="Share Screen">
                    <i class="fa fa-desktop"></i>
                </button>
            </div>

        </div>
    </div>

    <script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        const translations = {
            peer_joined: "{{ __('call.peer_joined') }}",
            peer_left: "{{ __('call.peer_left') }}",
            call_connected: "{{ __('call.call_connected') }}",
            call_disconnected: "{{ __('call.call_disconnected') }}"
        };
    </script>



    <script>
        const client = AgoraRTC.createClient({
            mode: "rtc",
            codec: "vp8"
        });

        let localTracks = {
            videoTrack: null,
            audioTrack: null,
        };

        let screenTrack = null;
        let isSharingScreen = false;


        async function startBasicCall() {

            await client.join("{{ $appId }}", "{{ $channelName }}", "{{ $token }}",
                "{{ $uid }}");

            [localTracks.audioTrack, localTracks.videoTrack] = await AgoraRTC.createMicrophoneAndCameraTracks();

            await localTracks.videoTrack.setMuted(true);
            await client.publish([localTracks.audioTrack, localTracks.videoTrack]);

            // [localTracks.audioTrack, localTracks.videoTrack] = await AgoraRTC.createMicrophoneAndCameraTracks();

            // await localTracks.videoTrack.play("local-player");
            // document.getElementById('local-player').style.height = '400px';

            // await client.publish(Object.values(localTracks));
            // console.log("Video track:", localTracks.videoTrack);
            // console.log("Audio track:", localTracks.audioTrack);

        }

        startBasicCall();


        // share screen
        document.getElementById("toggle-screen-share").onclick = async () => {
            if (!isSharingScreen) {
                try {
                    screenTrack = await AgoraRTC.createScreenVideoTrack();

                    await client.unpublish(localTracks.videoTrack);
                    await client.publish(screenTrack);

                    screenTrack.on("track-ended", async () => {
                        await client.unpublish(screenTrack);
                        // if (localTracks.videoTrack && !localTracks.videoTrack.muted) {
                        //     await client.publish(localTracks.videoTrack);
                        // }
                        // await client.publish(localTracks.videoTrack);
                        isSharingScreen = false;
                        alert("Screen sharing ended.");
                    });

                    isSharingScreen = true;
                } catch (err) {
                    // console.error("Screen share error:", err);
                    // alert("Screen sharing permission denied or failed.");
                }
            } else {
                await client.unpublish(screenTrack);
                // await client.publish(localTracks.videoTrack);
                isSharingScreen = false;
            }
            // if (!isSharingScreen) {
            //     screenTrack = await AgoraRTC.createScreenVideoTrack();
            //     await client.unpublish(localTracks.videoTrack);
            //     await client.publish(screenTrack);

            //     // play screen in full width div
            //     const screenDiv = document.getElementById("remote-screen");
            //     if (screenDiv) {
            //         screenTrack.play(screenDiv);
            //         screenDiv.style.display = "block";
            //     }

            //     screenTrack.on("track-ended", async () => {
            //         await client.unpublish(screenTrack);
            //         await client.publish(localTracks.videoTrack);
            //         isSharingScreen = false;
            //         document.getElementById("remote-screen").style.display = "none";
            //     });

            //     isSharingScreen = true;
            // }

        };



        client.on("user-published", async (user, mediaType) => {
            await client.subscribe(user, mediaType);
            console.log("Subscribed to user:", user.uid);

            if (mediaType === 'video') {
                let remoteDiv = document.getElementById(`remote-player-${user.uid}`);

                if (!remoteDiv) {
                    remoteDiv = document.createElement("div");
                    remoteDiv.id = `remote-player-${user.uid}`;
                    remoteDiv.className = 'remote-player';
                    document.getElementById("remote-playerlist").appendChild(remoteDiv);
                }

                user.videoTrack.play(remoteDiv);

                document.getElementById("remote-playerlist").style.display = 'block';
            }


            if (mediaType === 'audio') {
                user.audioTrack.play();
            }

            document.getElementById('status').innerText = translations.peer_joined;
        });


        client.on("user-left", (user) => {
            console.log("Peer left:", user.uid);
            document.getElementById('status').innerText = translations.peer_left;
            const remoteDiv = document.getElementById(`remote-player-${user.uid}`);
            if (remoteDiv) remoteDiv.remove();
        });

        client.on('connection-state-change', (curState, revState, reason) => {
            console.log('Connection state changed from', revState, 'to', curState, 'because', reason);
            if (curState === 'CONNECTED') {
                document.getElementById('status').innerText = translations.call_connected;
            } else if (curState === 'DISCONNECTED') {
                document.getElementById('status').innerText = translations.call_disconnected;
            }
        });

        // Toggle audio
        document.getElementById("toggle-audio").onclick = () => {
            if (localTracks.audioTrack.muted) {
                localTracks.audioTrack.setMuted(false);
                document.getElementById("audio-icon").className = "fa fa-microphone";
            } else {
                localTracks.audioTrack.setMuted(true);
                document.getElementById("audio-icon").className = "fa fa-microphone-slash";
            }
        };


        document.getElementById("toggle-video").onclick = async () => {
            if (localTracks.videoTrack.muted) {
                await localTracks.videoTrack.setMuted(false);
                await localTracks.videoTrack.play("local-player");
                document.getElementById("video-icon").className = "fa fa-video";
                document.getElementById('local-player').style.display = 'block';

            } else {
                await localTracks.videoTrack.setMuted(true);
                document.getElementById("video-icon").className = "fa fa-video-slash";

                document.getElementById('local-player').style.display = 'none';
            }
        };


        const callId = "{{ $call->id }}";

        function leaveCall() {
            localTracks.audioTrack && localTracks.audioTrack.close();
            localTracks.videoTrack && localTracks.videoTrack.close();

            client && client.leave();

            // أخفاء الفيديو
            document.getElementById('local-player').style.display = 'none';
            document.getElementById('remote-playerlist').style.display = 'none';

            // إعادة التوجيه
            window.location.href = "{{ route('freelancer.home.index') }}";
        }

        function checkCallEnded() {
            fetch(`/freelancer/call/status/${callId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.ended) {
                        // alert("تم إنهاء المكالمة من الطرف الآخر.");
                        alert("The call has been ended.");
                        leaveCall();
                    }
                })
                .catch(console.error);
        }

        // نبدأ polling كل 3 ثواني
        setInterval(checkCallEnded, 3000);
    </script>
@endsection
