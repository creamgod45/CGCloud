function IMG() {
    /**
     * @type {NodeListOf<HTMLImageElement>}
     */
    let imginfos = document.querySelectorAll('.imginfo');
    for (let imginfo of imginfos) {
        imginfo.dataset.width = imginfo.naturalWidth;
        imginfo.dataset.height = imginfo.naturalHeight;
    }
}

document.addEventListener('DOMContentLoaded', IMG);
