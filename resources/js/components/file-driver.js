import * as Utils from './utils.js';

function fileDriver() {
    let fileDrivers = document.querySelectorAll('.file-driver');
    for (let f of fileDrivers) {
        let addressList = {
            root: f,
            items: f.querySelectorAll(".fd-item"),
        };

        let deleteButtonFn = function (self) {
            Utils.confirmDialog('請確認是否繼續此操作', '確定要移除此物件?', 'danger', function () {
                self.remove();
            }, function () {

            });
        };

        for (let item of addressList.items) {
            item.addressList = {
                imgInfo: item.querySelector(".fdi-imginfo"),
                deleteButton: item.querySelector(".fdi-delete"),
            };
            item.addressList.deleteButton.onclick = ()=>{ deleteButtonFn(item) };
            let imginfo = item.addressList.imgInfo;
            let width = imginfo.naturalWidth;
            let height = imginfo.naturalHeight;
            let sizeText = document.createElement('div');
            sizeText.classList.add('fdi-size-text');
            sizeText.innerText = `${width} x ${height}`;

            item.appendChild(sizeText);
        }

        f.addressList = addressList;
    }
}

document.addEventListener('DOMContentLoaded', fileDriver);
