@extends('layouts.master')

@section('content')
<div class="p-5">
    <h2>Calling {{ $receiver->username }}</h2>
    <div id="local-player" style="width: 400px; height: 300px; background: #333;"></div>
    <div id="remote-playerlist"></div>
</div>

<script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>
<script>
    const appId = "{{ $appId }}";
    const channelName = "{{ $channelName }}";
    const token = "{{ $token }}";
    const uid = {{ $uid }};

    const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

    client.init(appId, function () {
        console.log("AgoraRTC client initialized");

        client.join(token, channelName, uid, (uid) => {
            console.log("User " + uid + " join channel successfully");

            const localStream = AgoraRTC.createStream({
                streamID: uid,
                audio: true,
                video: true,
                screen: false
            });

            localStream.init(() => {
                localStream.play('local-player');
                client.publish(localStream, (err) => {
                    console.log("Publish local stream error: " + err);
                });
            });

        });
    }, (err) => {
        console.log("AgoraRTC client init failed", err);
    });

    client.on("stream-added", function (evt) {
        const stream = evt.stream;
        console.log("New stream added: " + stream.getId());
        client.subscribe(stream);
    });

    client.on("stream-subscribed", function (evt) {
        const remoteStream = evt.stream;
        const remoteId = "remote-player-" + remoteStream.getId();
        const remoteDiv = document.createElement("div");
        remoteDiv.id = remoteId;
        remoteDiv.style.width = "400px";
        remoteDiv.style.height = "300px";
        document.getElementById("remote-playerlist").appendChild(remoteDiv);
        remoteStream.play(remoteId);
    });

</script>
@endsection
