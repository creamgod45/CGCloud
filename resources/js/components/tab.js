/**
 * 初始化並設置標籤頁按鈕的行為，包括顯示和隱藏對應的標籤內容。
 * 將 document 中所有的元素匹配到類名為 .tab-btn 的節點，並根據其 data-tab 屬性對應的內容來設置各自的 tab 顯示行為。
 * @return {void} 無返回值
 */
function tabLoader() {
    let tabs = document.querySelectorAll(".tab-btn");
    for (let tab of tabs) {
        let tabValue = tab.dataset.tab;
        if (tabValue !== undefined) {
            tab.tab = document.querySelector(tabValue);
        }
        tab.tabs = tabs;
        let show = () => {
            if (tab.tab === null) return false;
            for (let temptab of tab.tabs) {
                temptab.classList.remove("active");
                temptab.tab.classList.add("!hidden");
            }
            tab.classList.add("active");
            tab.tab.classList.remove("!hidden");
        };

        tab.show = show;
        tab.onclick = show;
        tab.hide = function () {
            tab.classList.remove("active");
            tab.tab.classList.add("!hidden");
        };
    }
}

document.addEventListener("DOMContentLoaded", function () {
    tabLoader();
});
