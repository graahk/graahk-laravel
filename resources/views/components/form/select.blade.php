@props([
    'label' => null,
    'options' => collect(),
    'nullable' => false,
    'value' => null,
    'name' => strtolower($label),
])

<x-form.reactive-label :$name :$label>
    <select
        data-label-target
        x-on:change="setLabelStatus()"
        {{ $attributes->except('options')->merge([
            'class' => 'bg-surface py-3 px-2 text-lg rounded-lg w-full outline-none',
        ]) }}
    >
        @if ($nullable)
            <option value=""></option>
        @endif

        @foreach ($options as $key => $label)
            <option
                value="{{ $key }}"
                @if($key === $value) selected="selected" @endif
            >
                {{ $label}}
            </option>
        @endforeach
    </select>
</x-form.reactive-label>

@error($attributes->get('wire:model'))
    <div class="text-error">
        {{ $message }}
    </div>
@enderror
