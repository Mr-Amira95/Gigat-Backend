<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Support Ticket</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:40px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:10px; box-shadow:0 3px 12px rgba(0,0,0,0.06); padding:40px;">

                    <!-- Greeting -->
                    <tr>
                        <td style="color:#111827; font-size:18px; font-weight:600; padding-bottom:15px;">
                            Hi {{ $ticket->user->username }},
                        </td>
                    </tr>

                    <!-- Intro -->
                    <tr>
                        <td style="color:#374151; font-size:15px; line-height:1.7; padding-bottom:25px;">
                            Our support team has opened a new ticket on your behalf on <strong>Gigat</strong>.
                        </td>
                    </tr>

                    <!-- Details Table -->
                    <tr>
                        <td>
                            <table width="100%" cellpadding="10" cellspacing="0"
                                style="border-collapse:collapse; font-size:14px; color:#374151;">

                                <tr style="background:#f9fafb;">
                                    <td style="border:1px solid #e5e7eb;"><strong>Ticket Code</strong></td>
                                    <td style="border:1px solid #e5e7eb;">{{ $ticket->code }}</td>
                                </tr>

                                <tr>
                                    <td style="border:1px solid #e5e7eb;"><strong>Subject</strong></td>
                                    <td style="border:1px solid #e5e7eb;">{{ $ticket->subject }}</td>
                                </tr>

                                @if ($ticket->messages->first())
                                    <tr style="background:#f9fafb;">
                                        <td style="border:1px solid #e5e7eb;"><strong>Message</strong></td>
                                        <td style="border:1px solid #e5e7eb;">{{ $ticket->messages->first()->message }}</td>
                                    </tr>
                                @endif

                            </table>
                        </td>
                    </tr>

                    <!-- Support -->
                    <tr>
                        <td style="padding-top:25px; font-size:14px; color:#4b5563; line-height:1.6;">
                            You can reply directly from your Gigat account, or reach out to us at
                            <a href="mailto:support@gigat.app" style="color:#2563eb; text-decoration:none;">
                                support@gigat.app
                            </a>.
                        </td>
                    </tr>

                    <!-- Closing -->
                    <tr>
                        <td style="padding-top:30px; font-size:14px; color:#111827;">
                            Best regards,<br>
                            <strong>The Gigat Team</strong>
                        </td>
                    </tr>

                </table>

                <!-- Footer -->
                <table width="600" cellpadding="0" cellspacing="0" style="margin-top:15px;">
                    <tr>
                        <td align="center" style="font-size:12px; color:#9ca3af;">
                            © {{ date('Y') }} Gigat. All rights reserved.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>
