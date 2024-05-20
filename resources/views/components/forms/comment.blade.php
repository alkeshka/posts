@props(['postId'])

<form {{ $attributes(["class" => "max-w-2xl bg-white rounded-lg border p-2  mt-10"]) }} >
    @csrf
    <div class="px-3 mb-2 mt-2">
        <input type="hidden" name="posts_id" value="{{ $postId }}">
        <textarea name="body" placeholder="Post your comment" 
        class="w-full bg-gray-100 rounded border border-gray-400 leading-normal resize-none h-20 py-2 px-3 placeholder-gray-700 focus:outline-none focus:bg-white">{{ old('commentBody') }}</textarea>
        @error('body')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex justify-end px-4">
        <input type="submit" class="px-2.5 py-1.5 rounded-md text-white text-sm bg-indigo-500" value="Comment">
    </div>
</form>