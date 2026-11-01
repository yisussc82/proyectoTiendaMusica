@props(['name', 'label', 'value' => null])

<x-form.input
    type="date"
    :name="$name"
    :label="$label"
    :value="$value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value"
    {{ $attributes }}
/>
