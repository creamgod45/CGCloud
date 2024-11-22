import tippy, {followCursor} from "tippy.js";

function pagination() {
    let paginations = document.querySelectorAll(".pagination .btn");
    let pagination_btn_active = document.querySelector(".pagination-btn-active");
    if (pagination_btn_active !== null) {
        pagination_btn_active.scrollIntoView({
            behavior: 'smooth', // 平滑滚动
            block: "center"      // 滚动到元素的顶部
        });
    }
    for (let paginationEl of paginations) {
        if (paginationEl.classList.contains("pg-info")) continue;
        let text = paginationEl.innerText + " 頁面";
        if (paginationEl.rel === "prev") {
            text = "上頁";
        }
        if (paginationEl.rel === "next") {
            text = "下頁";
        }
        paginationEl.tippy = tippy(paginationEl, {
            content: "已經點擊 " + text + "。正在載入頁面中...",
            placement: "auto",
            arrow: true,
            animation: "scale",
            theme: 'light',
            followCursor: true,
            hideOnClick: false,
            trigger: 'manual',
            plugins: [followCursor],
            duration: 500,
        });
        paginationEl.onclick = () => {
            if (paginationEl.classList.contains("btn-color1")) {
                paginationEl.tippy.show();
                paginationEl.classList.add("btn-dead");
                paginationEl.classList.remove("btn-color1");
                for (let tpaginationEl of paginations) {
                    tpaginationEl.classList.add("btn-dead");
                    tpaginationEl.classList.remove("btn-color1");
                }
            }
        };
    }
}

document.addEventListener("DOMContentLoaded", function () {
    pagination();
});
