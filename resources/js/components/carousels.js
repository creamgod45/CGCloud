import Drift from 'drift-zoom';
import PhotoViewer from 'photoviewer';

/**
 * 初始化輪播圖元件，並設定相關事件監聽器及屬性。
 *
 * @return {void} 無返回值
 */
function carousels() {
    let carousels = document.querySelectorAll(".carousels");
    for (let carousel of carousels) {
        let address_list = {
            ImageViewFrameField: carousel.children[0],
            ImageViewField: carousel.children[0].children[0],
            ImageViewInfoBoxField: carousel.children[0].children[1],
            ImageViewTitleField: carousel.children[0].children[1].children[1],
            ImageViewSubtitleField: carousel.children[0].children[1].children[2],
            ImageViewInfoBoxCloseBtnField: carousel.children[0].children[1].children[3],
            ControlFrameField: carousel.children[1],
            ControlZoomBtnField: carousel.children[1].children[0],
            ControlZoomChunkField: carousel.children[1].children[1],
            ControlImagesListField: carousel.children[1].children[2],
            ControlLeftBtnField: carousel.children[1].children[3],
            ControlRightBtnField: carousel.children[1].children[4],
            ImagesList: [],
        };
        address_list.ImageViewInfoBoxCloseBtnField.addEventListener("click", function () {
            if (address_list.ImageViewInfoBoxField.dataset.on === "true") {
                address_list.ImageViewInfoBoxField.classList.remove("!h-32", "!w-full", "!overflow-auto", "!bottom-0", "!left-0", "p-5");
                setTimeout(function () {
                    address_list.ImageViewInfoBoxField.dataset.on = "false";
                }, 200);
            }
        });

        address_list.ImageViewInfoBoxField.addEventListener("click", function () {
            if (address_list.ImageViewInfoBoxField.dataset.on === "false") {
                address_list.ImageViewInfoBoxField.classList.add("!h-32", "!w-full", "!overflow-auto", "!bottom-0", "!left-0", "p-5");
                setTimeout(function () {
                    address_list.ImageViewInfoBoxField.dataset.on = "true";
                }, 200);
            }
            for (let child of address_list.ImageViewInfoBoxField.children) {
                if (child.classList.contains("carousel-info-icon")) {
                    continue;
                }
                child.classList.toggle("!hidden");
            }
        });
        let Images = [];
        let index = 0;
        for (let Image of carousel.children[1].children[2].children) {
            let item = {
                index: index,
                title: Image.dataset.title,
                subtitle: Image.dataset.subtitle,
                src: Image.dataset.src,
                object: Image,
            };
            index++;
            Images.push(item);
            ImageInit(Image, address_list, item);
        }
        address_list.ImagesList = Images;
        address_list.ControlLeftBtnField.onclick = () => prev(address_list);
        address_list.ControlRightBtnField.onclick = () => next(address_list);
        address_list.ControlZoomChunkField.Drift = new Drift(address_list.ImageViewField, {
            paneContainer: address_list.ControlZoomChunkField,
            inlinePane: 0,
            sourceAttribute: 'data-src',
            handleTouch: true,
            onShow: () => {
                if (window.innerWidth <= 375)
                    address_list.ControlZoomChunkField.Drift.disable();
                else
                    address_list.ControlZoomChunkField.Drift.enable();
            },
        });
        address_list.ImageViewField.ontouchmove = function (event) {
            if (window.innerWidth <= 375) return false;
            address_list.ControlZoomChunkField.classList.add("!fixed");
            address_list.ControlZoomChunkField.classList.remove("hidden")
            address_list.ControlZoomChunkField.style.top = 16;
            address_list.ControlZoomChunkField.style.left = 16;
        };
        address_list.ImageViewField.ontouchend = function () {
            address_list.ControlZoomChunkField.classList.add("hidden")
        };
        address_list.ImageViewField.onmousemove = (event) => {
            if (window.innerWidth <= 375) return false;
            address_list.ControlZoomChunkField.classList.remove("hidden", "!fixed")
            address_list.ControlZoomChunkField.style.top = (event.offsetY - 450);
            address_list.ControlZoomChunkField.style.left = (event.offsetX - 50);
        };
        address_list.ImageViewField.onmouseleave = () => {
            address_list.ControlZoomChunkField.classList.add("hidden");
        };
        window.addEventListener("resize", function () {
            if (window.innerWidth <= 375)
                address_list.ControlZoomChunkField.Drift.disable();
            else
                address_list.ControlZoomChunkField.Drift.enable();
        });

        address_list.ControlZoomBtnField.onclick = () => {
            let options = {
                i18n: {
                    minimize: '最小化',
                    maximize: '最大化',
                    close: '關閉',
                    zoomIn: '放大(+)',
                    zoomOut: '縮小(-)',
                    prev: '上一個(←)',
                    next: '下一個(→)',
                    fullscreen: '全螢幕',
                    actualSize: '實際大小(Ctrl+Alt+0)',
                    rotateLeft: '向左旋轉(Ctrl+,)',
                    rotateRight: '向右旋轉(Ctrl+.)',
                },
                initMaximized: true,
                index: parseInt(address_list.ImageViewField.dataset.index),
                zIndex: 999999,
                initModalPos: {
                    top: 0,
                    left: 0,
                },

            };
            new PhotoViewer(address_list.ImagesList, options);
        };
        carousel.address_list = address_list;
    }
}

