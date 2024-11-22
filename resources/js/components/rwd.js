function system_setting(rwdEl, responsivelist, names) {
    for (let name of names) {
        /**
         * @var {HTMLElement} Element
         */
        if (name === "" || name === null) continue;
        let Element = rwdEl.querySelector(name);
        console.log(Element);
        if (Element !== null) {
            console.log("Element not null");
            if (Element.dataset.status === undefined) {
                Element.status = true;
            } else {
                Element.status = Element.dataset.status === "true";
            }
            if (Element.dataset.hideelement !== undefined) {
                rwdEl.targetEl = Element;
                let hideelement = Element.querySelector(Element.dataset.hideelement);
                hideelement.onclick = function () {
                    Element.classList.add("!hidden");
                    Element.status = false;
                    Element.dataset.status = "false";
                }
            }
            if (Element.dataset.openelement !== undefined) {
                let openelement = Element.querySelector(Element.dataset.openelement);
                openelement.onclick = function () {
                    rwdEl.targetEl.classList.remove("!hidden");
                    rwdEl.targetEl.status = true;
                    rwdEl.targetEl.dataset.status = "true";
                }
            }
        }
    }
}

function rwd() {
    for (let rwdEl of document.querySelectorAll(".rwd")) {
        if (!rwdEl.classList.contains('rwded')) {
            rwdEl.classList.add("rwded");
            let responsivelist = rwdEl.dataset.responsivelist;
            let fn = rwdEl.dataset.fn;
            let names = responsivelist.split(',');
            switch (fn) {
                case "system-setting":
                    system_setting(rwdEl, responsivelist, names);
                    break;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    rwd();
});
