<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->title }} | {{ config('app.name', 'مجوهراتي') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #D4AF37;
            --liquid-gold: linear-gradient(135deg, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
            --matte-black: #121212;
            --royal-cream: #FDFBF7;
            --text-grey: #8E8E93;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--matte_black);
            color: white;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Premium Mesh Background Effect */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(212, 175, 55, 0.05) 0%, transparent 40%);
            z-index: -1;
        }

        .header {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(18, 18, 18, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo {
            font-size: 24px;
            font-weight: 900;
            background: var(--liquid-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .hero-section {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .product-image {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .content-card {
            padding: 30px 24px;
            background: linear-gradient(to bottom, transparent, rgba(18,18,18,0.95));
            border-radius: 40px 40px 0 0;
            margin-top: -40px;
            position: relative;
        }

        .badge {
            background: var(--liquid-gold);
            color: black;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .product-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            color: white;
        }

        .store-info {
            display: flex;
            align-items: center;
            color: var(--primary-gold);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .store-info i {
            margin-left: 6px;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin: 24px 0;
        }

        .spec-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .spec-label {
            color: var(--text-grey);
            font-size: 12px;
            margin-bottom: 4px;
        }

        .spec-value {
            color: var(--primary-gold);
            font-size: 16px;
            font-weight: 700;
        }

        .description {
            color: #ccc;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 100px; /* Space for fixed bottom buttons */
        }

        .action-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px 24px;
            background: rgba(18, 18, 18, 0.95);
            backdrop-filter: blur(15px);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 1000;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--liquid-gold);
            color: black;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        }

        .btn-primary:active {
            transform: scale(0.97);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--primary-gold);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .btn i {
            margin-left: 10px;
        }

        /* Simple Carousel Indicator */
        .indicators {
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
        }

        .dot.active {
            background: var(--primary-gold);
            width: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">مـجـوهـراتـي</div>
    </header>

    <main class="hero-section">
        @php
            $imageUrl = $product->image_url;
            // Handle local dev URLs
            if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = asset('storage/' . $imageUrl);
            }
        @endphp
        
        <img src="{{ $imageUrl }}" alt="{{ $product->title }}" class="product-image">
        
        <div class="indicators">
            <div class="dot active"></div>
            @foreach($product->images->skip(1) as $img)
                <div class="dot"></div>
            @endforeach
        </div>

        <section class="content-card">
            @if($product->is_featured)
                <div class="badge">قطعة ملكية</div>
            @endif
            
            <h1 class="product-title">{{ $product->title }}</h1>
            
            <div class="store-info">
                <span>متجر: {{ $product->merchant->store_name }}</span>
            </div>

            <div class="specs-grid">
                <div class="spec-item">
                    <span class="spec-label">الوزن</span>
                    <span class="spec-value">{{ $product->weight }} جرام</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">العيار</span>
                    <span class="spec-value">K{{ $product->purity }}</span>
                </div>
            </div>

            <div class="description">
                <h3 style="margin-bottom: 10px; color: var(--primary-gold);">التفاصيل</h3>
                <p>{{ $product->description }}</p>
            </div>
        </section>
    </main>

    <footer class="action-footer">
        <a href="mojohrti://product/{{ $product->id }}" class="btn btn-primary">
            فتح في التطبيق
        </a>
        <a href="{{ url('/download-app') }}" class="btn btn-secondary">
            تحميل التطبيق (APK)
        </a>
    </footer>

    <script>
        // Automatic Deep Link Attempt if App is installed
        window.onload = function() {
            // Only try if on mobile
            if(/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)){
                // We'll let the user click the button to avoid annoying auto-redirects
                console.log("On mobile device");
            }
        };
    </script>
</body>
</html>
