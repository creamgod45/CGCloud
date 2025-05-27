import axios from "axios";

function initPopupElement(id, user, href, users) {
    if (document.getElementById( "shareable_"+id) !== null) return;

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
    membersSelect.setAttribute("data-options", users);
    let optionsids = [];
    const parse = JSON.parse(users);
    if(parse !== null) {
        console.log(parse);
        for (let userElement of parse) {
            optionsids.push(userElement['value']);
        }
        membersSelect.setAttribute("data-optionsids", JSON.stringify(optionsids));
    }

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
    document.dispatchEvent(new CustomEvent('CGCT::init'));

    setTimeout(() => {
        switcher.on('click', function () {
            hiddenInput.classList.toggle("!hidden");
        });
    });
}

async function initDownloadMenuPopupElement(id, href, json, delete_url) {
    if (document.getElementById("download_"+id) !== null) return;

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
    table.classList.add("table", "table-striped", "table-row-hover", "table-border-0", "datatable", "placeholder", "placeholder-ct", "placeholder-full-wh");

    let object = JSON.parse(json);
    let uuids=[];
    for (let objectElement of object) {
        uuids.push(objectElement['uuid']);
    }

    await axios.post('/api/sharetable/create/preview', {
        fileId: uuids,
        shareTableId: id,
    }, { adapter: "fetch", method: 'post',responseType:'json'}).then(res => {
        console.log(res);
        let data = res.data;

        for (let objectKey in object) {
            let a = href.replace("%id%", id);
            a = a.replace("%fileId%", object[objectKey]['uuid']);
            let b = delete_url.replace("%id%", id);
            b = b.replace("%fileId%", object[objectKey]['uuid']);

            let uuid = object[objectKey]['uuid'];
            let resDataValue = data.find(item => item.includes(uuid));
            object[objectKey]['action'] = object[objectKey]['action'].replace("%url-0%", resDataValue);
            object[objectKey]['action'] = object[objectKey]['action'].replace("%url-1%", a);
            object[objectKey]['action'] = object[objectKey]['action'].replace("%url-2%", b);
        }
    });

    table.dataset.placeholderdelay = "1000";
    table.dataset.cgdatatype = "JSON";
    table.dataset.cgdata = JSON.stringify(object);
    table.dataset.cgfixedtable = "true";
    table.dataset.cgdefaulthash = "false";
    table.dataset.cgcolumns = "[" +
        "{\"data\":\"id\",\"name\":\"ID\",\"title\":\"ID\",\"footer\":\"ID\"}," +
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
    document.dispatchEvent(new CustomEvent('CGPLACEHOLDER::init'));
    document.dispatchEvent(new CustomEvent('CGCT::init'));
    setTimeout(()=>{
        document.dispatchEvent(new CustomEvent('CGCONFIRMBOX::init'));
    }, 1000);
}

async function initConversionPopupElement(id, href, json) {
    if (document.getElementById("conversion_"+id) !== null) return;

    let shareablePopover = document.createElement("div");
    shareablePopover.id = "conversion_"+id;
    shareablePopover.classList.add("shareable-popover");
    shareablePopover.setAttribute("popover", "");

    let header = document.createElement("div");
    header.classList.add("shareable-popover-header");

    let title = document.createElement("div");
    title.classList.add("shareable-popover-title");
    title.innerText = "轉換資源";

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
    table.classList.add("table", "table-striped", "table-row-hover", "table-border-0", "datatable", "placeholder", "placeholder-ct", "placeholder-full-wh");

    let object = JSON.parse(json);
    for (let objectElement of object) {
        let a = href.replace("%fileId%", objectElement['uuid']);
        objectElement['action'] = objectElement['action'].replace("%url-0%", a);
    }

    table.dataset.placeholderdelay = "1000";
    table.dataset.cgdatatype = "JSON";
    table.dataset.cgdata = JSON.stringify(object);
    table.dataset.cgfixedtable = "true";
    table.dataset.cgdefaulthash = "false";
    table.dataset.cgcolumns = "[" +
        "{\"data\":\"id\",\"name\":\"ID\",\"title\":\"ID\",\"footer\":\"ID\"}," +
        "{\"data\":\"filename\",\"name\":\"filename\",\"title\":\"名稱\",\"footer\":\"名稱\"}," +
        "{\"data\":\"created_at\",\"name\":\"created_at\",\"title\":\"建立時間\",\"footer\":\"建立時間\"}," +
        "{\"data\":\"action\",\"name\":\"action\",\"title\":\"操作\",\"footer\":\"操作\"}," +
        "{\"data\":\"size\",\"name\":\"size\",\"title\":\"大小\",\"footer\":\"大小\"}" +
        "]";
    table.dataset.cgresponsive = "false";


    content.appendChild(table);

    shareablePopover.appendChild(header);
    shareablePopover.appendChild(content);

    document.body.appendChild(shareablePopover);
    document.dispatchEvent(new CustomEvent('CGTABLE::init'));
    document.dispatchEvent(new CustomEvent('CGPLACEHOLDER::init'));
    document.dispatchEvent(new CustomEvent('CGCT::init'));
    setTimeout(()=>{
        document.dispatchEvent(new CustomEvent('CGCONFIRMBOX::init'));
    }, 1000);
}

async function shareable() {
    let shareables = document.querySelectorAll(".shareable");
    for (let shareable of shareables) {
        shareable.classList.add("shareableed");
        shareable.onclick = async () => {
            let id = shareable.dataset.id;
            let href = shareable.dataset.href;
            let type = shareable.dataset.type;
            let data = shareable.dataset.data;
            if (type === undefined) return;
            let popover;
            if (type === "download") {
                if (data === undefined) return;
                let delete_url = shareable.dataset.delete;
                let owner = shareable.dataset.owner;
                if (delete_url === undefined) return;
                await initDownloadMenuPopupElement(id, href, data, delete_url, owner);
                popover = document.getElementById("download_" + id);
            } else if (type === "share") {
                let user = shareable.dataset.user;
                let users = shareable.dataset.users;
                if (user === undefined) {
                    console.log("user url is undefined");
                    return;
                }
                if (users === undefined) {
                    console.log("users is undefined");
                    return;
                }
                await initPopupElement(id, user, href, users);
                popover = document.getElementById("shareable_" + id);
            } else if (type === "conversion") {
                await initConversionPopupElement(id, href, data);
                popover = document.getElementById("conversion_" + id);
            }
            if (popover.open) {
                await popover.hidePopover();
            } else {
                await popover.showPopover();
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', shareable);
