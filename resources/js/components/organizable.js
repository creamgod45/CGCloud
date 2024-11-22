import {Sortable} from '@shopify/draggable';
import * as Utils from './utils.js';

function organizable() {
    /**
     * @type {NodeListOf<HTMLElement>}
     */
    let organizables = document.querySelectorAll(".organizable-panel");
    for (let organizableParentEl of organizables) {
        let organizables = organizableParentEl.querySelectorAll(".organizable");
        let ItemAddressList = [];
        for (let organizableEl of organizables) {
            let address = {
                self: organizableEl,
                point: organizableEl.querySelector(".organizable-point"),
                remove: organizableEl.querySelector(".organizable-delete"),
                title: organizableEl.querySelector(".title"),
            };
            address.remove.onclick = () => {
                let a = Utils.confirmDialog("請確認是否要繼續此操作?", "刪除此 " + address.title.innerText + " 選單? (送出更新後才會更新，如果操作錯誤請重新整理頁面)", "info", function () {
                    address.self.remove();
                }, function () {

                });
                console.log(a);
            };
            ItemAddressList.push(address);
        }
        const draggable = new Sortable(organizableParentEl, {
            draggable: ".organizable",
            handle: '.organizable-point',
            delay: 100,
            classes: {
                'draggable:dragging': 'organizable--is-dragging',
                'draggable:over': 'organizable--is-over',
                'draggable:mirror': 'organizable--mirror',
            },
            scrollable: {
                speed: 20, // 滾動速度
                sensitivity: 30, // 靈敏度
            },
            // 您可以在此處添加其他選項
        });
        organizableParentEl.addressList = {
            organizableParentEl: organizableParentEl,
            organizableParentElDraggable: draggable,
            organizables: organizables,
            organizablesItemAddressList: ItemAddressList,
        };

        draggable.on('sortable:stop', (event) => {
            const items = Array.from(event.newContainer.children);
            const order = items.map((item) => {
                return {
                    id: item.getAttribute('data-id'), // 假設您在項目上添加了 data-id
                    title: item.querySelector('.title').textContent.trim(),
                };
            });
            console.log('主選單新順序：', order);
            // 發送到後端保存
        });
        draggable.on('sortable:sort', () => console.log('sortable:sort'));
        draggable.on('sortable:sorted', () => console.log('sortable:sorted'));
        draggable.on('sortable:stop', () => console.log('sortable:stop'));
    }
}

document.addEventListener("DOMContentLoaded", organizable);
