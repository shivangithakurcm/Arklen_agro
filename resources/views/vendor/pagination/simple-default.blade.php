@if ($paginator->hasPages())
<div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; padding-top:1rem; margin-top:1rem; border-top:1px solid #e5e7eb;">

    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:0 4px;color:#9ca3af;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="display:inline-flex;align-items:center;justify-content:center;
                        min-width:36px;height:36px;border-radius:6px;font-size:14px;
                        background:#16a34a;color:#fff;font-weight:500;border:1px solid #16a34a;">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       style="display:inline-flex;align-items:center;justify-content:center;
                       min-width:36px;height:36px;border-radius:6px;font-size:14px;
                       color:#374151;border:1px solid #d1d5db;background:#fff;text-decoration:none;">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

</div>
@endif