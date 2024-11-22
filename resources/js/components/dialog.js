/**
 * 處理所有具有 'dialog' 和 'dialog-frame' 類別的對話方塊和框架元素，並添加關閉按鈕的功能。
 * 此方法會找到每個對話框元素中的關閉按鈕並給予 onclick 事件，以便在關閉對話框時恢復滾動條。
 *
 * @return {void} 此方法不返回任何值
 */
function dialog() {
    for (let el of document.querySelectorAll('.dialog')) {
        //console.log(el);
        let closebtn = null;
        for (let child of el.children) {
            //console.log(child);
            let children1 = child.children;
            for (let child2 of children1) {
                if (child2.classList.contains('dialog-closebtn')) {
                    closebtn = child;
                    break;
                }
            }
            if (closebtn !== null) break;
        }
        if (closebtn !== null) {
            closebtn.onclick = () => {
                document.body.style.overflow = "";
                el.close();
            };
        }
    }
    for (let el of document.querySelectorAll('.dialog-frame')) {
        //console.log(el);
        let closebtn = null;
        for (let child of el.children) {
            //console.log(child);
            if (child.classList.contains('dialog-vt')) {
                closebtn = el.querySelector(".dialog-closebtn");
            }
        }
        if (closebtn !== null) {
            closebtn.onclick = () => {
                document.body.style.overflow = "";
                el.classList.add("!hidden");
                if (el.dataset.status !== undefined) {
                    el.dataset.status = "off";
                }
            };
        }
    }
}

document.addEventListener('DOMContentLoaded', dialog);
