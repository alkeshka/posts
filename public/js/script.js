function loadComments() {

    const modalContent = $("#modalContent");
    modalContent.html("");

    const postId = arguments[0];

    $('#modal').removeClass('hidden');
    $.ajax({
    url: "/comments/" + postId,
    type: "GET",
    dataType: "json",
    success: function(data) {
        if (data.length === 0) {
            const noCommentsHTML = `
                    <div class="mt-1">
                        <span class="block text-sm font-medium text-gray-700">
                            No comments for the selected post!
                        </span>
                    </div>`;
            modalContent.append(noCommentsHTML);
        } else {
            $.each(data, function(index, comment) {
                const commentHTML = `
                    <div class="mt-1">
                        <span class="block text-sm font-medium text-gray-700">
                            ${comment.user.first_name + " " + comment.user.last_name}
                        </span>
                        <p class="text-sm">${comment.body}</p>
                        <div class="border-b block border-gray-900/10 py-1"></div>
                    </div>`;

                $("#modalContent").append(commentHTML);
            });
        }
        $('#modal').removeClass('hidden');
    },
    error: function(jqXHR, textStatus, errorThrown) {
        alert("There was an error fetching data!");
    }
    });

}

$(document).ready(function () {
    $('#category').select2({
        ajax: {
            url: '/tags', // Replace '/tags' with your route for fetching tags
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (tag) {
                        return {
                            id: tag.id,
                            text: tag.name
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Select tags',
        minimumInputLength: 0, // Allows searching with no minimum input length
        multiple: true // Enable multi-select
    });

    function closeModal() {
        $('#modal').addClass('hidden');
    }

    $('#closeModalButton').on('click', function () {
        closeModal();
    });

    $('#modal').on('click', function (event) {
        if ($(event.target).is('#modal')) {
            closeModal();
        }
    });

    var startDate;
    var endDate;

    // Initialize DataTable
    var table = $('#postsTable').DataTable({
        scrollY: false,
        scrollX: false,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: $('#postsTable').data('url'),
            data: function (d) {
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
        dom: '<"top"b>rt<"bottom"lp><"clear">',
        language: {
            emptyTable: "No posts available for the selected criteria" // Custom message
        }
    });

    // Event listener to redraw DataTable on input change
    $('#noOfComments, #searchQuery, #category, #author').on('keyup change', function () {
        table.draw();
    });

    // Initialize Date Range Picker
    $('input[name="publishedDateRange"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false // To prevent the default update
    }, function (start, end, label) {
        startDate = start;
        endDate = end;
        table.draw();
    });

    // Update the input fields with the selected date range
    $('input[name="publishedDateRange"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    // Clear the input fields when canceling the date picker
    $('input[name="publishedDateRange"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        startDate = null;
        endDate = null;
        table.draw();
    });
});
