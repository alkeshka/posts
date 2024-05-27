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

        $('#modal').removeClass('hidden');
    },
    error: function(jqXHR, textStatus, errorThrown) {
        alert("There was an error fetching data!");
    }
    });

}

$(document).ready(function() {

    $('#closeModalButton').on('click', function() {
        $('#modal').addClass('hidden');
    });

    $(window).on('click', function(event) {
        var modal = $('#modal');
        if ($(event.target).is(modal)) {
            modal.addClass('hidden');
        }
    });

    $('#author, #category, #noOfComments, #publishedDate, #searchQuery').on('change', function() {

        const tableBody = $("#tableBody");

        var author = $('#author').val();
        var category = $('#category').val();
        var noOfComments = $('#noOfComments').val();
        var publishedDate = $('#publishedDate').val();
        var searchQuery = $('#searchQuery').val();

        const isLoggedIn = $('#isLoggedIn').val();
        const userId = $('#userId').val();
        const userRoleId = $('#userRoleId').val();

        $.ajax({
            url: "/filter" ,
            type: "POST",
            dataType: "json",
            data: {
                "_token": $('#token').val(),
                'author': author,
                'category': category,
                'noOfComments': noOfComments,
                'publishedDate': publishedDate,
                'searchQuery': searchQuery
            },
            success: function(data) {
                tableBody.html("");
                $.each(data, function(index, post) {

                    const tagNames = post.tags.map(tag => tag.name);
                    const tagNamesString = tagNames.join(", ");

                    const commentHTML = `
                    <tr class="bg-white border-b bg-gray-800 border-gray-700 ">
                        <td class="px-6 py-4">
                            ${ index + 1 }
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            ${ post.title }
                        </th>
                        <td class="px-6 py-4">
                            ${ post.user.first_name + " " + post.user.last_name }
                        </td>
                        <td class="px-6 py-4">
                           <button onclick="loadComments({{ $post->id }})">
                                ${ post.comments_count }
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            ${ tagNamesString }
                        </td>
                        <td class="px-6 py-4">
                            ${ post.created_at }
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="/posts/${ post.id }" class="font-medium text-blue-600 text-blue-500 hover:underline">
                                <i class="fa fa-eye" style="font-size:18px"></i></a>
                                ${ isLoggedIn ?
                            ` ${ userId == post.user_id || userRoleId == 1 ? `
                                    <a href="/posts/${ post.id }/edit" class="font-medium text-blue-600 text-blue-500 hover:underline">
                                        <i class="fa fa-edit" style="font-size:18px"></i>
                                    </a>` : ''}

                                        ${ userRoleId == 1  ? `
                                    <a onclick="return confirm('Are you sure?')" href="/posts/${ post.id }/delete" class="font-medium text-blue-600 text-blue-500 hover:underline">
                                        <i class="fa fa-trash-o text-red-500" style="font-size:18px"></i>
                                    </a>` : '' }

                                    ` : '' }
                        </td>
                    </tr>`;
                    tableBody.append(commentHTML);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("There was an error fetching data!");
            }
        });

    });
});
