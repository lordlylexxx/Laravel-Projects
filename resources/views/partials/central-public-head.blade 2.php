@php
    $pageTitle = $pageTitle ?? 'IMPASUGONG TOURISM | Impasugong Accommodations';
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $pageTitle }}</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: {
                        dark: '#1B5E20',
                        primary: '#2E7D32',
                        medium: '#43A047',
                        soft: '#C8E6C9',
                    },
                },
                keyframes: {
                    fadeInUp: {
                        '0%': { opacity: '0', transform: 'translateY(30px)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' },
                    },
                },
                animation: {
                    'fade-in-up': 'fadeInUp 0.6s ease forwards',
                    'fade-in-up-d1': 'fadeInUp 0.6s ease 0.15s forwards',
                    'fade-in-up-d2': 'fadeInUp 0.6s ease 0.3s forwards',
                    'fade-in-up-d3': 'fadeInUp 0.6s ease 0.45s forwards',
                },
            },
        },
    };
</script>
