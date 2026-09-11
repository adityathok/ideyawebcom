<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kontak Baru</title>
</head>
<body style="margin: 0; padding: 24px; background: #f3f6ff; font-family: Arial, Helvetica, sans-serif; color: #100f12;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #e3eaff; border-radius: 16px; overflow: hidden;">
        <div style="background: #0a1589; padding: 20px 24px; color: #ffffff;">
            <h1 style="margin: 0; font-size: 18px; font-weight: 600;">Pesan Kontak Baru</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #c7d6ff;">{{ $subjectLine }}</p>
        </div>
        <div style="padding: 24px;">
            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 6px 0; width: 90px; color: #65646e;">Nama</td>
                    <td style="padding: 6px 0; font-weight: 600;">{{ $senderName }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #65646e;">Email</td>
                    <td style="padding: 6px 0;"><a href="mailto:{{ $senderEmail }}" style="color: #0a1589;">{{ $senderEmail }}</a></td>
                </tr>
                @if($senderPhone)
                    <tr>
                        <td style="padding: 6px 0; color: #65646e;">Telepon</td>
                        <td style="padding: 6px 0;">{{ $senderPhone }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #65646e;">Subjek</td>
                    <td style="padding: 6px 0;">{{ $subjectLine }}</td>
                </tr>
            </table>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e3eaff;">

            <p style="margin: 0 0 8px; font-size: 13px; color: #65646e;">Pesan:</p>
            <p style="margin: 0; font-size: 15px; line-height: 1.7; white-space: pre-line;">{{ $messageBody }}</p>
        </div>
    </div>
</body>
</html>
