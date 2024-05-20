<x-layout header="Log In">

<x-forms.form action="/login" method="POST">
    @csrf

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

        <x-forms.input name="email" type="email" label="Email" outerClass="sm:col-span-4" />
        
        <x-forms.input name="password" type="password" label="Password" outerClass="sm:col-span-4" />      

      </div>

      @error('auth_exception')
        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
      @enderror

      <x-forms.divider></x-forms.divider>

      <x-forms.buttons :cancel="false" text="Log In"></x-forms.buttons>

</x-forms.form>

</x-layout>