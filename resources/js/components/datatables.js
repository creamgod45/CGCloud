import pdfmake from "pdfmake/build/pdfmake";
import * as vfsOption from "./cgvfs_fonts.js";
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-fixedcolumns-dt';
import 'datatables.net-fixedheader-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-scroller-dt';
import 'datatables.net-select-dt';
import 'datatables.net-staterestore-dt';
import './datatable.tailwind.js';
//import 'datatables.net-autofill-dt';
//import 'datatables.net-colreorder-dt';
//import 'datatables.net-keytable-dt';
//import 'datatables.net-rowgroup-dt';
//import 'datatables.net-rowreorder-dt';
//import 'datatables.net-searchbuilder-dt';
//import 'datatables.net-searchpanes-dt';
import tippy from "tippy.js";
import jQuery from "jquery";

/**
 * 當前窗口的原始位置。
 * 使用 window.location.origin 獲取當前窗口的原始位置 URL。這個變量包含協議、域名以及端口號（如果有）。
 * 可以用於需要完整站點根URL的情況，如網頁中相對路徑的絕對化等操作。
 */
let domain = window.location.origin;
pdfmake.vfs = vfsOption.vfs;
pdfmake.fonts = {
    Roboto: {
        normal: `${domain}/font/NotoSansTC-Regular.ttf`,
        bold: `${domain}/font/NotoSansTC-Bold.ttf`,
        italics: `${domain}/font/NotoSansTC-Light.ttf`,
        bolditalics: `${domain}/font/NotoSansTC-Thin.ttf`
    },
};

/**
 * 表格顯示的多語言設置。
 * 包含所有用於本地化DataTables插件的字符串。
 *
 * @property {string} decimal 顯示小數點的符號。
 * @property {string} processing 當數據正在處理時顯示的文本。
 * @property {string} search 搜尋框前的提示文本。
 * @property {string} lengthMenu 控制表格每頁顯示數量的提示文本，_MENU_ 會被替換為選擇框。
 * @property {string} info 顯示表格當前頁數據的信息，_START_、_END_ 和 _TOTAL_ 會被動態替換。
 * @property {string} infoEmpty 當沒有數據時顯示的信息。
 * @property {string} infoFiltered 當數據是進行過過濾時顯示的信息，_MAX_ 會被替換為總數據數量。
 * @property {string} infoPostFix 顯示在信息資料的後綴文本。
 * @property {string} thousands 顯示千位分隔符的符號。
 * @property {string} loadingRecords 當數據正在載入時顯示的文本。
 * @property {string} zeroRecords 當沒有符合檢索條件的數據時顯示的文本。
 * @property {string} emptyTable 當表格中沒有數據時顯示的文本。
 * @property {Object} paginate 分頁按鈕顯示的文本。
 * @property {string} paginate.first 分頁中跳轉到第一頁的按鈕文本。
 * @property {string} paginate.previous 分頁中跳轉到上一頁的按鈕文本。
 * @property {string} paginate.next 分頁中跳轉到下一頁的按鈕文本。
 * @property {string} paginate.last 分頁中跳轉到最後一頁的按鈕文本。
 * @property {Object} aria ARIA 無障礙設置的文本提示。
 * @property {string} aria.sortAscending 點擊以升序排序的提示文本。
 * @property {string} aria.sortDescending 點擊以降序排序的提示文本。
 * @property {Object} select 選擇行時顯示的文本提示。
 * @property {Object} select.rows 選擇的行數相關的文本提示。
 * @property {string} select.rows._ 選擇多行時顯示的文本，%d 會被替換為選擇的行數。
 * @property {string} select.rows.1 選擇單行時顯示的文本。
 */
let languages = {
    decimal: "",
    processing: "處理中...",
    search: "搜尋&nbsp;:",
    lengthMenu: "顯示 _MENU_ 項結果",
    info: "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
    infoEmpty: "顯示第 0 至 0 項結果，共 0 項",
    infoFiltered: "(從 _MAX_ 項結果中過濾)",
    infoPostFix: "",
    thousands: ",",
    loadingRecords: "載入中...",
    zeroRecords: "沒有符合的結果",
    emptyTable: "表格中無可用數據",
    paginate: {
        first: "第一頁",
        previous: "上一頁",
        next: "下一頁",
        last: "最後一頁"
    },
    aria: {
        sortAscending: ": 點擊以升序排序",
        sortDescending: ": 點擊以降序排序"
    },
    select: {
        rows: {
            _: '選擇 %d 列',
            1: '選擇 1 列'
        }
    },
};

