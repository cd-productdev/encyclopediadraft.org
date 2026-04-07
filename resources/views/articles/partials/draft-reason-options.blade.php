@php
    $rawReasons = isset($article) ? ($article->rejection_reason ?? []) : [];
    if (is_string($rawReasons)) {
        $rawReasons = $rawReasons !== '' ? [$rawReasons] : [];
    }
    if (! is_array($rawReasons)) {
        $rawReasons = [];
    }
    $existingReasons = old('rejection_reason', $rawReasons);
    $existingActive = old('rejection_active', []);

    if (empty($existingActive) && ! empty($existingReasons)) {
        foreach ($existingReasons as $k => $v) {
            if ($v !== null && trim((string) $v) !== '') {
                $existingActive[$k] = '1';
            }
        }
    }
@endphp

<div id="rejectionReasonsField" class="space-y-4">
    <span class="block text-sm font-medium text-gray-700">Rejection reasons</span>
    <p class="text-xs text-gray-500">Up to four lines. Check <strong>Show on article</strong> for any line whose text should appear on the article page.</p>

    <div class="grid grid-cols-1 gap-3">
        @for ($i = 0; $i < 4; $i++)
            @php
                $rowOn = isset($existingActive[$i]) && (string) $existingActive[$i] === '1';
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-start gap-3 p-3 border border-gray-200 rounded-lg bg-white">
                <div class="shrink-0 flex items-center pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                        <input type="checkbox"
                               name="rejection_active[{{ $i }}]"
                               value="1"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                               {{ $rowOn ? 'checked' : '' }}>
                        <span class="font-medium text-gray-600">Show on article</span>
                    </label>
                </div>

                <div class="flex-1 min-w-0">
                    <label for="rejection_reason_{{ $i }}" class="sr-only">Rejection reason {{ $i + 1 }}</label>
                    <input type="text"
                           id="rejection_reason_{{ $i }}"
                           name="rejection_reason[{{ $i }}]"
                           value="{{ $existingReasons[$i] ?? '' }}"
                           placeholder="Text for line {{ $i + 1 }} (optional)"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
        @endfor
    </div>
</div>
