@props(['cancel' => false, 'text' => 'Save'])
<div class="mt-6 flex items-center justify-end gap-x-6">
  @if ($cancel)
    <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
  @endif
  <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
    {{ $text }}</button>
</div>