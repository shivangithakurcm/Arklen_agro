@if ($paginator->hasPages())
<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding-top:1rem;margin-top:0.5rem;">
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:0 4px;color:#9ca3af;">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border-radius:6px;font-size:14px;background:var(--green-700);color:#fff;font-weight:500;">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border-radius:6px;font-size:14px;color:var(--green-800);border:1px solid var(--green-700);background:#fff;text-decoration:none;">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach
</div>
@endif