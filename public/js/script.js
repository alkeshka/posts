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
});