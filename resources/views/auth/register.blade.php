<x-layout header="Register">

<x-forms.form action="/register" method="POST">
  

  <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        
        <x-forms.input name="first_name" label="First Name" />

        <x-forms.input name="last_name" label="Last Name" />
        
        <x-forms.input name="email" type="email" label="Email" outerClass="sm:col-span-4" />
        
        <x-forms.input name="password" type="password" label="Password" />

        <x-forms.input name="password_confirmation" type="password" label="Confirm Password" />

      </div>
      
      <x-forms.divider></x-forms.divider>

      <x-forms.buttons :cancel="true" text="Save"></x-forms.buttons>

</x-forms.form>

</x-layout>