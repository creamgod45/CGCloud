import * as Utils from './utils.js';
import jQuery from 'jquery/dist/jquery.js';
import Fuse from "fuse.js";

/**
 * 重置地址列表的選擇框狀態。
 *
 * @param {Object} addressList - 包含地址清單的對象，其中包含必要的欄位和選項。
 * @return {void} - 無返回值。
 */
function resetSBQ(addressList) {
    initGroupField(addressList, addressList.groupField);
    addressList.menuField.classList.add("!hidden");
    setStep(addressList.root, 1);
    setLayer(addressList.root, 1);
    let {children, select_map} = initSelectMap(addressList.groupField);
    let sbq_info = {
        groupid: "",
        input: "",
        matches: "",
        html: "",
        action: "",
    };
    addressList.root['step'] = {};
    toggleStep(parseInt(getStep(addressList.root)), select_map, sbq_info, addressList);
    build_fake_input(addressList);
    console.log("reset");
}

/**
 * 為指定的 addressList 生成一個 "oninput" 事件處理函數，當輸入框內容改變時觸發。
 *
 * @param {Object} addressList 包含多個元素和屬性的對象，用於管理地址選擇的各個部分。
 * @param {HTMLElement} addressList.root 根元素，用於控制地址選擇的步驟。
 * @param {HTMLElement} addressList.groupField 包含選項的元素，用於顯示匹配結果。
 * @param {HTMLElement} addressList.menuField 菜單元素，用於顯示或隱藏地址選擇菜單。
 *
 * @return {Function} 返回一個事件處理函數，用於處理輸入框的輸入事件。
 */
function getOninput(addressList) {
    return function (e) {
        const sbq_info = { groupid: "", input: "", matches: "", html: "", action: "" };
        const { select_map } = initSelectMap(addressList.groupField);
        toggleStep(parseInt(getStep(addressList.root)), select_map, sbq_info, addressList);

        if (addressList.menuField.classList.contains("!hidden")) {
            addressList.menuField.classList.remove("!hidden");
        }

        if (e.target.value.length > 0) {
            let inputs = [], values = [], keys = [];
            for (let menuEl of addressList.groupField.children) {
                if (menuEl.classList.contains("!hidden")) continue;
                if (menuEl.dataset.groupid === getStep(addressList.root)) {
                    inputs.push(menuEl.dataset.input);
                    values.push(menuEl.innerText);
                    keys.push(menuEl);
                }
            }

            const options = { includeScore: true, threshold: 0.6 };
            const inputsFuse = new Fuse(inputs, options);
            const valuesFuse = new Fuse(values, options);

            const search = inputsFuse.search(e.target.value);
            const search2 = valuesFuse.search(e.target.value);

            for (let x of keys) x.classList.add("!hidden");
            search2.forEach(key => keys[key.refIndex].classList.remove("!hidden"));
            search.forEach(key => keys[key.refIndex].classList.remove("!hidden"));
        } else {
            const { select_map } = initSelectMap(addressList.groupField);
            toggleStep(parseInt(getStep(addressList.root)), select_map, sbq_info, addressList);
        }
    };
}

/**
 * 當按下鍵盤上的 Enter 鍵時觸發的事件處理函數。
 * 此方法會自動點擊 `addressList` 內所有可見的選單項目，並清空輸入欄位的值。
 *
 * @param {Object} addressList 含有選單元素的物件。`addressList.menuField.children[1]` 應該是包含選單項目的容器。
 * @return {Function} 返回一個處理鍵盤事件的函數，當事件發生時執行點擊動作並清空輸入欄位。
 */
function SBQFastInputkeydown(addressList) {
    return function (e) {
        if (e.keyCode === 13) { // enter
            for (let menuEl of addressList.menuField.children[1].children) {
                if (!menuEl.classList.contains("!hidden")) menuEl.click();
            }
            e.target.value = "";
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        }
    };
}

/**
 * 初始化並配置所有具有類別 '.select-bar-query' 的元素。
 * 該方法將為這些元素設定事件監聽器，例如輸入事件、點擊事件以及重設事件。
 * 它也會初始化組的配置並提供一個重置欄位的確認對話框。
 *
 * @return {void} 此方法不返回任何值。
 */
