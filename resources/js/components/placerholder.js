const init = new CustomEvent('Placeholderinit', {
    detail: {
        message: "init all components"
    },
    cancelable: false
});

function placerholder() {
    for (let el of document.querySelectorAll('.placeholder-ct')) {
        if(!el.classList.contains("placeholdered-ct")) {
            if (el.dataset.placeholderdelay !== null) {
                setTimeout(() => {
                    el.classList.remove('placeholder');
                    el.classList.remove('placeholder-circle');
                    el.classList.remove('placeholder-16-9');
                    el.classList.remove('placeholder-full-wh');
                    el.classList.add("placeholdered-ct");
                    document.dispatchEvent(init);
                }, Number.parseInt(el.dataset.placeholderdelay));
            } else {
                setTimeout(() => {
                    el.classList.remove('placeholder');
                    el.classList.remove('placeholder-circle');
                    el.classList.remove('placeholder-16-9');
                    el.classList.remove('placeholder-full-wh');
                    el.classList.add("placeholdered-ct");
                    document.dispatchEvent(init);
                }, 1000);
            }
        }
    }
}

document.addEventListener('CGPLACEHOLDER::init', () => {
    placerholder()
});
document.addEventListener('DOMContentLoaded', () => {
    placerholder()
});
