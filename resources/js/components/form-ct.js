import axios from "axios";
import * as Utils from "./utils";

/**
 * 處理用戶登錄的函數。此函數會根據輸入的帳號密碼資訊進行驗證，並在驗證通過後發送登錄請求。
 *
 * @param {HTMLElement} element 包含登錄表單和其他相關元素的 HTML 元素。
 * @return {boolean} 如果初始化的登錄元素無效則返回 false；否則不返回任何值。
 */
function Auth_login(element) {
    let options = element.cgoptions;
    console.log(options);
    if (options.rawOptions.target === null) return false;
    for (let tracksListElement of options.tracksList) {
        if (tracksListElement === null) return false;
    }
    /**
     * @var {HTMLButtonElement}
     */
    let login = options.tracksList.login;
    /**
     * @var {HTMLButtonElement}
     */
    let username = options.tracksList.username;
    /**
     * @var {HTMLButtonElement}
     */
    let password = options.tracksList.password;

    let alert = element.querySelector(options.rawOptions.target);
    let validateFn = () => {
        let b = username.validateStatus && password.validateStatus;
        if (!b) {
            if (!username.validateStatus) {
                username.tippy.show();
            }
            if (!password.validateStatus) {
                password.tippy.show();
            }
        }
        return b;
    };
    let proccessFn = () => {
        axios.post('/login', {
            username: username.value,
            password: Utils.encodeContext(password.value)['compress'],
            token: element.token,
        }, {
            adapter: "fetch",
            method: "POST",
            responseType: "json",
        }).then((response) => {
            let data = response.data;
            element.token = data.token;
            console.log(data);
            console.log(element.token);
            if (alert !== null) {
                alert.innerHTML = data.message;
            }
            if (data.error_keys !== undefined) {
                for (let key of data.error_keys) {
                    if (key !== "token" && key !== "login") {
                        options.tracksList[key].tippy.show();
                    }
                }
            }
            common_proccess(data);
        });
    }
    let submitFn = () => {
        if (element.dataset.token === undefined) return false;
        if (element.token === undefined) element.token = element.dataset.token;
        let r = validateFn();
        if (r) {
            proccessFn();
        }
        return false;
    };
    login.form.onsubmit = submitFn;
    login.onclick = submitFn;
}

/**
 * Auth_Register 方法用于处理用户注册过程中的各类验证和数据提交操作。
 *
 * @param {HTMLElement} element 包含注册表单及其各项配置的元素
 * @return {boolean} 表单验证或处理是否成功，成功返回 true，失败返回 false
 */
function Auth_Register(element) {
    let options = element.cgoptions;
    console.log(options);
    if (options.rawOptions.target === null) return false;
    for (let tracksListElement of options.tracksList) {
        if (tracksListElement === null) return false;
    }
    /**
     * @var {HTMLButtonElement}
     */
    let register = options.tracksList.register;
    /**
     * @var {HTMLInputElement}
     */
    let username = options.tracksList.username;
    /**
     * @var {HTMLInputElement}
     */
    let password = options.tracksList.password;
    /**
     * @var {HTMLInputElement}
     */
    let password_confirmation = options.tracksList.password_confirmation;
    /**
     * @var {HTMLInputElement}
     */
    let email = options.tracksList.email;
    /**
     * @var {HTMLInputElement}
     */
    let phone = options.tracksList.phone;

    let alert = element.querySelector(options.rawOptions.target);
    let validateFn = () => {
        let b = username.validateStatus && password.validateStatus && password_confirmation.validateStatus && email.validateStatus && phone.validateStatus && password.value === password_confirmation.value;
        if (!b) {
            if (!username.validateStatus) {
                username.tippy.show();
            }
            if (!password.validateStatus) {
                password.tippy.show();
            }
            if (!password_confirmation.validateStatus) {
                password_confirmation.tippy.show();
            }
            if (!email.validateStatus) {
                email.tippy.show();
            }
            if (!phone.validateStatus) {
                phone.tippy.show();
            }
            if (password.value !== password_confirmation.value) {
                password_confirmation.tippy.show();
            }
        }
        return b;
    };
    let proccessFn = () => {
        axios.post('/register', {
            username: username.value,
            email: email.value,
            phone: phone.value,
            password: Utils.encodeContext(password.value)['compress'],
            password_confirmation: Utils.encodeContext(password_confirmation.value)['compress'],
            token: element.token,
        }, {
            adapter: "fetch",
            method: "POST",
            responseType: "json",
        }).then((response) => {
            let data = response.data;
            element.token = data.token;
            console.log(data);
            console.log(element.token);
            if (alert !== null) {
                alert.innerHTML = data.message;
            }
            if (data.error_keys !== undefined) {
                for (let key of data.error_keys) {
                    if (key !== "token" && key !== "register") {
                        options.tracksList[key].tippy.show();
                    }
                }
            }
            common_proccess(data);
        });
    }
    let submitFn = () => {
        if (element.dataset.token === undefined) return false;
        if (element.token === undefined) element.token = element.dataset.token;
        let r = validateFn();
        if (r) {
            proccessFn();
        }
        return false;
    };
    register.form.onsubmit = submitFn;
    register.onclick = submitFn;
}

