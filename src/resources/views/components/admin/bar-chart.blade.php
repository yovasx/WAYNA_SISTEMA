@props([
    'buckets',
    'series',
    'visible',
    'maxValue' => 1,
    'minWidth' => '420px',
    'chartHeight' => 240,
    'wrapperHeight' => 320,
    'barWidth' => 22,
    'minBarHeight' => 10,
])

@php
    $bucketLabel = static fn (array $bucket) => $bucket['label'] ?? $bucket['mes'] ?? $bucket['key'] ?? '';
@endphp

<div class="overflow-x-auto">
    <div style="min-width: {{ $minWidth }};">
        <div class="flex items-end gap-4 border-b border-l border-[#d8d2de] pb-3 pl-3" style="height: {{ $wrapperHeight }}px;">
            @foreach ($buckets as $bucket)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <div class="flex w-full items-end justify-center gap-2" style="height: {{ $chartHeight }}px;">
                        @foreach ($visible as $key)
                            @php
                                $serie = $series[$key] ?? null;
                                $valor = (float) ($bucket[$key] ?? 0);
                                $altura = $valor > 0 && $maxValue > 0
                                    ? max(($valor / $maxValue) * ($chartHeight - 30), $minBarHeight)
                                    : 0;
                            @endphp

                            @if ($serie)
                                <div
                                    class="shrink-0 rounded-t-md transition duration-200 hover:brightness-110"
                                    style="width: {{ $barWidth }}px; height: {{ $altura }}px; background-color: {{ $serie['color'] }};"
                                    title="{{ $serie['label'] }}: Bs {{ number_format($valor, 2) }}"
                                ></div>
                            @endif
                        @endforeach
                    </div>

                    <span class="text-[10px] font-medium text-slate-500">{{ $bucketLabel($bucket) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
