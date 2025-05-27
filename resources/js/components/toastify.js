import Toastify from 'toastify-js'

document.addEventListener('CGTOASTIFY::notice', function (e) {
    console.log(e);

    if(e.detail.message === null) return;
    Toastify({
        text: e.detail.message,
        avatar: e.detail.avatar ?? "",
        className: e.detail.className ?? "info",
        duration: e.detail.duration ?? 3000,
        destination: e.detail.destination ?? undefined,
        newWindow: e.detail.newWindow ?? true,
        close: e.detail.closeable ?? true,
        gravity: e.detail.gravity ?? "bottom", // `top` or `bottom`
        position:  e.detail.position ?? "right", // `left`, `center` or `right`
        stopOnFocus: e.detail.stopOnFocus ?? true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(120deg, #f6d365 0%, #fda085 100%)",
        },
        onClick: e.detail.onClickCallBack // Callback after click

    }).showToast();
});