/**
 * @namespace exportFormatter
 * @description 此对象用于定义数据导出的格式化逻辑。
 *
 * @property {Object} format - 包含格式化函数的对象。
 * @property {function} format.body - 定义如何格式化表格数据的函数。
 * @property {function} format.body(data, row, column) - 根据列索引格式化数据的函数。
 * @param {*} data - 当前单元格的数据。
 * @param {number} row - 当前行的索引。
 * @param {number} column - 当前列的索引。
 * @returns {*} 格式化后的数据。
 */
const exportFormatter = {
    format: {
        body: (data, row, column) => {
            if ([0, 1, 3].includes(column)) data = '';
            return data;
        }
    }
};

/**
 * DataTable 扩展属性 errMode
 *
 * 此属性用于控制 DataTable 在发生错误时的行为。
 * 您可以通过设置此属性来定制错误处理逻辑，从而增强程序的鲁棒性。
 *
 * 可选值：
 * - 'alert': 弹出一个警告窗口，显示错误信息。
 * - 'throw': 抛出 JavaScript 错误，使得程序流程中断。
 * - 'none': 忽略错误，继续执行后续代码。
 *
 * 示例：
 * DataTable.ext.errMode = 'throw'; // 设置为在发生错误时抛出错误
 *
 * @type {string}
 */
DataTable.ext.errMode = 'throw';

/**
 * 初始化所有具有 class "datatable" 的元素，設置資料表格的選項和功能。
 *
 * @return {Promise<boolean|void>} 如果某些必要的資料屬性（如 columns 或 fixedtable）缺失則返回 false，否則無返回值。
 */
async function datatables() {
    const datatables = document.querySelectorAll(".datatable:not(.initialized)");

    for (let datatableEl of datatables) {
        datatableEl.classList.add("initialized");
        let {
            cgcaption: caption,
            cgdata: data = JSON.parse("{}"),
            cgcolumns: columns,
            cgdatatype: dataType = "JSON",
            cgresponsive: responsive = true,
            cgselect: select = true,
            cgsearching: searching = true,
            cgordering: ordering = true,
            cgscroller: scroller = null,
            cgscrolly: scrollY = null,
            cgfixedtable: fixedtable,
            cgpaging: paging = true,
            cgdefaulthash: defaulthash,
            cgpopover: popover
        } = datatableEl.dataset;
        if(defaulthash === undefined){
            defaulthash = true;
        }
        if(defaulthash !== "true") defaulthash = false;

        scrollY = scrollY ? parseInt(scrollY) : null;
        if (scroller && scroller !== "[]") scroller = JSON.parse(scroller);

        if (columns === undefined || fixedtable === undefined) return false;

        try {
            columns = JSON.parse(columns);
            if(defaulthash){
                columns.unshift({
                    data: null,
                    orderable: false,
                    searchable: false,
                    footer: '',
                    render: DataTable.render.select('id', 'checkbox')
                });
                columns.unshift({
                    data: '#',
                    title: '#',
                    footer: '#',
                    orderable: false,
                    searchable: false
                });
            }
            fixedtable = JSON.parse(fixedtable);
        } catch (e) {
            break;
        }

        const options = {
            pageLength: 50,
            lengthMenu: [50, 100, 200, 500, 1000, 5000, 10000, 50000, { label: '全部', value: -1 }],
            responsive: JSON.parse(responsive),
            data: dataType.toUpperCase() === "JSON" ? JSON.parse(data) : data,
            language: { url: 'https://cdn.datatables.net/plug-ins/2.1.5/i18n/zh-HANT.json' },
            columns: columns,
            searching: JSON.parse(searching),
            ordering: JSON.parse(ordering),
            caption: caption,
            fixedtable: fixedtable,
            processing: true,
            paging: paging,
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '全選 Ctrl+Shift+A',
                            key: { ctrl: true, shift: true, key: 'a' },
                            className: 'btn btn-color7 btn-ripple',
                            action: (e, dt) => dt.rows().select()
                        },
                        {
                            text: '取消全選',
                            key: { ctrl: true, shift: true, key: 'a' },
                            className: 'btn btn-color7 btn-ripple',
                            action: (e, dt) => dt.rows().deselect()
                        },
                        {
                            extend: 'copyHtml5',
                            text: '複製排版',
                            className: 'btn btn-color7 btn-ripple',
                            exportOptions: exportFormatter
                        },
                        { extend: 'csv', text: '匯出 CSV', className: 'btn btn-color7 btn-ripple' },
                        {
                            extend: 'excelHtml5',
                            text: '匯出 Excel HTML',
                            className: 'btn btn-color7 btn-ripple',
                            exportOptions: exportFormatter,
                            title: '匯出 Excel 資料'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '匯出 PDF',
                            className: 'btn btn-color7 btn-ripple',
                            exportOptions: exportFormatter,
                            title: '匯出 PDF'
                        },
                        {
                            text: '匯出 JSON',
                            className: 'btn btn-color7 btn-ripple',
                            action: (e, dt) => {
                                const data = dt.buttons.exportData();
                                delete data.headerStructure;
                                delete data.footer;
                                delete data.footerStructure;
                                DataTable.fileSave(new Blob([JSON.stringify(data, null, 4)]), '匯出 JSON.json');
                            }
                        },
                        { extend: 'pageLength', className: 'btn btn-color7 btn-ripple' }
                    ]
                },
                bottomEnd: { paging: { boundaryNumbers: true } }
            }
        };

        if (data !== undefined && dataType.toUpperCase() === "SERVERSIDE") {
            delete options.data;
            options.serverSide = true;
            options.ajax = JSON.parse(data);
        }

        options.select = {
            style: 'multi',
            selector: 'td:not(:first-child,.clickable)',
            headerCheckbox: 'select-all'
        };
        options.order = [[2, 'asc']];
        if (scroller !== null) options.scroller = scroller;
        if (scrollY !== null) options.scrollY = scrollY;

        datatableEl.datatableObj = new DataTable(datatableEl, options);
        datatableEl.selectedRowsFn = handleSelectedRows;
        datatableEl.selectedRows = [];
        datatableEl.cg = options;
        datatableEl.faildAlert1El = null;

        attachDatatableEvents(datatableEl, options, popover);
    }
}

