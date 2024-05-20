<x-layout header="Create Your Post">
    <x-forms.form action="/posts" enctype="multipart/form-data" method="POST">
        <div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

          <x-forms.input name="title" label="Title" />
          
          <x-forms.textarea name="body" label="Post Body" />

          <x-forms.select name="status" label="Published Status">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </x-forms.select>

          <x-forms.input name="thumbnail" label="Thumbnail" type="file" />

          <x-forms.input name="categories" label="Category ( Seprated by comma ',' )" />
        </div>
        <x-forms.buttons :cancel="true" text="Save"></x-forms.buttons>
    </x-forms.form>
</x-layout>