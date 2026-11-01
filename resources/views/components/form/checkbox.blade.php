@props(['name', 'label', 'checked' => false])

<div>
    <label for="{{ $name }}" class="inline-flex items-center">
        <x-checkbox :id="$name" :name="$name" value="1" :checked="(bool) old($name, $checked)" {{ $attributes }} />
        <span class="ms-2 text-sm text-gray-700">{{ $label }}</span>
    </label>
    <x-input-error :for="$name" class="mt-2" />
</div>
