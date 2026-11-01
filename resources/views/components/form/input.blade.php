@props(['name', 'label', 'type' => 'text', 'value' => null])

<div>
    <x-label :for="$name" :value="$label" />
    <x-input :id="$name" :name="$name" :type="$type" :value="old($name, $value)" {{ $attributes->merge(['class' => 'block mt-1 w-full']) }} />
    <x-input-error :for="$name" class="mt-2" />
</div>
