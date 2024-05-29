@props(['postAuthors', 'tags'])

<div class="mb-2 flex">

    <select name="author" id="author" class="ml-1 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Filter by Author</option>
        @foreach ($postAuthors as $postAuthor)
            <option class="capitalize" value="{{ $postAuthor->id }}">{{ $postAuthor->first_name . ' '. $postAuthor->last_name }}</option>
        @endforeach
    </select>

    <select name="category" id="category" class="ml-1 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Filter by Categories</option>
        @foreach ($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
    </select>

    <input type="text" name="publishedDateRange" id="publishedDateRange" placeholder="Select the date" class="ml-1 border border-gray-300 rounded-md p-3 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>

    <div class="inline-block">
        <input type="number" class="ml-4 rounded-md p-3" placeholder="Enter the no. of comments" id="noOfComments" name="noOfComments" >
    </div>

    <div class="inline-block">
        <input type="text" class="ml-8 rounded-md p-3" placeholder="Search" id="searchQuery" name="searchQuery" >
    </div>

</div>