function selectBarQuery() {
    document.querySelectorAll('.select-bar-query').forEach(listOfElement => {
        const addressList = {
            form: listOfElement.children[2].form,
            root: listOfElement,
            inputField: listOfElement.children[0],
            textareaField: listOfElement.children[1].children[0].children[0],
            groupField: listOfElement.children[1].children[1],
            menuField: listOfElement.children[1],
            advancedSearchField: listOfElement.children[2],
            resetField: listOfElement.children[3].children[1]
        };

        addressList.textareaField.oninput = getOninput(addressList);
        addressList.textareaField.onkeydown = SBQFastInputkeydown(addressList);

        addressList.inputField.onclick = function () {
            addressList.menuField.classList.toggle("!hidden");
        };

        initGroupField(addressList, addressList.groupField);

        addressList.resetField.onclick = function () {
            Utils.confirmDialog('請問是否繼續操作?', "確認重設欄位", "danger", function () {
                resetSBQ(addressList);
            });
        };

        addressList.form.addEventListener("reset", function () {
            resetSBQ(addressList);
        });
    });
}

/**
 * 獲取 DOM 節點的 step 資料集屬性值。
 *
 * @param {HTMLElement} root - DOM 節點元素
 * @return {string} - step 資料屬性值
 */
function getStep(root) {
    return root.dataset.step;
}

/**
 * 設置步驟值到指定的根元素的data-step屬性
 *
 * @param {HTMLElement} root - 要設置步驟值的根元素
 * @param {string|number} value - 要設置的步驟值
 * @return {void}
 */
function setStep(root, value) {
    root.dataset.step = value;
}

/**
 * 從給定的根節點中獲取數據屬性 'layer' 的值
 *
 * @param {Element} root - 要從中提取 'layer' 屬性的 HTML 元素
 * @return {string} 返回 'layer' 的值
 */
function getLayer(root) {
    return root.dataset.layer;
}

/**
 * 設定元素的自定義屬性 'data-layer' 的值
 *
 * @param {HTMLElement} root - 要設置層級值的根元素
 * @param {string} value - 要設置的層級值
 * @return {void}
 */
function setLayer(root, value) {
    root.dataset.layer = value;
}

/**
 * 初始化選擇項目的方法
 *
 * @param {Element} self - 傳入的 DOM 元素
 * @return {Object} 返回初始化後的物件，包含多個 dataset 屬性及 HTML 元素本身
 */
function initSelect(self) {
    const dataset = self.dataset;
    const obj = {
        groupid: dataset.groupid,
        input: dataset.input,
        matches: dataset.matches,
        action: dataset.action,
        label: dataset.label,
        html: self
    };

    if (dataset.groupid !== "1") self.classList.add("!hidden");
    self.sbq_info = obj;

    return obj;
}

/**
 * 隱藏所有元素。
 *
 * @param {Object} select_map 對象，其值為由元素組成的數組。
 * @return {void} 無返回值。
 */
function hiddenAllElements(select_map) {
    for (let selectMapList of Object.values(select_map)) {
        selectMapList.forEach(el => el.html.classList.add("!hidden"));
    }
}

/**
 * 切換指定步驟的顯示狀態。
 *
 * @param {string} step_id - 步驟的唯一識別碼。
 * @param {Object} select_map - 包含步驟與其對應元素的對照表。
 * @param {Object} sbq_info - 包含當前輸入資訊的對象。
 * @param {Array} addressList - 地址列表。
 * @return {void}
 */
function toggleStep(step_id, select_map, sbq_info, addressList) {
    const selectMapElement = select_map[step_id];
    hiddenAllElements(select_map);
    if (selectMapElement !== undefined) {
        selectMapElement.forEach(el => {
            if (!el.matches || el.matches === "all" || el.matches === sbq_info.input) {
                el.html.classList.remove("!hidden");
            }
        });
    }
}

/**
 * 在 select_map 對象中搜尋並返回一個 input 屬性為 "text" 的元素。
 *
 * @param {Object} select_map - 要搜尋的對象，鍵值對應元素數組。
 * @return {Object|null} 返回一個 input 屬性為 "text" 的元素，如果沒有找到則返回 null。
 */
function searchTextInput(select_map) {
    return [].concat(...Object.values(select_map)).find(item => item.input === "text") || null;
}

/**
 * 確認select_map中是否存在搜尋文本輸入框。
 *
 * @param {Object} select_map - 包含DOM元素的映射。
 * @return {boolean} 如果搜尋文本輸入框存在，則返回true，否則返回false。
 */
function hasSearchTextInput(select_map) {
    return searchTextInput(select_map) !== null;
}

/**
 * 處理自訂選擇列表的初始化。
 *
 * @param {HTMLElement} self - 包含選項數據的HTML元素。
 * @return {void} 無返回值。
 */
