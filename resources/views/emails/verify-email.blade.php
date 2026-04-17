<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>E-posta Dogrulama</title>
</head>
<body style="margin:0; padding:24px; background:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; border:1px solid #e5e7eb;">
        <h1 style="margin:0 0 16px; font-size:24px; line-height:1.3;">E-posta adresinizi dogrulayin</h1>

        <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">
            Merhaba {{ $user->name ?: 'kullanici' }},
        </p>

        <p style="margin:0 0 24px; font-size:16px; line-height:1.6;">
            Hesabinizi etkinlestirmek icin asagidaki butona tiklayin.
        </p>

        <p style="margin:0 0 24px;">
            <a href="{{ $verificationLink }}" style="display:inline-block; padding:14px 22px; background:#0f766e; color:#ffffff; text-decoration:none; border-radius:8px; font-weight:700;">
                E-postami Dogrula
            </a>
        </p>

        <p style="margin:0 0 12px; font-size:14px; line-height:1.6; color:#4b5563;">
            Buton calismazsa bu linki tarayiciniza yapistirin:
        </p>

        <p style="margin:0; font-size:14px; line-height:1.6; word-break:break-all;">
            <a href="{{ $verificationLink }}" style="color:#0f766e;">{{ $verificationLink }}</a>
        </p>
    </div>
</body>
</html>
