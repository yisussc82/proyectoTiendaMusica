@props(['name', 'label', 'options' => [], 'selected' => null, 'placeholder' => null])

<div>
    <x-label :for="$name" :value="$label" />
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
    <x-input-error :for="$name" class="mt-2" />
</div>
