<x-layout >
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
                       {{ $post->comments_count }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $post->tags->pluck('name')->implode(', ') }}
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="/posts/{{ $post->id }}" class="font-medium text-blue-600 text-blue-500 hover:underline">
                            <i class="fa fa-eye" style="font-size:18px"></i></a>
                        <a href="#" class="font-medium text-blue-600 text-blue-500 hover:underline">
                            <i class="fa fa-edit" style="font-size:18px"></i></a>
                                                    <a href="#" class="font-medium text-blue-600 text-blue-500 hover:underline">
                            <i class="fa fa-trash-o" style="font-size:18px"></i></a>
                    </td>
                </tr>
                @endforeach


            </tbody>
        </table>
    </div>
</x-layout>