/**
 * 處理選擇的行，將它們在資料表中標記為選取狀態。
 *
 * @param {Object} e 事件對象，包含資料表的相關資訊
 * @param {Object} setting 設置對象，包括配置選項
 * @return {void} 無返回值
 */
function handleSelectedRows(e, setting) {
    const dt = e.dt;
    const selectedRows = this.selectedRows;
    selectedRows.forEach(row => dt.row(row).select());
}

/**
 * 為數據表格附加各種事件處理程序。
 *
 * @param {object} datatableEl - 包含數據表格元素及相關屬性的對象。
 * @param {object} options - 用於初始化數據表格的選項對象。
 * @param {object} popover - 用於顯示行點擊時的彈出框對象。
 * @return {void} 無返回值。
 */
function attachDatatableEvents(datatableEl, options, popover) {
    datatableEl.datatableObj.on('draw.dt', () => {
        document.dispatchEvent(new CustomEvent('BtnLoad'));
        document.dispatchEvent(new CustomEvent('CGTIPPYER::init'));
        document.dispatchEvent(new CustomEvent('CGAUTOUPDATE::init'));
        if (datatableEl.faildAlert1El) datatableEl.faildAlert1El.hide();
        clearInterval(datatableEl.timer);
    });

    datatableEl.datatableObj.on('length', () => {
        datatableEl.timerKey = 0;
        datatableEl.timer = setInterval(() => handleLoadingTimer(datatableEl), 1000);
    });

    datatableEl.datatableObj.on('error.dt', message => {
        tippy(datatableEl, {
            content: '已經超出系統加載上限，目前元件重新建構中...(修復中)',
            arrow: true,
            theme: 'danger',
            animation: 'scale',
            hideOnClick: false,
            placement: "top",
        }).show();
        datatableEl.datatableObj = new DataTable(datatableEl, options);
    });

    datatableEl.datatableObj.on('select', (e, dt, type, indexes) => {
        if (type === 'row') datatableEl.selectedRows.push(indexes);
    });

    datatableEl.datatableObj.on('deselect', (e, dt, type, indexes) => {
        if (type === 'row') {
            const index = datatableEl.selectedRows.indexOf(indexes);
            if (index > -1) datatableEl.selectedRows.splice(index, 1);
        }
    });

    jQuery(datatableEl.querySelector("tbody")).on('click', '.clickable', (e) => handleRowClick(datatableEl, popover, e));
}

