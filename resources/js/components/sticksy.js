import 'sticksy/index.js';

function sticksyLoader() {
    let sticksys = document.querySelectorAll(".sticksy");
    for (let sticksy of sticksys) {
        let topspacing = sticksy.dataset.topspacing;
        let listen = sticksy.dataset.listen;
        if (topspacing === undefined) topspacing = "0";
        if (listen === undefined) listen = true;
        sticksy.sticksy = new Sticksy(sticksy, {
            topSpacing: parseInt(topspacing),
            listen: listen,
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    sticksyLoader();
});
