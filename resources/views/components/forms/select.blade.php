@props(['name', 'label' => false])

<div class="sm:col-span-3">
    @if ($label)
        <label for="{{ $name }}" class="m-1 block text-sm font-medium leading-6 text-gray-900">{{ $label }}</label>
    @endif
    <div class="mt-2">
        <select id="{{ $name }}" name="{{ $name }}" autocomplete="country-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
            {{ $slot }}
        </select>
    </div>
</div>