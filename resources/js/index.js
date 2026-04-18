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
import './components/select2.js';
import './components/tom-select.js';
import './components/tab.js';
// tippy dynamically imported below
import './components/sticksy.js';
// datatables dynamically imported below
import './components/rwd.js';
import './components/pagination.js';
import './components/organizable.js';
import './components/dark.js';
import './components/form.js';
import './components/form-ct.js';
import './components/scroll-indicator.js';
// shakaPlayer dynamically imported below
import './components/file-driver.js';
import './components/imginfo.js';
// tom-select dynamically imported below
import './components/presize.js';
import './components/shareable.js';
import './components/copyer.js';
import './components/ConfirmBox.js';
import './components/autoupdate.js';
// toastify dynamically imported below
import './components/panel-field-card.js';
import Masonry from 'masonry-layout/masonry.js'
import axios from "axios";

let masonry;
document.addEventListener('DOMContentLoaded', async function () {
    // Dynamic Imports (Code Splitting)
    if (document.querySelectorAll('.filepond').length > 0) {
        import('./components/filepond.js').then(() => {
            document.dispatchEvent(new CustomEvent('CG_FILEPOND::init'));
        });
    }
    if (document.querySelectorAll('.shaka-player').length > 0) {
        import('./components/shakaPlayer.js').then(() => {
            document.dispatchEvent(new CustomEvent('CG::Video_init'));
        });
    }
    let datatableImported = false;
    if (document.querySelectorAll('.datatable').length > 0) {
        datatableImported = true;
        import('./components/datatables.js').then(() => {
            document.dispatchEvent(new CustomEvent('CGTABLE::init'));
        });
    }
    // Handle CGTABLE::init fired by dynamic popups when datatables.js hasn't been loaded yet.
    // Once datatables.js is loaded it registers its own listener, so we only need to bootstrap once.
    document.addEventListener('CGTABLE::init', () => {
        if (!datatableImported) {
            datatableImported = true;
            import('./components/datatables.js').then(() => {
                // datatables.js is now loaded and has registered its own CGTABLE::init listener
                document.dispatchEvent(new CustomEvent('CGTABLE::init'));
            });
        }
    });


    import('./components/tippy.js').then(() => {
        document.dispatchEvent(new CustomEvent('CGTIPPYER::init'));
    });
    import('./components/toastify.js');

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
                    
                    masonry.cglocked = true;
                    masonry.layout();
                    setTimeout(() => {
                        masonry.cglocked = false;
                        
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
        
        if (masonry !== undefined && masonry.cglocked === false) {
            
            masonry.cglocked = true;
            masonry.layout();
            setTimeout(() => {
                masonry.cglocked = false;
                
            }, 500);
        }
        e.target.scrollingElement.scrollTopOld = e.target.scrollingElement.scrollTop;
    } else {
        e.target.scrollingElement.scrollTopOld = e.target.scrollingElement.scrollTop;
    }
});
