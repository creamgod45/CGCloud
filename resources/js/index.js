//import './components/Pusher.js'
import jQuery from 'jquery';
import '@fortawesome/fontawesome-free/js/all.js';
import 'ion-rangeslider/js/ion.rangeSlider.js';
import 'sticksy/index.js';
//import 'select2';
import './components/tooltip.js';
import './components/lazyImageLoader.js';
import './components/placerholder.js';
import './components/menu.js';
import './components/button.js';
//import './components/notification.js';
import './components/customTrigger.js';
import './components/select-bar-query.js';
//import './components/carousels.js';
import './components/phone.js';
import './components/popover.js';
import './components/switch.js';
import './components/dialog.js';
//import './components/ShopItem.js';
//import './components/ShopCart.js';
import './components/validate.js';
import './components/filepond.js';
//import './components/select2.js';
import './components/tab.js';
import './components/tippy.js';
import './components/sticksy.js';
import './components/datatables.js';
import './components/rwd.js';
import './components/pagination.js';
import './components/organizable.js';
import './components/dark.js';
import './components/form.js';
import './components/form-ct.js';
import './components/scroll-indicator.js';
//import './components/dashvideo.js';
import './components/videoJs.js';
import './components/file-driver.js';
import './components/imginfo.js';
import './components/tom-select.js';
import './components/presize.js';
import './components/shareable.js';
import './components/copyer.js';
import './components/ConfirmBox.js';
import './components/autoupdate.js';
import './components/toastify.js';
import './components/panel-field-card.js';
import Masonry from 'masonry-layout/masonry.js'
import axios from "axios";

let masonry;
document.addEventListener('DOMContentLoaded', async function () {
    let element = document.querySelector('.panel-field-list');
    if(element !== null){
        setTimeout(() => {
            masonry = new Masonry('.panel-field-list', {
                itemSelector: '.panel-field-card',
                percentPosition: true,
                gutter: 12,
            });
            masonry.cglocked = false;
            element.classList.remove('placeholder');

            // 創建一個 MutationObserver 實例來監聽 DOM 變化
            const observer = new MutationObserver((mutations) => {
                // 當 DOM 變化時重新布局
                if (masonry !== undefined && masonry.cglocked === false) {
                    console.log('layouted');
                    masonry.cglocked = true;
                    masonry.layout();
                    setTimeout(() => {
                        masonry.cglocked = false;
                        console.log('layout again');
                    }, 500);
                }
            });

            // 配置 observer 監聽子節點的變化和屬性變化
            observer.observe(element, {
                childList: true,  // 監聽子節點的添加或移除
                subtree: true,    // 監聽所有後代元素
                attributes: true  // 監聽屬性變化
            });

        }, 2000);
    }
});

window.addEventListener('scroll', function (e) {
    if(e.target.scrollingElement.scrollTop  >= (e.target.scrollingElement?.scrollTopOld ?? 0) + 100){
        console.log('scroll');
        if (masonry !== undefined && masonry.cglocked === false) {
            console.log('layouted');
            masonry.cglocked = true;
            masonry.layout();
            setTimeout(() => {
                masonry.cglocked = false;
                console.log('layout again');
            }, 500);
        }
        e.target.scrollingElement.scrollTopOld = e.target.scrollingElement.scrollTop;
    } else {
        e.target.scrollingElement.scrollTopOld = e.target.scrollingElement.scrollTop;
    }
});
