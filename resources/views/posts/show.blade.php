<x-layout header="{{ $post->title }}" >
    <x-breadcrumb breadcrumbsName="view" :model="$post" />

    @if (session('status'))
        <x-flash-messages message="{{ session('status')['message'] }}" class="{{ session('status')['type'] }}" ></x-flash-messages>
    @endif


    <section >
        <div>
            @if(isset($post->thumbnail))
                <div>
                    <img src="{{ asset('storage/'. $post->thumbnail)}}" alt="post_image" class="mb-4">
                </div>
            @endif
            {{ $post->body }}
        </div>
    </section>

    <section>
        <div>
            <h4 class="font-bold text-gray-900 mt-10 text-xl">Categories</h4>
            <div class="mt-2">
                @foreach ($post->tags as $category)
                    <a href="/tags/{{ $category->id }}">
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>

        </div>
    </section>

    @auth
        <section>
            <x-forms.comment :postId="$post->id" action="/comments" method="POST" />
        </section>
    @endauth

    <section >
        <h4 class="font-bold text-gray-900 mt-10 text-xl">Comments</h4>
        @foreach ($post->comments as $comment)
            <div class=" mt-2  border px-6 py-4 rounded-lg">
                <div class="flex items-center mb-6">
                    <div>
                        <div class="text-lg font-medium text-gray-800">{{ $comment->user->first_name . ' ' . $comment->user->last_name }}</div>
                        <div class="text-gray-500">{{ $comment->created_at  }}</div>
                    </div>
                </div>
                <p class="text-sm leading-relaxed ">{{ $comment->body }}</p>
                @auth
                    @if ($comment->user->id === Auth::user()->id || Auth::user()->users_role_id === 1)
                        <div class="flex justify-end">
                            <form method="post" action="/comments/{{ $comment->id }}/delete">
                                @csrf
                                <button onclick="return confirm('Are you sure?')" type="submit" class="text-red-500">Delete</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @endforeach
    </section>

</x-layout>
