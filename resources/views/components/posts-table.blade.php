<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table id="postsTable" name="postsTable" class="w-full text-sm text-left rtl:text-right text-gray-500 text-gray-400">
        <thead class="text-xs uppercase bg-gray-50 bg-gray-700 text-white">
            <tr>
                <th scope="col" class="px-6 py-3">SN</th>
                <th scope="col" class="px-6 py-3">Blog Title</th>
                <th scope="col" class="px-6 py-3">Author</th>
                <th scope="col" class="px-6 py-3">Comments count</th>
                <th scope="col" class="px-6 py-3">Categories</th>
                <th scope="col" class="px-6 py-3">Published Date</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    // Include CSRF token in AJAX setup
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });

    $('#postsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('posts.data') }}", // Use a string for the AJAX URL
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'author', name: 'author', orderable: false, searchable: false },
            { data: 'comments_count', name: 'comments_count', orderable: false, searchable: false },
            { data: 'tags', name: 'tags', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
