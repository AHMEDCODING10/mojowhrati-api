<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - مجوهراتي</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Tajawal', 'Segoe UI', Tahoma, Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            padding: 30px 15px;
            direction: rtl;
        }

        .wrapper {
            max-width: 580px;
            margin: 0 auto;
        }

        /* === HEADER === */
        .header {
            background: linear-gradient(135deg, #1C1C2E 0%, #2D2D44 100%);
            border-radius: 20px 20px 0 0;
            padding: 40px 30px 30px;
            text-align: center;
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50px; left: -50px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(212,175,55,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30px; right: -30px;
            width: 150px; height: 150px;
            background: radial-gradient(circle, rgba(212,175,55,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .logo-circle {
            width: 75px;
            height: 75px;
            background: linear-gradient(135deg, #D4AF37, #F5D060, #B8860B);
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: 0 6px 24px rgba(212, 175, 55, 0.4);
        }

        .brand-name {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #D4AF37, #F5D060);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .brand-sub {
            font-size: 12px;
            color: rgba(212, 175, 55, 0.5);
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* === BODY === */
        .body {
            background: #ffffff;
            padding: 44px 40px;
            text-align: center;
        }

        .title-badge {
            display: inline-block;
            background: linear-gradient(135deg, #FFF9E6, #FFF3C4);
            border: 1px solid rgba(212,175,55,0.3);
            color: #B8860B;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 18px;
            border-radius: 20px;
            margin-bottom: 22px;
            letter-spacing: 0.5px;
        }

        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: #1C1C2E;
            margin-bottom: 12px;
        }

        .description {
            font-size: 15px;
            color: #666;
            line-height: 1.8;
            margin-bottom: 36px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* === OTP BOX === */
        .otp-container {
            background: linear-gradient(135deg, #1C1C2E 0%, #2D2D44 100%);
            border-radius: 16px;
            padding: 32px 24px;
            margin: 0 0 32px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(28, 28, 46, 0.15);
        }

        .otp-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #D4AF37, #F5D060, #D4AF37, transparent);
        }

        .otp-label {
            font-size: 12px;
            color: rgba(212, 175, 55, 0.6);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .otp-code {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 12px;
            background: linear-gradient(135deg, #D4AF37, #F5D060, #D4AF37);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Courier New', monospace;
            text-shadow: none;
        }

        .otp-expire {
            margin-top: 14px;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }

        .otp-expire span {
            color: #D4AF37;
            font-weight: 700;
        }

        /* === INFO CARDS === */
        .info-cards {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            text-align: right;
        }

        .info-card {
            flex: 1;
            background: #F8F9FF;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #EAECF0;
        }

        .info-card-icon {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .info-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #1C1C2E;
            margin-bottom: 4px;
        }

        .info-card-text {
            font-size: 11px;
            color: #888;
            line-height: 1.5;
        }

        /* === WARNING === */
        .warning {
            background: linear-gradient(135deg, #FFF5F5, #FFF0F0);
            border: 1px solid rgba(220, 53, 69, 0.15);
            border-right: 3px solid #dc3545;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13px;
            color: #721c24;
            text-align: right;
            margin-bottom: 8px;
        }

        /* === FOOTER === */
        .footer {
            background: linear-gradient(135deg, #1C1C2E 0%, #2D2D44 100%);
            border-radius: 0 0 20px 20px;
            padding: 28px 30px;
            text-align: center;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }

        .footer-divider {
            width: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            margin: 0 auto 16px;
        }

        .footer-text {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            line-height: 1.7;
        }

        .footer-brand {
            font-size: 13px;
            font-weight: 700;
            color: rgba(212, 175, 55, 0.7);
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <div class="logo-circle">💎</div>
            <div class="brand-name">مجوهراتي</div>
            <div class="brand-sub">Mojawharati</div>
        </div>

        <!-- Body -->
        <div class="body">
            <div class="title-badge">{{ $title }}</div>

            <div class="greeting">مرحباً بك! 👋</div>
            <p class="description">
                لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.
                استخدم رمز التحقق التالي لإتمام العملية.
            </p>

            <!-- OTP Code Box -->
            <div class="otp-container">
                <div class="otp-label">رمز التحقق الخاص بك</div>
                <div class="otp-code">{{ $code }}</div>
                <div class="otp-expire">
                    صالح لمدة <span>10 دقائق</span> فقط
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon">🔒</div>
                    <div class="info-card-title">أمان كامل</div>
                    <div class="info-card-text">رمزك سري ولا نطلبه أبداً بأي طريقة أخرى</div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">⚡</div>
                    <div class="info-card-title">استخدام فوري</div>
                    <div class="info-card-text">أدخل الرمز في التطبيق قبل انتهاء مدة صلاحيته</div>
                </div>
            </div>

            <!-- Warning -->
            <div class="warning">
                ⚠️ إذا لم تطلب هذا الرمز، تجاهل هذا البريد فوراً وتواصل معنا إن كان لديك أي استفسار.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-divider"></div>
            <div class="footer-brand">💎 مجوهراتي — Mojawharati</div>
            <div class="footer-text">
                © {{ date('Y') }} جميع الحقوق محفوظة.<br>
                هذا البريد مُرسَل تلقائياً، يرجى عدم الرد عليه.
            </div>
        </div>

    </div>
</body>
</html>
