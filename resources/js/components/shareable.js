import axios from "axios";

function initPopupElement(id, user, href) {
    if (document.getElementById(id) !== null) return;

    let shareablePopover = document.createElement("div");
    shareablePopover.id = "shareable_"+id;
    shareablePopover.classList.add("shareable-popover");
    shareablePopover.setAttribute("popover", "");

    let header = document.createElement("div");
    header.classList.add("shareable-popover-header");

    let title = document.createElement("div");
    title.classList.add("shareable-popover-title");
    title.innerText = "分享資源";

    let closeButton = document.createElement("button");
    closeButton.classList.add("btn", "btn-color7", "btn-ripple", "shareable-popover-close");
    closeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';

    closeButton.onclick = () => {
        shareablePopover.hidePopover();
    };

    header.appendChild(title);
    header.appendChild(closeButton);

    let itemBar = document.createElement("div");
    itemBar.classList.add("shareable-popover-itembar");

    let embedButton = document.createElement("div");
    embedButton.classList.add("btn", "btn-color7", "shareable-popover-item");
    embedButton.innerHTML = '<i class="fa-solid fa-code"></i><div class="shareable-popover-item-float-text">嵌入</div>';

    let copyLinkButton = document.createElement("div");
    copyLinkButton.classList.add("btn", "btn-color7", "shareable-popover-item", "copyer");
    copyLinkButton.dataset.url = href;
    copyLinkButton.innerHTML = '<i class="fa-solid fa-link"></i><div class="shareable-popover-item-float-text">複製網址</div>';

    itemBar.appendChild(embedButton);
    itemBar.appendChild(copyLinkButton);

    let otherDiv = document.createElement("div");
    otherDiv.classList.add("shareable-popover-other");

    let shareMethodField = document.createElement("div");
    shareMethodField.classList.add("shareable-popover-status-field");

    let shareMethodTitle = document.createElement("div");
    shareMethodTitle.classList.add("shareable-popover-status-field-title", "!w-1/2");
    shareMethodTitle.innerText = "分享方式";

    let shareMethodOperator = document.createElement("div");
    shareMethodOperator.classList.add("shareable-popover-status-field-operator", "!w-1/2");

    let methodSelect = document.createElement("select");
    methodSelect.classList.add("tom-select");
    methodSelect.innerHTML = `
    <option value="0">使用網址可以直接訪問資源</option>
    <option value="1">使用分享名單</option>
`;

    shareMethodOperator.appendChild(methodSelect);
    shareMethodField.appendChild(shareMethodTitle);
    shareMethodField.appendChild(shareMethodOperator);

    let shareListField = document.createElement("div");
    shareListField.classList.add("shareable-popover-status-field");

    let shareListTitle = document.createElement("div");
    shareListTitle.classList.add("shareable-popover-status-field-title", "!w-1/2");
    shareListTitle.innerText = "分享名單";

    let shareListOperator = document.createElement("div");
    shareListOperator.classList.add("shareable-popover-status-field-operator", "!w-1/2");

    let membersSelect = document.createElement("select");
    membersSelect.classList.add("tom-select");
    membersSelect.setAttribute("data-src", user);
    membersSelect.setAttribute("data-placeholder", "輸入使用者");
    membersSelect.setAttribute("data-width", "100%");
    membersSelect.setAttribute("name", "shareMembers");
    membersSelect.setAttribute("multiple", "");

    shareListOperator.appendChild(membersSelect);
    shareListField.appendChild(shareListTitle);
    shareListField.appendChild(shareListOperator);

    let startTimeField = document.createElement("div");
    startTimeField.classList.add("shareable-popover-status-field");

    let startTimeTitle = document.createElement("div");
    startTimeTitle.classList.add("shareable-popover-status-field-title", "!w-1/2");
    startTimeTitle.innerHTML = '<span class="tippyer" data-placement="auto" data-content="預設沒有啟用此設定時會採用現在時間">開始時間</span>';

    let startTimeOperator = document.createElement("div");
    startTimeOperator.classList.add("shareable-popover-status-field-operator", "flex", "items-center", "gap-3", "!w-1/2");

    let switcher = document.createElement("div");
    switcher.id = "SC-mode-switcher";
    switcher.classList.add("switch");
    switcher.setAttribute("data-name", "StartTimeEnable");
    switcher.setAttribute("data-onclick", "true");
    switcher.setAttribute("data-value", "false");
    switcher.innerHTML = `
    <div class="switch-border">
        <div class="switch-dot"></div>
    </div>
`;

    let hiddenInput = document.createElement("input");
    hiddenInput.type = "datetime-local";
    hiddenInput.classList.add("form-solid", "!hidden");

    startTimeOperator.appendChild(switcher);
    startTimeOperator.appendChild(hiddenInput);
    startTimeField.appendChild(startTimeTitle);
    startTimeField.appendChild(startTimeOperator);

    let expiryTimeField = document.createElement("div");
    expiryTimeField.className = "shareable-popover-status-field";

    let expiryTimeTitle = document.createElement("div");
    expiryTimeTitle.className = "shareable-popover-status-field-title !w-1/2";
    expiryTimeTitle.innerText = "到期時間";

    let expiryTimeOperator = document.createElement("div");
    expiryTimeOperator.className = "shareable-popover-status-field-operator !w-1/2";

    let expiryInput = document.createElement("input");
    expiryInput.type = "datetime-local";
    expiryInput.className = "form-solid";

    expiryTimeOperator.appendChild(expiryInput);
    expiryTimeField.appendChild(expiryTimeTitle);
    expiryTimeField.appendChild(expiryTimeOperator);

    let shareButton = document.createElement("div");
    shareButton.className = "btn btn-ripple btn-color7 btn-max btn-center";
    shareButton.innerText = "開始分享";

    otherDiv.appendChild(shareMethodField);
    otherDiv.appendChild(shareListField);
    otherDiv.appendChild(startTimeField);
    otherDiv.appendChild(expiryTimeField);
    otherDiv.appendChild(shareButton);

    shareablePopover.appendChild(header);
    shareablePopover.appendChild(itemBar);
    shareablePopover.appendChild(otherDiv);

    document.body.appendChild(shareablePopover);
    document.dispatchEvent(new CustomEvent('CGCP::init'));
    document.dispatchEvent(new CustomEvent('CGSW::init'));
    document.dispatchEvent(new CustomEvent('CGTS::init'));

    setTimeout(() => {
        switcher.on('click', function () {
            hiddenInput.classList.toggle("!hidden");
        });
    });
}

