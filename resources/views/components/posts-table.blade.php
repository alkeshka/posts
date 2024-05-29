<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table id="postsTable" name="postsTable" class="w-full text-sm text-left rtl:text-right text-gray-500 text-gray-400" data-url="{{ route('posts.data') }}">
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

    var startDate;
    var endDate;

    // Initialize DataTable
    var table = $('#postsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: $('#postsTable').data('url'),
            data: function(d) {
                d.noOfComments = $('#noOfComments').val();
                d.searchQuery = $('#searchQuery').val();
                d.category = $('#category').val();
                d.author = $('#author').val();
                d.publishedDateRangeStart = startDate ? startDate.format('YYYY-MM-DD') : null;
                d.publishedDateRangeEnd = endDate ? endDate.format('YYYY-MM-DD') : null;
            }
        },
        columns: [
            { data: 'id', name: 'id', searchable: false },
            { data: 'title', name: 'title', searchable: false, orderable: true },
            { data: 'author', name: 'author', orderable: true, searchable: false, class: 'capitalize' },
            { data: 'comments_count', name: 'comments_count', orderable: true, searchable: false },
            { data: 'tags', name: 'tags', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at', orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        dom: '<"top"b>rt<"bottom"lp><"clear">'
    });

    // Event listener to redraw DataTable on input change
    $('#noOfComments, #searchQuery, #category, #author').on('keyup change', function() {
        table.draw();
    });

    // Initialize Date Range Picker
    $('input[name="publishedDateRange"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false // To prevent the default update
    }, function(start, end, label) {
        startDate = start;
        endDate = end;
        table.draw();
    });

    // Update the input fields with the selected date range
    $('input[name="publishedDateRange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    // Clear the input fields when canceling the date picker
    $('input[name="publishedDateRange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        startDate = null;
        endDate = null;
        table.draw();
    });
});
</script>
