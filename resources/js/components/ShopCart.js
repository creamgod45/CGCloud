import 'sticksy/index.js'

function ShopCart() {
    let SC_scroll_frame = document.querySelectorAll('.Shop-Cart');
    for (let scScrollFrameElement of SC_scroll_frame) {
        let categoryList = scScrollFrameElement.querySelector(".SC-category-list");
        let categoryListTitle = categoryList.querySelector(".SC-category-list-title");
        let checkoutDetails = scScrollFrameElement.querySelector('.SC-checkout-details');

        let address_list = {
            categoryList: categoryList,
            categoryListItems: categoryList.querySelectorAll('.SC-category'),
            categoryListTitle: categoryListTitle,
            switcher: categoryListTitle.querySelector(".switch"),
            checkoutDetails: checkoutDetails,
            freight: checkoutDetails.querySelector(".SC-freight"),
            totalMoney: checkoutDetails.querySelector(".SC-total-money"),
            sendToLine: checkoutDetails.querySelector(".SC-send-to-Line"),
            PrintDetails: checkoutDetails.querySelector(".SC-print-details"),
            items: [],
            categoryItems: {},
            categoryTemplateHTML: categoryList.querySelector('.SC-category-template').innerHTML,
            nocategoryTemplateHTML: categoryList.querySelector('.SC-nocategory-template').innerHTML,
        };

        scScrollFrameElement.addEventListener('scroll', function (e) {
            if (window.innerWidth > 768) {
                let clientHeight = address_list.categoryList.getClientRects()[0].height - address_list.checkoutDetails.getClientRects()[0].height;
                if (clientHeight >= e.target.scrollTop) {
                    address_list.checkoutDetails.style.top = `${e.target.scrollTop}px`;
                } else {
                    address_list.checkoutDetails.style.top = `${clientHeight}px`;
                }
            } else {
                address_list.checkoutDetails.style.top = "";
            }
        });

        let categoryList1 = address_list.categoryListItems;
        for (let categoryList1Element of categoryList1) {
            let summary = categoryList1Element.querySelector('.summary');
            let items = categoryList1Element.querySelector('.SC-category-items');
            if (items !== null && summary !== null) {
                address_list.categoryItems[summary.dataset.category] = items.querySelectorAll(".SC-sub-category-item");
            }
            if (items !== null) {
                for (let item of items.querySelectorAll(".SC-sub-category-item")) {
                    address_list.items.push(item);
                }
            }
        }

        address_list.switcher.on('click', function (e) {
            let querySelectorAll = scScrollFrameElement.querySelectorAll('.summary');
            if (e.detail.value) {
                for (let a of querySelectorAll) {
                    a.classList.add('!hidden');
                }
                address_list.categoryListItems[0].querySelector(".SC-sub-category-footer").classList.add('!hidden');
                address_list.categoryListItems[1].querySelector(".SC-sub-category-head").classList.add('!hidden');
            } else {
                for (let a of querySelectorAll) {
                    a.classList.remove('!hidden');
                }
                address_list.categoryListItems[0].querySelector(".SC-sub-category-footer").classList.remove('!hidden');
                address_list.categoryListItems[1].querySelector(".SC-sub-category-head").classList.remove('!hidden');
            }
        });

        //console.log(address_list);
        scScrollFrameElement.address_list = address_list;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    ShopCart();
});