/**
 * 忘記密碼處理函數，處理用戶重置密碼的請求。
 *
 * @param {HTMLElement} element - 包含重置密碼所需元素的父級HTMLElement。
 * @return {boolean} 成功綁定提交事件時返回false。
 */
function Auth_ForgetPassword(element) {
    let options = element.cgoptions;
    console.log(options);
    if (options.rawOptions.target === null) return false;
    for (let tracksListElement of options.tracksList) {
        if (tracksListElement === null) return false;
    }
    /**
     * @var {HTMLButtonElement}
     */
    let forget = options.tracksList.forget;
    /**
     * @var {HTMLInputElement}
     */
    let email = options.tracksList.email;

    let alert = element.querySelector(options.rawOptions.target);
    let validateFn = () => {
        let b = email.validateStatus;
        if (!b) {
            if (!email.validateStatus) {
                email.tippy.show();
            }
        }
        return b;
    };
    let proccessFn = () => {
        axios.post('/forget-password', {
            email: email.value,
            token: element.token,
        }, {
            adapter: "fetch",
            method: "POST",
            responseType: "json",
        }).then((response) => {
            let data = response.data;
            element.token = data.token;
            console.log(data);
            console.log(element.token);
            if (alert !== null) {
                alert.innerHTML = data.message;
            }
            if (data.error_keys !== undefined) {
                for (let key of data.error_keys) {
                    if (key !== "token" && key !== "forget") {
                        options.tracksList[key].tippy.show();
                    }
                }
            }
            common_proccess(data);
        });
    }
    let submitFn = () => {
        if (element.dataset.token === undefined) return false;
        if (element.token === undefined) element.token = element.dataset.token;
        let r = validateFn();
        if (r) {
            proccessFn();
        }
        return false;
    };
    forget.form.onsubmit = submitFn;
    forget.onclick = submitFn;
}

function ShareTable_add(element) {
    let options = element.cgoptions;
    let i = 0;
    console.log(options);
    if (options.rawOptions.target === null) return false;
    console.log(i++)
    for (let tracksListElement of options.tracksList) {
        if (tracksListElement === null) return false;
    }
    console.log(i++)
    /**
     * resetpassword,password_confirmation,password
     * @var {HTMLButtonElement}
     */
    let addFile = options.tracksList.addFile;
    /**
     * @var {HTMLInputElement}
     */
    let password_confirmation = options.tracksList.password_confirmation;
    /**
     * @var {HTMLInputElement}
     */
    let password = options.tracksList.password;
    let shareTableName = options.tracksList.shareTableName;
    let shareTableShortCode = options.tracksList.shareTableShortCode;
    let files = options.tracksList.files;

    console.log(i++)
    let alert = element.querySelector(options.rawOptions.target);
    let validateFn = () => {
        let b = password.validateStatus && password_confirmation.validateStatus && password.value === password_confirmation.value && shareTableName.validateStatus && shareTableShortCode.validateStatus;
        if (!b) {
            if (!shareTableName.validateStatus) {
                shareTableName.tippy.show();
            }
            if (!shareTableShortCode.validateStatus) {
                shareTableShortCode.tippy.show();
            }
            if (!password.validateStatus) {
                password.tippy.show();
            }
            if (!password_confirmation.validateStatus) {
                password_confirmation.tippy.show();
            }
            if (password.value !== password_confirmation.value) {
                password_confirmation.tippy.show();
            }
        }
        return b;
    };
    let proccessFn = () => {
        axios.post('/passwordreset', {
            password: Utils.encodeContext(password.value)['compress'],
            password_confirmation: Utils.encodeContext(password_confirmation.value)['compress'],
            token2: element.token,
            token: token2.value,
            email: email.value,
        }, {
            adapter: "fetch",
            method: "POST",
            responseType: "json",
        }).then((response) => {
            let data = response.data;
            element.token = data.token;
            console.log(data);
            console.log(element.token);
            if (alert !== null) {
                alert.innerHTML = data.message;
            }
            if (data.error_keys !== undefined) {
                for (let key of data.error_keys) {
                    if (key !== "token" && key !== "resetpassword") {
                        options.tracksList[key].tippy.show();
                    }
                }
            }
            common_proccess(data);
        });
    }
    let submitFn = () => {
        if (element.dataset.token === undefined) return false;
        if (element.token === undefined) element.token = element.dataset.token;
        let r = validateFn();
        if (r) {
            proccessFn();
        }
        return false;
    };
    addFile.form.onsubmit = submitFn;
    addFile.onclick = submitFn;
}

