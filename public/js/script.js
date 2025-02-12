htmx.on('htmx:responseError', function (event) {
    console.log("Oh snap! Response error!", evt.detail.xhr.status);
    switch (evt.detail.xhr.status) {
        case 400:
            break;
        case 403:
            break;
        case 404:
            break;
        case 500:
            break;
    }
});
