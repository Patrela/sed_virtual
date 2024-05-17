@props(['value', 'text', 'attributes' => [],'selected' => false])

<option value="{{ $value }}"
    {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}
    @if ($selected) selected @endif
>
  {{ $text }}
</option>
