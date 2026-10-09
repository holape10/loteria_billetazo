{{-- PWA: permite instalar El Billetazo como app en el celular o la computadora --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#000000">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="El Billetazo">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<script src="{{ asset('js/pwa.js') }}" defer></script>