function handleCustomListSelect2(self) {
    if (self.dataset.options !== undefined && self.buildedOptions === undefined) {
        let options = JSON.parse(self.dataset.options);
        let type = options['type'];
        switch (type) {
            case "list":
                let items = options['items'];
                for (let item of items) {
                    let newOption = new Option(item.text, item.id, false, false);
                    jQuery('#sbq-input-select').append(newOption).trigger('change');
                }
                self.buildedOptions = false;
                break;
            case "ajax":
                self.buildedOptions = false;
                break;
        }
    }
}

/**
 * 處理按鈕點擊事件，根據提供的地址列表和選擇地圖進行相應的操作，如更新地址列表、處理特殊輸入類型及初始化選擇地圖等。
 *
 * @param {Object} self - 觸發按鈕點擊事件的元素本身。
 * @param {Object} addressList - 包含地址信息的對象列表。
 * @param {Object} select_map1 - 提供地址選擇映射的對象。
 * @return {boolean} 若操作成功則返回 true，否則返回 false。
 */
function btnProccess(self, addressList, select_map1) {
    addressList.textareaField.value = "";
    const sbq_info = self.sbq_info;
    let length = 0, parent = 0, root = 0;

    try {
        length = addressList.root["step"][getLayer(addressList.root)].length;
        parent = length - 1;
    } catch (e) {
        addressList.root["step"] = addressList.root["step"] || {};
        addressList.root["step"][getLayer(addressList.root)] = [];
        parent = -1;
        root = -1;
    }

    if (["select", "text"].includes(sbq_info.input)) {
        const querySelector = self.querySelector(self.dataset.inputtarget);
        if (!querySelector || querySelector.value === "") return false;
        addStep(addressList, sbq_info, querySelector, parent, root, length);
    } else {
        addStep(addressList, sbq_info, null, parent, root, length);
    }

    handleSpecialInputType(sbq_info.input, addressList);

    handleCustomListSelect2(self);
    const { select_map } = initSelectMap(addressList.groupField);
    toggleStep(parseInt(getStep(addressList.root)), select_map, sbq_info, addressList);
    build_fake_input(addressList);
}

/**
 * 添加搜尋條件步驟到地址列表中。
 *
 * @param {Object} addressList - 儲存地址信息的對象。
 * @param {Object} sbq_info - 包含步驟信息的對象。
 * @param {HTMLElement} querySelector - 用於選擇數值的查詢選擇器。
 * @param {number} parent - 父步驟的索引。
 * @param {number} root - 根步驟的索引。
 * @param {number} length - 當前步驟的索引長度。
 * @return {void} - 這個方法沒有回傳值。
 */
function addStep(addressList, sbq_info, querySelector, parent, root, length) {
    addressList.root["step"][getLayer(addressList.root)][length] = {
        input: sbq_info.input,
        label: sbq_info.label + (sbq_info.input === 'select' && querySelector ? ':' + jQuery(querySelector).find(":selected").text() : ''),
        matches: sbq_info.matches,
        action: sbq_info.action,
        value: querySelector ? querySelector.value : null,
        parent: parent !== -1 ? parent : null,
        root: root !== -1 ? root : null
    };

    addressList.advancedSearchField.value = JSON.stringify(addressList.root["step"]);
    if (querySelector) querySelector.value = "";
}

/**
 * 根據不同的 `inputType` 處理特殊輸入類型並更新 `addressList` 中的屬性。
 *
 * @param {string} inputType 指定的特殊輸入類型，其值可能為 "and", "or", "none", "text", "equal", "notequal", "include", "exclude" 等。
 * @param {Object} addressList 地址列表對象，包含需要修改的節點信息。
 * @return {void} 該方法不返回任何值。
 */
function handleSpecialInputType(inputType, addressList) {
    switch (inputType) {
        case "and":
        case "or":
            setStep(addressList.root, 1);
            setLayer(addressList.root, parseInt(getLayer(addressList.root)) + 1);
            break;
        case "none":
            setStep(addressList.root, -3);
            break;
        case "text":
            setStep(addressList.root, -1);
            break;
        case "equal":
        case "notequal":
        case "include":
        case "exclude":
            setStep(addressList.root, -2);
            break;
        default:
            setStep(addressList.root, parseInt(getStep(addressList.root)) + 1);
            break;
    }
}

/**
 * 删除对象中的指定键值并重新排序键
 *
 * @param {Object} obj - 要处理的对象
 * @param {string} keyToRemove - 要删除的键值
 * @return {Object} - 处理后的新对象
 */
