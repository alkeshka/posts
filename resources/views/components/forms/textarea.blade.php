@props(['name', 'label' => false, 'model' => null])
<div class="col-span-full">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium leading-6 text-gray-900">{{ $label }}</label>
    @endif
    <div class="mt-2">
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            rows="3" 
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old($name) ?? ($model ? $model->{$name} : '') }}</textarea>
    </div>
    <p class="mt-3 text-sm leading-6 text-gray-600"></p>
</div>