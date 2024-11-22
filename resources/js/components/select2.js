import jQuery from 'jquery/dist/jquery.js';
import select2 from 'select2';

select2();
/**
 * 用於顯示多語言錯誤和信息的語言包
 */
let language = {
    errorLoading: function () {
        return '無法載入結果。';
    },
    inputTooLong: function (args) {
        let overChars = args.input.length - args.maximum;

        return '請刪掉' + overChars + '個字元';
    },
    inputTooShort: function (args) {
        let remainingChars = args.minimum - args.input.length;

        return '請再輸入' + remainingChars + '個字元';
    },
    loadingMore: function () {
        return '載入中…';
    },
    maximumSelected: function (args) {
        return '你只能選擇最多' + args.maximum + '項';
    },
    noResults: function () {
        return '沒有找到相符的項目';
    },
    searching: function () {
        return '搜尋中…';
    },
    removeAllItems: function () {
        return '刪除所有項目';
    },
    removeItem: function () {
        return '刪除項目';
    }
};

/**
 * 初始化所有帶有 class 名為 select2 和 select2-tag 的元素，並基於各元素的數據屬性來設置 Select2 選項，
 * 包括占位符 (placeholder)、允許清除 (allowClear)、寬度 (width) 及默認值 (value) 等。
 *
 * @return {void} 不返回任何值。
 */
function select2Loader() {
    let select2s = document.querySelectorAll('.select2');
    for (let select2El of select2s) {
        let placholder = select2El.dataset.placholder;
        let allowclear = select2El.dataset.allowclear;
        let width = select2El.dataset.width;
        let value = select2El.dataset.value;
        if (placholder === undefined) placholder = "請選擇物件";
        if (allowclear === undefined) allowclear = false;
        if (width === undefined) width = "100%";
        jQuery(select2El).select2({
            matcher: matchCustom,
            placholder: placholder,
            language: language,
            allowClear: allowclear,
            width: width,
        });
        if (value !== undefined)
            jQuery(select2El).val(value).trigger('change');
    }
    let select2_tags = document.querySelectorAll('.select2-tag');
    for (let select2Tag of select2_tags) {
        let placholder = select2Tag.dataset.placholder;
        let allowclear = select2Tag.dataset.allowclear;
        let width = select2Tag.dataset.width;
        if (placholder === undefined) placholder = "請輸入標籤或選擇有的標籤";
        if (allowclear === undefined) allowclear = false;
        if (width === undefined) width = "100%";
        let value = select2Tag.dataset.value;
        jQuery(select2Tag).select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placholder: placholder,
            language: language,
            allowClear: allowclear,
            width: width,
        });
        if (value !== undefined)
            jQuery(select2Tag).val(value).trigger('change');
    }
}

/**
 * 根據給定的條件篩選並返回匹配的數據對象，若對象匹配則在其文本後加上“ (匹配)”。
 *
 * @param {Object} params - 包含搜索條件的參數對象，必須有 `term` 屬性表示搜索關鍵字。
 * @param {Object} data - 被篩選的數據對象，必須有 `text` 屬性或 `id` 屬性來進行匹配。
 * @return {Object|null} 返回修改後的數據對象，如果不匹配則返回 `null`。
 */
function matchCustom(params, data) {
    // If there are no search terms, return all of the data
    //console.log(params, data);
    if (jQuery.trim(params.term) === '') {
        return data;
    }

    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
        return null;
    }

    // `params.term` should be the term that is used for searching
    // `data.text` is the text that is displayed for the data object
    if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
        let modifiedData = jQuery.extend({}, data, true);
        modifiedData.text += ' (匹配)';

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }
    if (data.id.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
        let modifiedData = jQuery.extend({}, data, true);
        modifiedData.text += ' (匹配)';

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}

document.addEventListener("DOMContentLoaded", function () {
    select2Loader();
})