function initDownloadMenuPopupElement(id, href, json) {
    if (document.getElementById(id) !== null) return;

    let shareablePopover = document.createElement("div");
    shareablePopover.id = "download_"+id;
    shareablePopover.classList.add("shareable-popover");
    shareablePopover.setAttribute("popover", "");

    let header = document.createElement("div");
    header.classList.add("shareable-popover-header");

    let title = document.createElement("div");
    title.classList.add("shareable-popover-title");
    title.innerText = "下載資源";

    let closeButton = document.createElement("button");
    closeButton.classList.add("btn", "btn-color7", "btn-ripple", "shareable-popover-close");
    closeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';

    closeButton.onclick = () => {
        shareablePopover.hidePopover();
    };

    header.appendChild(title);
    header.appendChild(closeButton);


    let content = document.createElement("div");
    content.classList.add("shareable-popover-other");

    let table = document.createElement("table");
    table.classList.add("table", "table-striped", "table-row-hover", "table-border-0", "datatable");

    let object = JSON.parse(json);
    for (let objectKey in object) {
        let a = href.replace("%id%", id);
        a = a.replace("%fileId%", object[objectKey]['uuid']);
        object[objectKey]['action'] = object[objectKey]['action'].replace("%url%", a);
    }
    table.dataset.cgdatatype = "JSON";
    table.dataset.cgdata = JSON.stringify(object);
    table.dataset.cgfixedtable = "true";
    table.dataset.cgdefaulthash = "false";
    table.dataset.cgcolumns = "[" +
        "{\"data\":\"uuid\",\"name\":\"uuid\",\"title\":\"UUID\",\"footer\":\"UUID\"}," +
        "{\"data\":\"filename\",\"name\":\"filename\",\"title\":\"名稱\",\"footer\":\"名稱\"}," +
        "{\"data\":\"created_at\",\"name\":\"created_at\",\"title\":\"建立時間\",\"footer\":\"建立時間\"}," +
        "{\"data\":\"size\",\"name\":\"size\",\"title\":\"大小\",\"footer\":\"大小\"}," +
        "{\"data\":\"action\",\"name\":\"action\",\"title\":\"操作\",\"footer\":\"操作\"}" +
        "]";


    content.appendChild(table);

    shareablePopover.appendChild(header);
    shareablePopover.appendChild(content);

    document.body.appendChild(shareablePopover);
    document.dispatchEvent(new CustomEvent('CGTABLE::init'));
}

function shareable() {
    let shareables = document.querySelectorAll(".shareable");
    for (let shareable of shareables) {
        shareable.classList.add("shareableed");
        shareable.onclick = () => {
            let id = shareable.dataset.id;
            let href = shareable.dataset.href;
            let user = shareable.dataset.user;
            let type = shareable.dataset.type;
            let data = shareable.dataset.data;
            if(type === undefined) return;
            let popover;
            if (type === "download") {
                if (data === undefined) return;
                initDownloadMenuPopupElement(id, href, data);
                popover = document.getElementById("download_"+id);
            } else if(type === "share") {
                if (user === undefined) {
                    console.log("user url is undefined");
                    return;
                }
                initPopupElement(id, user, href);
                popover = document.getElementById("shareable_"+id);
            }
            if (popover.open) {
                popover.hidePopover();
            } else {
                popover.showPopover();
            }
            if (href !== undefined) {
                axios.post(href, {}, {adapter: "fetch", method: 'get'}).then(res => {
                    let data = res.data;
                    console.log(data);
                    let redirect = data['redirect'];
                    if (redirect !== undefined) {
                        window.location.href = redirect;
                    }
                });
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', shareable);
