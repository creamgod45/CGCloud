function presize() {
    console.log("DOMContentLoaded");
    /**
     * @type {NodeListOf<HTMLImageElement>}
     */
    let persizes = document.querySelectorAll(".presize");
    for (let persize of persizes) {
        if(persize.classList.contains("presized")) continue;
        let size = persize.dataset.presize;
        let height = persize.dataset.preheight;
        let width = persize.dataset.prewidth;
        let delay = persize.dataset.delay || 1000;

        if(size !== undefined) {
            persize.style.height = size;
            persize.style.width = size;
            persize.classList.add("presized");
        } else {
            if(height !== undefined) {
                persize.style.height = height;
            }

            if(width !== undefined) {
                persize.style.width = width;
            }
            persize.classList.add("presized");
        }
        setTimeout(() => {
            persize.style.height = '';
            persize.style.width = '';
        }, delay);
    }
}

document.addEventListener('DOMContentLoaded', presize);
