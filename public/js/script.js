htmx.on('htmx:responseError', function (event) {
    console.log("Oh snap! Response error!", event.detail.xhr.status);
    switch (event.detail.xhr.status) {
        case 400:
            break;
        case 403:
            break;
        case 404:
            break;
        case 500:
            document.querySelector("html").innerHTML = event.detail.xhr.response;
            break;
    }
});

function scrollToTop() {
    window.scrollTo({
    top: 0,
    behavior: 'smooth'
    });
}

function copyToClipboard(text) {
  if (navigator.clipboard) {
    // Modern asynchronous API
    navigator.clipboard.writeText(text)
      .then(() => {
        console.log('Copied to clipboard successfully!');
      })
      .catch(err => {
        console.error('Failed to copy text: ', err);
      });
  } else {
    // Fallback for older browsers
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';  // Prevent scrolling to bottom
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    try {
      document.execCommand('copy');
      console.log('Copied to clipboard (fallback).');
    } catch (err) {
      console.error('Fallback: Failed to copy', err);
    }
    document.body.removeChild(textarea);
  }
}
