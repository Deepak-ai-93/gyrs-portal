@props(['name' => null, 'size' => 20, 'label' => null, 'solid' => false])

@php
    $icons = [
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/>',
        'map-pin' => '<path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
        'building' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h6"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.5a3.5 3.5 0 0 1 0 7"/><path d="M21.5 20a6.5 6.5 0 0 0-5-6.35"/>',
        'shield' => '<path d="M12 3 5 6v5c0 4.5 3 7.8 7 10 4-2.2 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
        'check' => '<path d="m4 12 5 5L20 6"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
        'alert' => '<path d="M12 4 2.5 20h19L12 4Z"/><path d="M12 10v4"/><path d="M12 17.5h.01"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/>',
        'x' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'arrow-left' => '<path d="M19 12H5"/><path d="m11 18-6-6 6-6"/>',
        'calendar' => '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'file' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/>',
        'phone' => '<path d="M5 4h3l2 5-2 1.5a11 11 0 0 0 5 5L14.5 13l5 2v3a2 2 0 0 1-2.2 2A16 16 0 0 1 3 6.2 2 2 0 0 1 5 4Z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'external' => '<path d="M14 4h6v6"/><path d="M20 4 11 13"/><path d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'logout' => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 12H3"/><path d="m7 8-4 4 4 4"/>',
        'home' => '<path d="M4 11 12 4l8 7"/><path d="M6 10v10h12V10"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'download' => '<path d="M12 4v10"/><path d="m8 11 4 4 4-4"/><path d="M5 20h14"/>',
        'edit' => '<path d="M4 20h4l10-10-4-4L4 16v4Z"/><path d="m14 6 4 4"/>',
        'trash' => '<path d="M4 7h16"/><path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/><path d="M6 7v13a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7"/><path d="M10 11v6M14 11v6"/>',
        'eye' => '<path d="M2.5 12S6 6 12 6s9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/>',
        'eye-off' => '<path d="M3 3l18 18"/><path d="M10.6 6.2A9.7 9.7 0 0 1 12 6c6 0 9.5 6 9.5 6a17.6 17.6 0 0 1-3.3 3.9"/><path d="M6.2 6.9A17.4 17.4 0 0 0 2.5 12S6 18 12 18a9.6 9.6 0 0 0 3.6-.7"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>',
        'award' => '<circle cx="12" cy="9" r="5"/><path d="m8.5 13-1.5 7 5-3 5 3-1.5-7"/>',
        'refresh' => '<path d="M20 11a8 8 0 1 0-2.3 5.7"/><path d="M20 4v7h-7"/>',
        'lock' => '<rect x="4.5" y="10" width="15" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'sparkles' => '<path d="m12 4 1.6 4.4L18 10l-4.4 1.6L12 16l-1.6-4.4L6 10l4.4-1.6L12 4Z"/><path d="M18 15.5 18.7 17l1.5.7-1.5.7L18 20l-.7-1.5L15.8 17.7l1.5-.7.7-1.5Z"/>',
        'ticket' => '<path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V8Z"/><path d="M13 6v12"/>',
        'filter' => '<path d="M3 5h18l-7 8v6l-4-2v-4L3 5Z"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'inbox' => '<path d="M4 13 6 5h12l2 8v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5Z"/><path d="M4 13h5l1 2h4l1-2h5"/>',
    ];

    // Filled counterparts, used by the mobile tab bar. Shapes are drawn to the
    // same 24×24 grid and optical size as the outline set above.
    $solidIcons = [
        'home' => '<path d="M12 2.7 2.5 10.5a1.3 1.3 0 0 0 .8 2.3h1.1V19a2 2 0 0 0 2 2h3.3v-4.6a2.3 2.3 0 0 1 4.6 0V21h3.3a2 2 0 0 0 2-2v-6.2h1.1a1.3 1.3 0 0 0 .8-2.3L12 2.7Z"/>',
        'briefcase' => '<path fill-rule="evenodd" d="M4.8 6.6h14.4a2.4 2.4 0 0 1 2.4 2.4v9.4a2.4 2.4 0 0 1-2.4 2.4H4.8a2.4 2.4 0 0 1-2.4-2.4V9a2.4 2.4 0 0 1 2.4-2.4Zm5.4 5.1a1.1 1.1 0 0 0 0 2.2h3.6a1.1 1.1 0 0 0 0-2.2h-3.6Z"/><path d="M8.4 6.6V5.3a2.4 2.4 0 0 1 2.4-2.4h2.4a2.4 2.4 0 0 1 2.4 2.4v1.3h-2V5.3a.4.4 0 0 0-.4-.4h-2.4a.4.4 0 0 0-.4.4v1.3H8.4Z"/>',
        'search' => '<path fill-rule="evenodd" d="M10.6 3a7.6 7.6 0 1 0 0 15.2 7.6 7.6 0 0 0 0-15.2Zm0 3.4a4.2 4.2 0 1 1 0 8.4 4.2 4.2 0 0 1 0-8.4Z"/><path d="M16.43 14.38 20.63 18.58a1.45 1.45 0 0 1-2.05 2.05L14.38 16.43Z"/>',
        'user' => '<path d="M12 3.4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/><path d="M4 21a8 8 0 0 1 16 0Z"/>',
        'plus' => '<path d="M10.1 5.3a1.9 1.9 0 0 1 3.8 0v4.8h4.8a1.9 1.9 0 0 1 0 3.8h-4.8v4.8a1.9 1.9 0 0 1-3.8 0v-4.8H5.3a1.9 1.9 0 0 1 0-3.8h4.8V5.3Z"/>',
        'sparkles' => '<path d="M10.1 2.9Q11.88 8.33 17.3 10.1 11.88 11.88 10.1 17.3 8.33 11.88 2.9 10.1 8.33 8.33 10.1 2.9Z"/><path d="M18.9 15.7Q19.6 17.89 21.8 18.6 19.6 19.31 18.9 21.5 18.2 19.31 16 18.6 18.2 17.89 18.9 15.7Z"/>',
        'logout' => '<path d="M10.3 10.3h6.1a1.7 1.7 0 0 1 0 3.4h-6.1v4.3L3.1 12 10.3 6v4.3Z"/><path d="M16.4 3.5h2.9a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2h-2.9a1.15 1.15 0 0 1-1.15-1.15V18.2h3.95V5.8h-3.95V4.65A1.15 1.15 0 0 1 16.4 3.5Z"/>',
        'shield' => '<path fill-rule="evenodd" d="M11.3 2.7a1.8 1.8 0 0 1 1.4 0l7 3a1.8 1.8 0 0 1 1 1.6V11c0 5-3.2 8.6-7.7 10.9a1.7 1.7 0 0 1-1.5 0C7.2 19.6 3.3 16 3.3 11V7.3a1.8 1.8 0 0 1 1-1.6l7-3Zm-3.21 9.61a1.15 1.15 0 0 1 1.62 0L11.2 12.18l3.99-3.99a1.15 1.15 0 0 1 1.62 1.62L11.2 15.42 8.09 12.31Z"/>',
    ];

    $solid = (bool) $solid;
    $isSolid = $solid && $name && isset($solidIcons[$name]);
@endphp

@if ($name && $isSolid)
    <svg
        class="icon icon-{{ $name }} icon-solid"
        width="{{ $size }}"
        height="{{ $size }}"
        viewBox="0 0 24 24"
        fill="currentColor"
        stroke="none"
        focusable="false"
        @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
    >{!! $solidIcons[$name] !!}</svg>
@elseif ($name && isset($icons[$name]))
    <svg
        class="icon icon-{{ $name }}"
        width="{{ $size }}"
        height="{{ $size }}"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        focusable="false"
        @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
    >{!! $icons[$name] !!}</svg>
@endif
