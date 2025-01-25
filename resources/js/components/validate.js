/**
 * 檢查給定的 validate 是否符合必須的條件。
 *
 * @param {Object} validate - 要檢查的對象，其需具有 value 屬性和 validateStatus 屬性。
 * @return {boolean} - 如果 validate 的 value 長度大於 0，則設置 validateStatus 為 true，否則設置為 false。
 */
function checkRequired(validate) {
    if (validate.value.length > 0) {
        validate.validateStatus = true;
    }
    validate.validateStatus = false;
}

/**
 * 檢查電子郵件地址的有效性。
 *
 * @param {Object} validate 包含電子郵件地址和驗證狀態的物件
 * @param {string} validate.value 需要檢查的電子郵件地址
 * @param {boolean} validate.validateStatus 驗證狀態，將被設置為檢查結果
 *
 * @return {void} 沒有返回值，結果將直接修改 validate.validateStatus
 */
function checkEmail(validate) {
    const emailRegex = /^(?:(?:[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*)|("(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?)*\.?$)/;

    // Length constraints per RFC 5321
    if (validate.value.length > 254) {
        validate.validateStatus = false;
    }
    const [localPart, domainPart] = validate.value.split('@');
    if (!localPart || !domainPart || localPart.length > 64 || domainPart.length > 255) {
        validate.validateStatus = false;
    }
    validate.validateStatus = emailRegex.test(validate.value);
}

/**
 * 驗證所有具備 class `validate` 的輸入框，根據內部的 `data-method` 屬性執行不同的驗證邏輯，
 * 支援的驗證方法包括數字、電子郵件以及必填項驗證。
 *
 * @return {boolean} 返回 `true` 表示所有驗證都通過，否則返回 `false`
 */
function validate() {
    let validates = document.querySelectorAll('.validate');
    for (let validate of validates) {
        let method = validate.dataset.method;
        if (method === undefined) continue;
        /**
         * @var {HTMLInputElement} validate
         */
        switch (method) {
            case "number":
                let numberOfDigits = validate.dataset.numberofdigits;
                let negative = validate.dataset.negative;
                let step = validate.dataset.step;
                if (numberOfDigits === undefined) return false;
                if (step === undefined) step = "1";
                validate.events = [];
                validate.registerEvent = (method) => {
                    validate.events[validate.events.length] = method;
                };
                validate.isRegistered = (method) => {
                    let filter = validate.events.find(m => m === method);
                    return filter !== undefined;
                };

                if (validate.dataset.onmouseup === "true") {
                    validate.registerEvent('wheel');
                }
                validate.on = (method, callback) => {
                    if (validate.isRegistered(method)) {
                        document.addEventListener('CGV::' + method, function (e) {
                            if (e.detail.html.isRegistered(method) && validate === e.detail.html) {
                                callback(e)
                            }
                        });
                    }
                };

                validate.dstep = Number.parseFloat(step);

                let regexPattern = '^';  // 正則表達式的開頭
                validate.negative = false;
                if (negative === 'true') {
                    regexPattern += '-?';  // 如果允許負數，添加可選的負號
                    validate.negative = true;
                }
                regexPattern += '\\d+';  // 匹配整數部分
                validate.numberOfDigits = numberOfDigits;
                if (numberOfDigits > 0) {
                    regexPattern += '(\\.\\d{1,' + numberOfDigits + '})?';  // 匹配小數點及其後最多指定位數的小數
                }
                regexPattern += '$';  // 正則表達式的結尾
                let regex = new RegExp(regexPattern);
                validate.pattern = regexPattern;
                validate.previousValue = validate.value;
                validate.addEventListener('input', function (e) {
                    if (e.type === "input" && e.inputType === undefined) {
                        if (regex.test(validate.value)) {
                            validate.previousValue = parseFloat(validate.value).toFixed(parseInt(numberOfDigits));
                        }
                    } else if (regex.test(e.data) || (e.data === "." && validate.previousValue.indexOf(".") === -1)) {
                        validate.previousValue = parseFloat(validate.value).toFixed(parseInt(numberOfDigits));
                    }
                });
                validate.addEventListener('blur', function (e) {
                    validate.value = parseFloat(validate.value).toFixed(parseInt(numberOfDigits));
                });

                validate.addEventListener('wheel', function (e) {
                    e.preventDefault();  // 防止預設的滾動行為
                    if (e.deltaY > 0) {
                        if (!validate.negative) {
                            if (Number.parseFloat(validate.value) > 0) {
                                validate.value = Number.parseFloat(validate.value) - validate.dstep;
                            }
                        } else {
                            validate.value = Number.parseFloat(validate.value) - validate.dstep;
                        }
                    } else {
                        validate.value = Number.parseFloat(validate.value) + validate.dstep;
                    }
                    document.dispatchEvent(new CustomEvent('CGV::wheel', {
                        cancelable: false,
                        detail: {
                            html: validate,
                            originEvent: e,
                            value: validate.value,
                        }
                    }));
                });
                break;
            case "email":
                if (validate.validateStatus === undefined || validate.validateStatus === null) {
                    validate.validateStatus = false;
                }
                setTimeout(function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                    checkEmail(validate);
                }, 100);
                validate.oninput = function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                    checkEmail(validate);
                }
                validate.onchange = function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                    checkEmail(validate);
                }
                validate.onfocus = function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                    checkEmail(validate);
                }
                break;
            case "required":
                if (validate.validateStatus === undefined || validate.validateStatus === null) {
                    validate.validateStatus = false;
                }
                setTimeout(function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }, 100);
                validate.oninput = function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }
                validate.onchange = function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }
                validate.onfocus = function () {
                    checkRequired(validate);
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }
                break;
            case "default":
                if (validate.validateStatus === undefined || validate.validateStatus === null) {
                    validate.validateStatus = false;
                }
                setTimeout(function () {
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }, 100);
                validate.oninput = function () {
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }
                validate.onchange = function () {
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }
                validate.onfocus = function () {
                    checkMaxLength(validate);
                    checkMinLength(validate);
                }
                break;
        }
    }
}

/**
 * 檢查輸入值的長度是否符合最小長度要求，並更新驗證狀態。
 *
 * @param {Object} self - 包含輸入值和驗證狀態的對象，必須包含 value 和 minLength 屬性。
 * @return {void}
 */
function checkMinLength(self) {
    if (self.value.length < self.minLength) {
        self.validateStatus = false;
    } else {
        self.validateStatus = true;
    }
}

/**
 * 檢查輸入值是否超過最大長度。
 *
 * @param {Element} self - 包含輸入值和最大長度的對象。
 * @param {string} self.value - 需要檢查的輸入值。
 * @param {number} self.maxLength - 設定的最大長度。
 * @param {boolean} self.validateStatus - 驗證狀態。
 *
 * @return {void}
 */
function checkMaxLength(self) {
    if (self.value.length > self.maxLength) {
        self.validateStatus = false;
    } else {
        self.validateStatus = true;
    }
}

/**
 * loader 方法用於加載特定資源或執行初始化操作。
 * @return {void} 沒有返回值。
 */
function loader() {
    validate();
}

document.addEventListener('DOMContentLoaded', loader);
