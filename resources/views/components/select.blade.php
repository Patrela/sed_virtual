@props(['name', 'options', 'selected' => null, 'attributes' => []])

<div class="mb-3">
  <select id="{{ $name }}" name="{{ $name }}" class="space-y-2 m-2 border border-gray-100 rounded-md shadow-sm w-full focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2" style="margin-bottom: 4px;">
    @foreach ($options as $key => $value)
      <option value="{{ $key }}" @if ($key == $selected) selected @endif>
        {{ $value }}
      </option>
    @endforeach
  </select>
</div>

