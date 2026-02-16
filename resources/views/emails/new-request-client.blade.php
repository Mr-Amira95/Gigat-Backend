<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Thank You for Your Request</title>
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
                            Hi {{ $request->user->username }},
                        </td>
                    </tr>

                    <!-- Intro -->
                    <tr>
                        <td style="color:#374151; font-size:15px; line-height:1.7; padding-bottom:25px;">
                            Thank you for requesting our services on <strong>Gigat</strong>.
                            We’re excited to start working with you!
                        </td>
                    </tr>

                    <!-- Section Title -->
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:15px;">
                            Here are the payment details for your request: </td>
                    </tr>

                    <!-- Details Table -->
                    <tr>
                        <td>
                            <table width="100%" cellpadding="10" cellspacing="0"
                                style="border-collapse:collapse; font-size:14px; color:#374151;">

                                <tr style="background:#f9fafb;">
                                    <td style="border:1px solid #e5e7eb;"><strong>Request ID</strong></td>
                                    <td style="border:1px solid #e5e7eb;">{{ $request->order_number }}</td>
                                </tr>

                                <tr>
                                    <td style="border:1px solid #e5e7eb;"><strong>Service</strong></td>
                                    <td style="border:1px solid #e5e7eb;">
                                        {{ $request->service->translations()->where('language', 'en')->first()->title }}
                                    </td>
                                </tr>

                                <tr style="background:#f9fafb;">
                                    <td style="border:1px solid #e5e7eb;"><strong>Deadline</strong></td>
                                    <td style="border:1px solid #e5e7eb;">{{ $request->end_date }}</td>
                                </tr>

                                <tr>
                                    <td style="border:1px solid #e5e7eb;"><strong>Amount</strong></td>
                                    <td style="border:1px solid #e5e7eb;">${{ $finance->amount }}</td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                    <!-- Support -->
                    <tr>
                        <td style="padding-top:25px; font-size:14px; color:#4b5563; line-height:1.6;">
                            You can reach out to us for any inquiries at
                            <a href="mailto:support@gigat.app" style="color:#2563eb; text-decoration:none;">
                                support@gigat.app
                            </a>.
                        </td>
                    </tr>

                    <!-- Contract Notice -->
                    <tr>
                        <td style="padding-top:15px; font-size:14px; color:#4b5563;">
                            Please find the attached contract for your reference.
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