/**
 * 處理加載定時器的方法。根據當前的定時器鍵值(timerKey)顯示不同的提示消息。
 *
 * @param {object} datatableEl - 包含定時器鍵值及提示消息元素的數據表元素。
 * @return {void}
 */
function handleLoadingTimer(datatableEl) {
    const search = document.querySelector("#dt-search-0");
    switch (datatableEl.timerKey) {
        case 17:
            datatableEl.faildAlert1El = tippy(search, {
                content: '目前正在加載中(如果庫存過多可能會導致加載失敗;超過30秒將會中斷載入) 已經讀取 17 秒 ',
                arrow: true,
                theme: 'danger',
                animation: 'scale',
                hideOnClick: false,
                placement: "bottom",
            });
            datatableEl.faildAlert1El.show();
            break;
        case 30:
            datatableEl.faildAlert1El.hide();
            tippy(search, {
                content: '加載失敗幾秒後重新整理頁面(請嘗試加載少量的筆數)',
                arrow: true,
                theme: 'danger',
                animation: 'scale',
                hideOnClick: false,
                placement: "bottom",
            }).show();
            setTimeout(() => window.location.reload(), 3000);
            break;
        default:
            if (datatableEl.timerKey > 17) {
                datatableEl.faildAlert1El.hide();
                datatableEl.faildAlert1El = tippy(search, {
                    content: `目前正在加載中(如果庫存過多可能會導致加載失敗;超過30秒將會中斷載入) 已經讀取 ${datatableEl.timerKey} 秒`,
                    arrow: true,
                    theme: 'danger',
                    animation: 'scale',
                    hideOnClick: false,
                    placement: "bottom",
                });
                datatableEl.faildAlert1El.show();
            }
            break;
    }
    datatableEl.timerKey += 1;
}

/**
 * 處理表格列點擊事件的方法。該方法會在指定的彈出視窗中載入相關內容，
 * 並在彈出視窗關閉時重置其內容。
 *
 * @param {HTMLElement} datatableEl 表格元素，包含被點擊列的資料。
 * @param {string} popover 彈出視窗的元素ID，用於顯示詳細資訊或編輯內容。
 *
 * @return {void} 該方法無回傳值。
 */
function handleRowClick(datatableEl, popover, event) {
    if (!popover) return;
    console.log();

    let data = datatableEl.datatableObj.row(event.currentTarget.parentElement._DT_RowIndex).data();
    const popoverEl = document.querySelector(`#${popover}`);

    if (popoverEl) {
        popoverEl.classList.remove("!hidden");

        const dialog_vt = popoverEl.querySelector(".dialog-vt");
        const shop_iframe = dialog_vt.querySelector(".shop-iframe");
        const order_iframe = dialog_vt.querySelector(".order-iframe");
        const shop_popover_loader = dialog_vt.querySelector(".shop-popover-placeholder");

        if (shop_iframe && shop_popover_loader) {
            shop_iframe.onload = () => shop_popover_loader.classList.add('hidden');
            shop_popover_loader.classList.remove("hidden");
            shop_iframe.src = `/shop/item/edit/${data.id}`;
            document.body.style.overflow = "hidden";
        } else if (order_iframe && shop_popover_loader) {
            console.log(data);
            order_iframe.onload = () => shop_popover_loader.classList.add('hidden');
            shop_popover_loader.classList.remove("hidden");
            order_iframe.src = `/customer/order/${data.id}`;
            document.body.style.overflow = "hidden";
        }

        const dialog_closebtn = dialog_vt.querySelector(".dialog-closebtn");

        if (dialog_closebtn) {
            const closeHandler = () => {
                document.body.style.overflow = "";
                popoverEl.classList.add("!hidden");
                shop_popover_loader.classList.remove("hidden");

                const iframe = shop_iframe || order_iframe;
                if (iframe) {
                    iframe.contentWindow.document.write("");
                    iframe.contentWindow.document.close();
                }
            };
            dialog_closebtn.onclick = closeHandler;
        }
    }
}

document.addEventListener('CGTABLE::init', datatables);
document.addEventListener('DOMContentLoaded', datatables);
