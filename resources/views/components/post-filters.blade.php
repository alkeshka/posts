@props(['postAuthors', 'tags', 'publishedDates', 'commentsCounts' , 'authUserRoleId'])

<div class="mb-2 flex">

    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <select name="author" id="author" class="ml-8 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Filter by Author</option>
        @foreach ($postAuthors as $postAuthor)
            <option value="{{ $postAuthor->id }}">{{ $postAuthor->first_name . ' '. $postAuthor->last_name }}</option>
        @endforeach
    </select>

    <select name="category" id="category" class="ml-8 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Filter by Categories</option>
        @foreach ($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
    </select>

    {{-- <select name="noOfComments" id="noOfComments" class="ml-8 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Filter by no of comments</option>
        @foreach ($commentsCounts as $commentsCount )
            <option value="{{ $commentsCount }}">{{ $commentsCount }}</option>
        @endforeach
    </select> --}}

    <select name="publishedDate" id="publishedDate" class="ml-8 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Filter by published date</option>
        @foreach ($publishedDates as $publishedDate)
            <option value="{{ $publishedDate }}">{{ $publishedDate }}</option>
        @endforeach
    </select>


    <div class="inline-block">
        <input type="text" class="ml-8 rounded-md p-3" placeholder="Enter the no. of comments" id="noOfComments" name="noOfComments" >
    </div>

    <div class="inline-block">
        <input type="text" class="ml-8 rounded-md p-3" placeholder="Search" id="searchQuery" name="searchQuery" >
    </div>

    <input type="hidden" id="isLoggedIn" value="{{ Auth::check() }}">

    @auth
        <input type="hidden" id="userId" value="{{ Auth::id() }}">
        <input type="hidden" id="userRoleId" value="{{ Auth::user()->users_role_id }}">
    @endauth



{{-- <div date-rangepicker class="flex items-center">
  <div class="relative">
    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
         <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
        </svg>
    </div>
    <input name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start">
  </div>
  <span class="mx-4 text-gray-500">to</span>
  <div class="relative">
    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
         <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
        </svg>
    </div>
    <input name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end">
</div>
</div> --}}


</div>