/**
 * 更新圖片顯示區域的圖片及相關資訊
 *
 * @param {Object} address_list 包含圖片顯示區域各個 HTML 元素的對象
 * @param {Object} itemObj 包含圖片相關資訊的對象，包括索引、來源、標題和副標題
 * @return {void}
 */
function changeImage(address_list, itemObj) {
    address_list.ImageViewField.dataset.index = itemObj.index;
    address_list.ImageViewField.dataset.src = itemObj.src;
    address_list.ImageViewField.style.backgroundImage = `url(${itemObj.src})`;
    address_list.ImageViewTitleField.innerText = itemObj.title;
    address_list.ImageViewSubtitleField.innerText = itemObj.subtitle;
}

/**
 * 切換到圖片列表中的上一張圖片。
 *
 * @param {Object} address_list - 包含圖片資料和顯示字段的對象。
 * @param {Array} address_list.ImagesList - 圖片地址列表。
 * @param {HTMLElement} address_list.ImageViewField - 目前顯示圖片的 DOM 元素，包含 data-index 屬性。
 * @return {void}
 */
function prev(address_list) {
    let imagesList = address_list.ImagesList;
    if (imagesList.length > 0) {
        let index = parseInt(address_list.ImageViewField.dataset.index);
        if (imagesList.hasOwnProperty(--index)) {
            let itemObj = imagesList[index];
            changeImage(address_list, itemObj);
            itemObj.object.scrollIntoView({behavior: 'smooth', block: 'nearest'});
            setTimeout(() => createRipple(itemObj.object), 400);
        }
    }
}

/**
 * 創建水波效果。
 *
 * @param {HTMLElement} el - 要應用水波效果的元素。
 * @return {void} 此方法不返回任何值。
 */
function createRipple(el) {
    //console.log(event)
    const button = el;
    const circle = document.createElement("span");
    circle.classList.add("ripple");
    button.appendChild(circle);

    let b = button.getBoundingClientRect();
    const diameter = Math.max(b.width, b.height);
    const radius = diameter / 2;

    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = "0px";
    circle.style.top = "0px";

    setTimeout(() => {
        circle.remove();
    }, 600)
}

/**
 * 更改並顯示下一張圖片，若圖片存在則平滑滾動到該圖片並創建漣漪效果。
 *
 * @param {Object} address_list 包含圖像列表和視圖欄位數據的對象
 * @param {Array} address_list.ImagesList 圖片地址列表
 * @param {HTMLElement} address_list.ImageViewField 圖片顯示區域的HTML元素
 * @returns {void} 無返回值
 */
function next(address_list) {
    let imagesList = address_list.ImagesList;
    if (imagesList.length > 0) {
        let index = parseInt(address_list.ImageViewField.dataset.index);
        if (imagesList.hasOwnProperty(++index)) {
            let itemObj = imagesList[index];
            changeImage(address_list, itemObj);
            itemObj.object.scrollIntoView({behavior: 'smooth', block: 'nearest'});
            setTimeout(() => createRipple(itemObj.object), 400);
        }
    }
}

/**
 * 初始化圖片元素，並設定點擊事件。
 *
 * @param {HTMLImageElement} Image - 要初始化的圖片元素。
 * @param {Array<string>} address_list - 圖片地址列表。
 * @param {Object} itemObj - 圖片相關的物件。
 * @return {void} 這個函數沒有返回值。
 */
function ImageInit(Image, address_list, itemObj) {
    Image.onclick = () => {
        changeImage(address_list, itemObj);
    };
}

document.addEventListener('DOMContentLoaded', carousels);
document.addEventListener('LoadCarousels', carousels);
