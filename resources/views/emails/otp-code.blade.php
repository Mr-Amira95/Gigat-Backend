<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verification Code</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f1f8; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f1f8; padding:32px 12px;">
    <tr>
        <td align="center">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 6px 24px rgba(88, 28, 219, 0.08);">

                {{-- Header --}}
                <tr>
                    <td align="center" style="background:linear-gradient(135deg,#6D28D9,#8B5CF6); padding:32px 24px;">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="max-height:40px; display:block;">
                        @else
                            <span style="font-size:22px; font-weight:700; color:#ffffff; letter-spacing:0.5px;">{{ $appName }}</span>
                        @endif
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:40px 36px 24px 36px;">
                        <p style="margin:0 0 4px; font-size:14px; color:#8b8a99;">Hello,</p>
                        <h1 style="margin:0 0 16px; font-size:20px; line-height:28px; color:#1f1b2e; font-weight:700;">{{ $user->username ?? 'there' }} 👋</h1>
                        <p style="margin:0 0 28px; font-size:15px; line-height:24px; color:#5c596b;">
                            Use the verification code below to continue on {{ $appName }}. Enter it on the verification screen to confirm it's really you.
                        </p>

                        {{-- OTP Box --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="background-color:#f6f3ff; border:1px dashed #c7b6f7; border-radius:12px; padding:20px 16px;">
                                    <span style="display:inline-block; font-size:34px; font-weight:700; letter-spacing:10px; color:#6D28D9; font-family:'Courier New', monospace;">
                                        {{ $code }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0; font-size:13px; line-height:20px; color:#9793a5;">
                            ⏱ This code expires in a few minutes. If you didn't request it, you can safely ignore this email — your account is still secure.
                        </p>
                    </td>
                </tr>

                {{-- Divider --}}
                <tr>
                    <td style="padding:0 36px;">
                        <div style="border-top:1px solid #efedf6;"></div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 36px 32px 36px;" align="center">
                        <p style="margin:0 0 4px; font-size:12px; color:#b3b0c0;">
                            Need help? Contact us at <a href="mailto:support@gigat.app" style="color:#6D28D9; text-decoration:none;">support@gigat.app</a>
                        </p>
                        <p style="margin:0; font-size:12px; color:#c6c3d2;">
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
