function form() {
    for (let forms of document.querySelectorAll('.form-common')) {
        forms.onkeydown = (e) => {
            if (e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        };
    }
}

document.addEventListener('DOMContentLoaded', form);
