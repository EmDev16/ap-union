<style>
    *,
    *::before,
    *::after {
        border-radius: 0 !important;
    }

    .rounded-full,
    .nav-count-badge,
    .home-count-badge,
    .message-avatar,
    .message-unread {
        border-radius: 9999px !important;
    }

    :root {
        /* White and glowing silver, cobalt blue, burnt orange accents, warm brown headings */
        --c-page: #F3F4F6;
        --c-surface: #FFFFFF;
        --c-line: #D3D7DC;
        --c-ink: #1E2226;
        --c-muted: #61686F;
        --c-brand: #14417E;
        --c-brand-strong: #0E2F5C;
        --c-accent: #D2691E;
        --c-accent-strong: #A9530F;
        --c-soft: #E3E6EA;
        --c-soft-strong: #CDD2D8;
        --c-brown: #4A3728;
    }

    body {
        background-color: var(--c-page) !important;
        color: var(--c-ink) !important;
    }

    .bg-white,
    .bg-gray-50,
    .bg-gray-100 {
        background-color: var(--c-surface) !important;
    }

    .bg-indigo-500,
    .bg-indigo-600 {
        background-color: var(--c-brand) !important;
        color: #fff !important;
    }

    .hover\:bg-indigo-600:hover,
    .hover\:bg-indigo-700:hover {
        background-color: var(--c-brand-strong) !important;
    }

    .text-indigo-600,
    .text-indigo-700,
    .text-indigo-800 {
        color: var(--c-brand) !important;
    }

    .border-indigo-400,
    .border-indigo-500,
    .border-indigo-700 {
        border-color: var(--c-brand) !important;
    }

    .bg-gray-300 {
        background-color: var(--c-soft) !important;
        color: var(--c-ink) !important;
    }

    .hover\:bg-gray-100:hover,
    .hover\:bg-gray-50:hover {
        background-color: var(--c-soft) !important;
    }

    .hover\:bg-gray-400:hover {
        background-color: var(--c-soft-strong) !important;
    }

    .bg-gray-700,
    .bg-gray-800,
    .bg-gray-900 {
        background-color: var(--c-brown) !important;
    }

    .border-gray-200,
    .border-gray-300,
    .border-gray-400 {
        border-color: var(--c-line) !important;
    }

    .text-gray-900,
    .text-gray-800 {
        color: var(--c-ink) !important;
    }

    .text-gray-400,
    .text-gray-500,
    .text-gray-600,
    .text-gray-700 {
        color: var(--c-muted) !important;
    }

    .text-red-500,
    .text-red-600,
    .text-red-700 {
        color: var(--c-accent-strong) !important;
    }

    .bg-red-600,
    .bg-red-700 {
        background-color: var(--c-accent-strong) !important;
    }

    .nav-count-badge,
    .home-count-badge {
        background: var(--c-accent) !important;
        color: #fff !important;
    }

    h1, h2, h3, .home-main-title, .home-sidebar-title, .home-post-title, .home-item-title {
        color: var(--c-brown) !important;
    }

    .home-side-box,
    .home-post-box,
    .post-card {
        background: var(--c-surface) !important;
        border-color: var(--c-line) !important;
    }

    .home-sidebar:first-of-type .home-side-box {
        border-left: 3px solid var(--c-brand) !important;
    }

    .home-sidebar:last-of-type .home-side-box {
        border-left: 3px solid var(--c-accent) !important;
    }

    .profile-tool + .profile-tool {
        border-left-color: var(--c-line) !important;
    }

    .profile-tool {
        color: var(--c-brand) !important;
    }

    .profile-tool:hover {
        background: var(--c-soft) !important;
    }

    nav a:hover {
        color: var(--c-brand) !important;
    }
</style>
