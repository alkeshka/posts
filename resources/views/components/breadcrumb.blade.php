@props(['breadcrumbsName', 'model' => null])

<div class="mb-2 rounded-xl p-2 bg-white border-b border-gray-200">
    {{ Breadcrumbs::render($breadcrumbsName, $model) }}
</div>
