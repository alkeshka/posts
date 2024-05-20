@props(['name', 'label' => false, 'type' => 'text', 'outerClass' => 'sm:col-span-3' ])
<div class="{{ $outerClass}}">
    @if ($label)
    <label for="{{ $name }}" class="block text-sm font-medium leading-6 text-gray-900">{{ $label }}</label>
    @endif
    <div class="mt-2">
    <input type="{{ $type }}" value="{{ old($name) }}" name="{{ $name }}" id="{{ $name }}" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    </div>
    @error($name)
        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>