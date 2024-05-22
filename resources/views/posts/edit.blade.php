<x-layout header="Edit Your Post: {{ $post->title }}">
    <x-breadcrumb breadcrumbsName="edit" :model="$post" />
    <x-forms.form action="/posts/{{ $post->id }}/edit" enctype="multipart/form-data" method="POST">
        <div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

            <x-forms.input name="title" label="Title" :value="$post->title" />

            <x-forms.textarea name="body" label="Post Body" :model="$post"/>

            <x-forms.select name="status" label="Published Status">
                <option value="1" @if ($post->status == 1) selected @endif>Active</option>
                <option value="0" @if ($post->status == 0) selected @endif>Inactive</option>
            </x-forms.select>

            <x-forms.input name="thumbnail" label="Thumbnail" type="file" />
            <x-forms.input name="categories" label="Category ( Seprated by comma ',' )" :value="$post->tags->pluck('name')->implode(',')"  />
        </div>
        <x-forms.buttons :cancel="true" text="Update"></x-forms.buttons>
    </x-forms.form>
</x-layout>