function removeItemAndResortKeys(obj, keyToRemove) {
    delete obj[keyToRemove];
    const newObj = {};
    Object.keys(obj).forEach(key => newObj[key] = obj[key]);
    return newObj;
}

/**
 * 移除指定層級的項目並重新整理關鍵的排序。
 *
 * @param {object} addressList 包含地址信息的對象。
 * @param {number} layer 要移除的層級。
 * @param {object} self 當前上下文對象。
 * @return {void} 此方法沒有返回值。
 */
function removeLayerItem(addressList, layer, self) {
    addressList.root["step"] = removeItemAndResortKeys(addressList.root["step"], layer.toString());
    build_fake_input(addressList);

    if (self.input === "none") {
        const sbq_info = { groupid: null, input: null, matches: null, html: null, action: null, label: null };
        const { select_map } = initSelectMap(addressList.groupField);
        setStep(addressList.root, 1);
        toggleStep(1, select_map, sbq_info, addressList);
        addressList.menuField.classList.add("!hidden");
    }

    addressList.advancedSearchField.value = JSON.stringify(addressList.root["step"]);
}

/**
 * 檢查給定的值是否為單純物件（plain object）。
 *
 * 單純物件是指由 Object 建構函數建立的對象，或是純粹以物件字面量宣告的對象。
 *
 * @param {*} value - 要檢查的值。
 * @return {boolean} 如果值是單純物件，回傳 true；否則，回傳 false。
 */
function isPlainObject(value) {
    return Object.prototype.toString.call(value) === '[object Object]';
}

/**
 * 構建虛假的輸入項目，並將其添加到給定的 addressList 中。
 *
 * @param {Object} addressList 包含虛假輸入項目相關信息的物件，包括 root 和 inputField。
 * @return {void}
 */
function build_fake_input(addressList) {
    const root = addressList.root;
    const sbqFI = addressList.inputField.querySelector(".sbq-fast-input");
    const temp = sbqFI ? sbqFI.innerHTML : "";

    addressList.inputField.innerHTML = temp;

    for (const [key, value] of Object.entries(root["step"])) {
        for (const valueElement of value) {
            const item = document.createElement("div");
            item.classList.add("sbq-field-item");

            const content = document.createElement("div");
            content.classList.add("context", "rippleable1");

            const label = document.createElement("span");
            label.classList.add("label");
            label.innerText = valueElement.value ? `${valueElement.label}: ${valueElement.value}` : valueElement.label;
            content.append(label);

            if (["none", "and", "or"].includes(valueElement.input)) {
                const button = document.createElement("a");
                button.classList.add("btn", "btn-border-0", 'sbq-fi-close-btn');
                button.input = valueElement.input;
                button.onclick = () => removeLayerItem(addressList, key, button);

                const close_icon = document.createElement("i");
                close_icon.classList.add("fa-solid", "fa-circle-xmark");
                button.append(close_icon);

                content.append(button);
            }

            item.append(content);
            addressList.inputField.append(item);
        }
    }
}

/**
 * 初始化所有選擇項目。
 *
 * @param {Array} children - 子項目列表。
 * @return {Object} select_map - 包含分組後的選擇項目對象。
 */
function initAllSelect(children) {
    const select_map = {};
    for (let child of children) {
        const item = initSelect(child);
        if (!select_map[item.groupid]) select_map[item.groupid] = [];
        select_map[item.groupid].push(item);
    }
    return select_map;
}

/**
 * 初始化选择映射的方法。
 *
 * @param {Object} self - 拥有 children 属性的对象。
 * @return {Object} 返回包含 children 属性和选择映射的对象。
 */
function initSelectMap(self) {
    return { children: self.children, select_map: initAllSelect(self.children) };
}

/**
 * 初始化群組字段
 *
 * @param {Object} addressList - 包含地址信息的對象
 * @param {HTMLElement} self - 包含元素子節點的 DOM 節點
 * @return {void} 此函數不返回值
 */
function initGroupField(addressList, self) {
    if (!getStep(addressList.root)) setStep(addressList.root, 1);
    const { select_map } = initSelectMap(self);

    for (let element of self.children) {
        element.onclick = function () {
            btnProccess(element, addressList, select_map);
        };
    }
}

/**
 * `loader` 函數調用 `selectBarQuery` 方法。
 * @return {void} 無返回值。
 */
function loader() {
    selectBarQuery();
}

document.addEventListener('DOMContentLoaded', loader);
