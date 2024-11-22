function popover() {
    for (let el of document.querySelectorAll('.popover')) {
        console.log(el);
        let closebtn = null;
        for (let child of el.children) {
            //console.log(child);
            if (child.classList.contains('popover-closebtn')) {
                closebtn = child;
            }
        }
        if (closebtn !== null) {
            closebtn.onclick = () => {
                el.hidePopover();
            };
        }
    }
}

document.addEventListener('DOMContentLoaded', popover);
