function popover() {
    for (let el of document.querySelectorAll('.popover')) {
        
        let closebtn = null;
        for (let child of el.children) {
            
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
