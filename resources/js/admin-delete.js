$(document).on("click", ".delete-admin", function () {
    const id = $(this).data("id");
    if (confirm("Are you sure you want to delete this admin?")) {
        $.ajax({
            url: `/admins/${id}`,
            type: "DELETE",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#admins-table").DataTable().ajax.reload();
                }
            },
        });
    }
});
