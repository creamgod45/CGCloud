function panelFieldCard() {
    let panelFieldCards = document.querySelectorAll(".panel-field-card:not(.pfc-rendered)");
    for (const pfc of panelFieldCards) {
        pfc.classList.add("pfc-rendered");
        let addressList = {
            pfc_title: pfc.querySelector(".pfc-title"),
            pfc_tooltip: pfc.querySelector(".pfc-title").tippy,
            pfc_preview: pfc.querySelector(".pfc-preview"),
            pfc_operator: pfc.querySelector(".pfc-operator"),
        };

        pfc.visualRemove = () => {

        };
        pfc.update = () => {

        };
        pfc.clone = (deep = false) => {
            return pfc.cloneNode(deep);
        };
    }
}

function panelFieldCard_update(event) {
    console.log(event);

}

document.addEventListener("DOMContentLoaded", panelFieldCard);
document.addEventListener("CGPFC::update", panelFieldCard_update);
