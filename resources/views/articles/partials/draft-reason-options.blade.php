@php
    /** @var string $selectedKey */
    $draftPresets = config('article.draft_reason_presets', []);
@endphp
<div id="draftReasonField" class="space-y-3" style="display: none;">
    <span class="block text-sm font-medium text-gray-700">Draft notice (optional)</span>
    <p class="text-xs text-gray-500">Choose at most one. If none are selected, nothing appears on the article page.</p>
    <input type="hidden" name="draft_reason_preset" id="draft_reason_preset" value="{{ $selectedKey }}">
    <div class="space-y-2">
        @foreach ($draftPresets as $key => $label)
            <label class="flex items-start gap-3 cursor-pointer rounded-md border border-gray-200 p-3 hover:bg-gray-50">
                <input type="checkbox"
                    class="draft-reason-checkbox mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    data-draft-preset="{{ $key }}"
                    {{ $selectedKey === $key ? 'checked' : '' }}>
                <span class="text-sm text-gray-700 leading-snug">{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>