/**
 * Auth_ResetPassword 函式用於重設用戶的密碼。
 *
 * @param {HTMLElement} element - 包含重設密碼所需資料的 DOM 元素。
 * @return {boolean} 返回 false 以防止表單的默認提交行為。
 */
function Auth_ResetPassword(element) {
    let options = element.cgoptions;
    let i = 0;
    console.log(options);
    if (options.rawOptions.target === null) return false;
    console.log(i++)
    for (let tracksListElement of options.tracksList) {
        if (tracksListElement === null) return false;
    }
    console.log(i++)
    /**
     * resetpassword,password_confirmation,password
     * @var {HTMLButtonElement}
     */
    let resetpassword = options.tracksList.resetpassword;
    /**
     * @var {HTMLInputElement}
     */
    let password_confirmation = options.tracksList.password_confirmation;
    /**
     * @var {HTMLInputElement}
     */
    let password = options.tracksList.password;
    let email = options.tracksList.email;
    let token2 = options.tracksList.token2;

    console.log(i++)
    let alert = element.querySelector(options.rawOptions.target);
    let validateFn = () => {
        let b = password.validateStatus && password_confirmation.validateStatus && password.value === password_confirmation.value;
        if (!b) {
            if (!password.validateStatus) {
                password.tippy.show();
            }
            if (!password_confirmation.validateStatus) {
                password_confirmation.tippy.show();
            }
            if (password.value !== password_confirmation.value) {
                password_confirmation.tippy.show();
            }
        }
        return b;
    };
    let proccessFn = () => {
        axios.post('/passwordreset', {
            password: Utils.encodeContext(password.value)['compress'],
            password_confirmation: Utils.encodeContext(password_confirmation.value)['compress'],
            token2: element.token,
            token: token2.value,
            email: email.value,
        }, {
            adapter: "fetch",
            method: "POST",
            responseType: "json",
        }).then((response) => {
            let data = response.data;
            element.token = data.token;
            console.log(data);
            console.log(element.token);
            if (alert !== null) {
                alert.innerHTML = data.message;
            }
            if (data.error_keys !== undefined) {
                for (let key of data.error_keys) {
                    if (key !== "token" && key !== "resetpassword") {
                        options.tracksList[key].tippy.show();
                    }
                }
            }
            common_proccess(data);
        });
    }
    let submitFn = () => {
        if (element.dataset.token === undefined) return false;
        if (element.token === undefined) element.token = element.dataset.token;
        let r = validateFn();
        if (r) {
            proccessFn();
        }
        return false;
    };
    resetpassword.form.onsubmit = submitFn;
    resetpassword.onclick = submitFn;
}

/**
 * 根據數據的類型屬性進行處理，若類型存在則在3秒後跳轉到指定的URL。
 *
 * @param {Object} data - 需要處理的數據對象。
 * @param {string} data.type - 指示是否進行跳轉的類型屬性。
 * @param {string} data.redirect - 跳轉的目標URL。
 *
 * @return {void} 此函數不返回任何值。
 */
function common_proccess(data) {
    if (data.type) {
        setTimeout(() => {
            window.location.href = data.redirect;
        }, 3000);
    }
}

/**
 * 處理所有帶有 .form-ct 類的表單。
 * 根據表單數據集中的 fn 和 tracks 屬性來調用不同的身份驗證方法。
 * 將對應的跟蹤列表分配給表單並存儲在 cgoptions 屬性中。
 *
 * @return {boolean} 如果 fn 或 tracks 沒有定義，則返回 false。
 */
function form_ct() {
    let forms = document.querySelectorAll('.form-ct');
    for (let form of forms) {
        let fn = form.dataset.fn;
        let tracks = form.dataset.tracks;
        if (fn === undefined) return false;
        if (tracks === undefined) return false;
        let trackslist = [];
        if (tracks.split(",").length > 0) {
            for (let track of tracks.split(",")) {
                if (form.querySelector(track) === null) {
                    trackslist[track] = form[track];
                } else {
                    trackslist[track] = form.querySelector(track);
                }
            }
        } else {
            if (form.querySelector(tracks) === null) {
                trackslist[tracks] = form[tracks];
            } else {
                trackslist[tracks] = form.querySelector(tracks);
            }
        }

        let addressList = {
            root: form,
            rawOptions: form.dataset,
            fn: fn,
            tracks: tracks,
            tracksList: trackslist,
        };
        form.cgoptions = addressList;
        switch (fn) {
            case "Auth.login":
                Auth_login(form);
                break;
            case "Auth.Register":
                Auth_Register(form);
                break;
            case "Auth.ForgetPassword":
                Auth_ForgetPassword(form);
                break;
            case "Auth.ResetPassword":
                Auth_ResetPassword(form);
                break;
            case "ShareTable.add":
                ShareTable_add(form);
                break;
            default:
                break;
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    form_ct();
})
