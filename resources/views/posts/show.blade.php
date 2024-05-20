{{-- {{ dd($post->comments) }} --}}
<x-layout header="{{ $post->title }}" >

    <section >
        <div>
            {{ $post->body }} 
        </div>
    </section>

    <section>
        <form class="max-w-2xl bg-white rounded-lg border p-2  mt-20">
    <div class="px-3 mb-2 mt-2">
        <textarea placeholder="comment" class="w-full bg-gray-100 rounded border border-gray-400 leading-normal resize-none h-20 py-2 px-3 font-medium placeholder-gray-700 focus:outline-none focus:bg-white"></textarea>
    </div>
    <div class="flex justify-end px-4">
        <input type="submit" class="px-2.5 py-1.5 rounded-md text-white text-sm bg-indigo-500" value="Comment">
    </div>
</form>
    </section>

<section >
    <h4 class="font-bold text-gray-900 mt-10 text-xl">Comments</h4>
    
    @foreach ($post->comments as $comment)
        <div class=" mt-2  border px-6 py-4 rounded-lg">
        <div class="flex items-center mb-6">
            <div>
                <div class="text-lg font-medium text-gray-800">{{ $comment->user->first_name . '' . $comment->user->last_name }}</div>
                <div class="text-gray-500">{{ $comment->created_at  }}</div>
            </div>
        </div>
        <p class="text-sm leading-relaxed mb-6">{{ $comment->body }}
        </p>
    </div>
    @endforeach

    
</section>
</x-layout>