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
    // Include CSRF token in AJAX setup if needed
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $('#postsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('posts.data') }}",
            data: function(d) {
                d.noOfComments = $('#noOfComments').val();
                d.searchQuery = $('#searchQuery').val();
                d.category = $('#category').val();
                d.author = $('#author').val();
            }
        },
        columns: [
            { data: 'id', name: 'id', searchable: false, },
            { data: 'title', name: 'title', searchable: false },
            { data: 'author', name: 'author', orderable: false, searchable: false, class: 'capitalize'},
            { data: 'comments_count', name: 'comments_count', orderable: false, searchable: false },
            { data: 'tags', name: 'tags', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        dom: '<"top"b>rt<"bottom"lp><"clear">',
    });

    // Event listener to redraw DataTable on input change
    $('#noOfComments, #searchQuery, #category, #author').on('keyup change', function() {
        table.draw();
    });
});
</script>
