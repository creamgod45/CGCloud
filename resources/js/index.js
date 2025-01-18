//import './components/Pusher.js'
import jQuery from 'jquery';
import '@fortawesome/fontawesome-free/js/all.js';
import 'ion-rangeslider/js/ion.rangeSlider.js';
import 'sticksy/index.js';
import 'select2';
import './components/tooltip.js';
import './components/lazyImageLoader.js';
import './components/placerholder.js';
import './components/menu.js';
import './components/button.js';
import './components/notification.js';
import './components/customTrigger.js';
import './components/select-bar-query.js';
import './components/carousels.js';
import './components/phone.js';
import './components/popover.js';
import './components/switch.js';
import './components/dialog.js';
import './components/ShopItem.js';
import './components/ShopCart.js';
import './components/validate.js';
import './components/filepond.js';
import './components/select2.js';
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
import './components/dashvideo.js';
import './components/videoJs.js';
import './components/file-driver.js';
import './components/imginfo.js';
import './components/tom-select.js';
import './components/presize.js';
import './components/shareable.js';
import './components/copyer.js';
import Masonry from 'masonry-layout/masonry.js'
import axios from "axios";

document.addEventListener('DOMContentLoaded', async function () {
    if(document.querySelector('.panel-field-list') !== null){
        var masonry = new Masonry('.panel-field-list', {
            itemSelector: '.panel-field-card',
            percentPosition: true,
            gutter: 12,
        });
    }

});
