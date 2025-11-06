// request to server
function fnMakeRequest(
    url,
    method,
    data,
    data_type = "json",
    submit_form = false
) {
    return new Promise((resolve, reject) => {
        let ajaxOptions = {
            type: method,
            url: url,
            dataType: data_type,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: data,
            success: function (response) {
                resolve(response);
            },
            error: function (error) {
                reject(error);
            },
        };
        // Set processData and contentType based on data_type
        if (data_type === "json") {
            ajaxOptions.processData = true; // Process the data (default behavior)
            ajaxOptions.contentType = "application/json"; // Set content type to JSON
            ajaxOptions.data = JSON.stringify(data); // Convert data to JSON string
        } else {
            ajaxOptions.processData = false; // Don't process the data
            ajaxOptions.contentType = false; // Set content type to false (default behavior)
        }

        if (submit_form) {
            ajaxOptions.enctype = "multipart/form-data";
        }
        $.ajax(ajaxOptions);
    });
}

// Reload Table Data
function fnReloadTable(table, url, method, payload) {
    $.ajax({
        url: url,
        type: method,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: payload,
        success: function (response) {
            if (response.status) {
                data_source = response.data;

                table.clear();
                table.rows.add(data_source);
                table.draw();
                setTimeout(() => {
                    table.columns.adjust().draw();
                }, 250);
            } else {
                alert("Error Update Table.\n" + response.msg);
            }
        },
        error: function (xhr, status, error) {
            // Handle the error
            console.log("AJAX Error:", status, error);
            alert("Error Update Table.\n" + error);
        },
    });
}
