<x-layout >
<x-post-filters></x-post-filters>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg ">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 text-gray-400">
            <thead class="text-xs uppercase bg-gray-50 bg-gray-700 text-white">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        SN
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Blog Title
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Auther
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Comments count
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Categories
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Published Date
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr class="bg-white border-b bg-gray-800 border-gray-700 ">
                        <td class="px-6 py-4">
                            {{ $loop->index + 1 }}
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $post->title }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $post->user->first_name . ' ' . $post->user->last_name  }}
                        </td>
                        <td class="px-6 py-4">
                           <button onclick="loadComments({{ $post->id }})">
                                {{ $post->comments_count }}
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            {{ $post->tags->pluck('name')->implode(', ') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $post->created_at->format('d / m / Y') }}
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="/posts/{{ $post->id }}" class="font-medium text-blue-600 text-blue-500 hover:underline">
                                <i class="fa fa-eye" style="font-size:18px"></i></a>
                            @auth

                                @if ( Auth::id() == $post->user_id )
                                    <a href="/posts/{{ $post->id }}/edit" class="font-medium text-blue-600 text-blue-500 hover:underline">
                                        <i class="fa fa-edit" style="font-size:18px"></i>
                                    </a>
                                @endif
                            
                                @if ( Auth::user()->users_role_id == 1 )
                                    <div class="inline-block">
                                        <form method="post" action="/posts/{{ $post->id }}/delete">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-500"><i class="fa fa-trash-o" style="font-size:18px"></i></button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-layout>
<x-modal></x-modal>