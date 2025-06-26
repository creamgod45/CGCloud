import * as Utils from './utils.js';
/**
 * 切換密碼輸入欄位的顯示和隱藏狀態。
 *
 * @param {Element} ct - 觸發切換操作的 HTML 元素
 * @param {string} target - 目標密碼輸入欄位的 CSS 選擇器
 * @return {void} 無返回值
 */
function password_toggle(ct, target) {
    ct.onclick = () => {
        let tel = document.querySelector(target);
        if (tel !== null) {
            if (tel.type === "password") {
                ct.innerHTML = "<i class=\"fa-regular fa-eye-slash\"></i>";
                tel.type = 'text';
            } else {
                ct.innerHTML = "<i class=\"fa-regular fa-eye\"></i>";
                tel.type = 'password';
            }
        }
    };
}

/**
 * 初始化并配置用于滚动和选择的 datalist 元素。
 *
 * @param {HTMLElement} ct - 包含“next”和“prev”按钮引用的容器元素。
 * @param {string} target - 用于选择 datalist 元素的 CSS 选择器字符串。
 * @return {void} - 此函数没有返回值。
 */
function datalist_selector(ct, target) {
    let datalist = document.querySelector(target);

    function datalist_init() {
        //console.log("datalist_init");
        let s = Utils.generateRandomString(5);
        let i = 0;
        let str_arry = [];
        for (let child of datalist.children) {
            let id = `ct_dls_${s}_${i++}`;
            child.id = id;
            str_arry.push(id);
            if (datalist.dataset.index === null) {
                datalist.dataset.index = "-1";
            }
        }
        datalist.dataset.lists = str_arry.join(",");

        let prevfn = () => {
            console.log("prevfn");
            let index = datalist.dataset.index;
            if (index !== null) {
                if (index === "0") {
                    index = 0;
                }
                let number = Number.parseInt(index);
                if (number !== -1) {
                    if (datalist.dataset.lists !== null) {
                        let lists = datalist.dataset.lists;
                        let strings = lists.split(',');
                        let string = strings[--number];
                        let el = document.querySelector(`#${string}`);
                        if (el !== null) {
                            el.scrollIntoView({behavior: 'smooth', block: 'nearest'});
                            setTimeout(() => createRipple(el.children[0]), 400);
                            datalist.dataset.index = number;
                        }
                    }
                }
            }
        };
        let nextfn = () => {
            console.log("nextfn");
            let index = datalist.dataset.index;
            if (index !== null) {
                let number = Number.parseInt(index);
                if (number !== datalist.children.length) {
                    if (datalist.dataset.lists !== null) {
                        let lists = datalist.dataset.lists;
                        let strings = lists.split(',');
                        let string = strings[++number];
                        let el = document.querySelector(`#${string}`);
                        if (el !== null) {
                            el.scrollIntoView({behavior: 'smooth', block: 'nearest'});
                            setTimeout(() => createRipple(el.children[0]), 400);
                            datalist.dataset.index = number;
                        }
                    }
                }
            }
        };
        let next = document.querySelector(ct.dataset.next);
        if (next !== null) {
            next.onclick = nextfn;
        }
        let prev = document.querySelector(ct.dataset.prev);
        if (prev !== null) {
            prev.onclick = prevfn;
        }

        datalist.addEventListener("wheel", function (event) {
            if (event.deltaX < 0) {
                console.log('Wheel moved left');
                prevfn();
            } else if (event.deltaX > 0) {
                console.log('Wheel moved right');
                nextfn();
            }
        });
    }

    if (datalist !== null) {
        datalist_init();

        // Create an instance of MutationObserver
        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    //console.log('A child node has been added or removed.');
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            //console.log('Added node:', node);
                            if (!node.classList.contains("ripple")) datalist_init();
                        }
                    });
                }
            }
        });

        // Configuration of the observer
        const config = {childList: true, subtree: true};

        // Start observing the target node for configured mutations
        observer.observe(datalist, config);
    }
}

/**
 * 在指定元素上創建一個漣漪效果。
 *
 * @param {Element} el - 用於產生漣漪效果的DOM元素。
 * @return {void} 不返回任何值。
 */
function createRipple(el) {
    //console.log(event)
    const button = el;
    const circle = document.createElement("span");
    circle.classList.add("ripple");
    button.appendChild(circle);

    let b = button.getBoundingClientRect();
    const diameter = Math.max(b.width, b.height);
    const radius = diameter / 2;

    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = "0px";
    circle.style.top = "0px";

    setTimeout(() => {
        circle.remove();
    }, 600)
}

// 發送信箱驗證碼
/**
 * 發送郵件驗證碼到使用者的電子郵箱。
 *
 * @param {HTMLElement} ct - 觸發點擊事件的元素。
 * @param {string} target - 顯示返回訊息目標元素的選擇器。
 * @return {void}
 */
function sendMailVerifyCode_profile_email(ct, target) {
    ct.onclick = () => {
        if (ct.dataset.token === null) return false;
        ct.disabled = true;
        ct.classList.add("btn-dead");
        ct.classList.remove("btn-color7");
        let csrf = document.querySelector("#csrf_token");
        if (csrf === null) {
            ct.disabled = false;
            ct.classList.remove("btn-dead");
            ct.classList.add("btn-color7");
            return false;
        }
        let formdata = new FormData();
        formdata.append('token', ct.dataset.token);
        let onfulfilled = async (res) => {
            //console.log(res);

            let markSendStatus = ct.dataset.marksendstatus;
            let markSendStatusEl = document.querySelector(markSendStatus);
            if(markSendStatusEl !== null){
                markSendStatusEl.tippy.show();
                markSendStatusEl.dataset.send = "true";
            }
            let json;
            try {
                json = await res.json();
            } catch (e) {
                console.error(e);
                return false;
            }
            //console.log(json);
            let el = document.querySelector(target);
            ct.dataset.token = json.token;
            el.innerText = json.message;
            let cooldown = parseInt(json.cooldown);
            let date = new Date();
            let seconds = cooldown - Math.floor((date.valueOf() / 1000));
            /*ct.cooldown = seconds;
            console.log(seconds);
            setInterval(() => {
                ct.cooldown = ct.cooldown - 1;
                console.log(ct.cooldown);
            }, 1000);*/
            setTimeout(() => {
                clearInterval(ct.timer);
                ct.classList.remove("btn-dead");
                ct.classList.add("btn-color7");
                ct.disabled = false;
            }, seconds * 1000);
        };
        fetch('/profile/email/sendMailVerifyCode', {
            method: "post", headers: {
                'X-CSRF-TOKEN': csrf.value
            }, body: formdata,
        })
            .then(onfulfilled)
            .catch(onfulfilled);
    };
}

// 信箱的驗證碼驗證
/**
 * 此方法驗證用戶的電子郵件驗證碼，並更新前端元素以反映驗證結果。
 *
 * @param {HTMLElement} ct - 觸發驗證操作的按鈕元素，它包含一系列數據屬性以查找相關元素和令牌。
 * @param {string} target - 包含用戶輸入的驗證碼的目標元素的選擇器。
 * @return {void}
 */
function verifyCode_profile_email(ct, target) {
    if (ct.dataset.action !== null && ct.dataset.action1 !== null && ct.dataset.action2 !== null && ct.dataset.action3 !== null && ct.dataset.action4 !== null && ct.dataset.token !== null) {
        ct.onclick = () => {
            ct.disabled = true;
            let actionel = document.querySelector(ct.dataset.action);
            let action1el = document.querySelector(ct.dataset.action1);
            let action2el = document.querySelector(ct.dataset.action2);
            let action3el = document.querySelector(ct.dataset.action3);
            let action4el = document.querySelector(ct.dataset.action4);
            let targetel = document.querySelector(target);
            if(targetel.dataset.send !== "true"){
                action1el.tippy.show();
                ct.disabled = false;
                return false;
            }
            if (targetel.value === null) {
                targetel.tippy.show();
                ct.disabled = false;
                return false;
            }
            if (targetel.value === "") {
                targetel.tippy.show();
                ct.disabled = false;
                return false;
            }
            if (targetel.minLength !== null) {
                if (targetel.value.length < targetel.minLength) {
                    targetel.tippy.show();
                    ct.disabled = false;
                    return false;
                }
            }
            if (ct.dataset.token === null) {
                ct.disabled = false;
                return false;
            }
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) {
                ct.disabled = false;
                return false;
            }
            let formdata = new FormData();
            formdata.append("code", targetel.value);
            formdata.append('token', ct.dataset.token);
            let onfulfilled = async (res) => {
                //console.log(res);
                let json = await res.json();
                //console.log(json);
                if (res.status === 200) {
                    if (json.access_token !== "") {
                        let htmlInputElement = document.createElement("input");
                        htmlInputElement.value = json.access_token;
                        htmlInputElement.name = "sendMailVerifyCodeToken";
                        htmlInputElement.id = "sendMailVerifyCodeToken";
                        htmlInputElement.type = "hidden";
                        actionel.innerHTML = "";
                        actionel.append(htmlInputElement);
                        action1el.remove();
                        action2el.remove();
                        action3el.disabled = false;
                        action4el.disabled = false;
                        ct.dataset.token = json.token;
                    }
                } else {
                    ct.disabled = false;
                    targetel.tippy.show();
                }
            };
            fetch('/profile/email/verifyCode', {
                method: "post", body: formdata, headers: {
                    'X-CSRF-TOKEN': csrf.value
                },
            })
                .then(onfulfilled)
                .catch(() => {
                    ct.disabled = false;
                    return false;
                });
        };
    }
}

/**
 * 用来发送新的邮箱验证代码至服务器的方法。
 *
 * @param {HTMLElement} ct 元素，包含一些必要的dataset值，如token和data
 * @param {string} target 查询选择器字符串，表示要显示结果的目标元素
 * @return {void} 无返回值
 */
function newMailVerifyCode_profile_email(ct, target) {
    if (ct.dataset.token !== null && ct.dataset.data !== null && ct.dataset.result !== null) {
        ct.onclick = () => {
            ct.disabled = true;
            let targetel = document.querySelector(target);
            let needFinishEl = document.querySelector(ct.dataset.needfinish);
            if(needFinishEl !== null){
                needFinishEl.tippy.show();
                ct.disabled = false;
                return false;
            }
            let data = document.querySelector(ct.dataset.data);
            if (data.value === null) {
                ct.disabled = false;
                data.tippy.show();
                return false;
            }
            if (data.value === "") {
                ct.disabled = false;
                data.tippy.show();
                return false;
            }
            if (!Utils.validateEmail(data.value)) {
                ct.disabled = false;
                data.tippy.show();
                return false;
            }
            if (data.maxLength !== null) {
                if (data.value.length > data.maxLength) {
                    ct.disabled = false;
                    data.tippy.show();
                    return false;
                }
            }
            if (ct.dataset.token === "") {
                ct.disabled = false;
                return false;
            }
            if (ct.dataset.token === null) {
                ct.disabled = false;
                return false;
            }
            if (ct.dataset.token === undefined) {
                ct.disabled = false;
                return false;
            }
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) {
                ct.disabled = false;
                return false;
            }
            let formdata = new FormData();
            formdata.append("email", data.value);
            formdata.append("token", ct.dataset.token);
            let onfulfilled = async (res) => {
                //console.log(res);
                let json = await res.json();
                ct.dataset.token = json.token;
                targetel.innerText = json.message;
                let cooldown = parseInt(json.cooldown);
                let date = new Date();
                let seconds = cooldown - Math.floor((date.valueOf() / 1000));
                if (cooldown !== null) {
                    //ct.cooldown = seconds;
                    ct.classList.remove("btn-color7");
                    ct.classList.add("btn-dead");
                    ct.timer = setInterval(() => {
                        clearInterval(ct.timer);
                        ct.classList.add("btn-color7");
                        ct.classList.remove("btn-dead");
                        ct.disabled = false;
                    }, seconds * 1000)
                }
            };
            fetch("/profile/email/newMailVerifyCode", {
                method: 'post', body: formdata, headers: {
                    'X-CSRF-TOKEN': csrf.value
                },
            }).then(onfulfilled).catch(onfulfilled);
        };
    }
}

/**
 * 更新用戶的電子郵件地址。
 *
 * @param {HTMLElement} ct - 點擊事件的觸發元素，它包含了一些所需的數據集屬性，如 token 和 method。
 * @param {string} target - 用於選擇目標元素的選擇器，目標元素用於在成功更改電子郵件後隱藏彈出提示。
 * @return {void} 沒有返回值。
 */
function profileUpdateEmail(ct, target) {
    if (ct.dataset.token !== null && ct.dataset.method !== null) {
        ct.onclick = () => {
            ct.disabled = true;
            let count = 0;
            let value1el = document.querySelector(ct.dataset.value1);
            let value2el = document.querySelector(ct.dataset.value2);
            let value3el = document.querySelector(ct.dataset.value3);
            let value4el = document.querySelector(ct.dataset.value4);
            let resultel = document.querySelector(ct.dataset.result);
            let targetel = document.querySelector(target);
            if (value1el.value === null) {
                ct.disabled = false;
                value1el.tippy.show();
                return false;
            }
            if (value2el.value === null) {
                ct.disabled = false;
                return false;
            }
            if(value3el === null) {
                if(value4el !== null && value4el.hasOwnProperty("tippy")) {
                    value4el.tippy.show();
                }
                ct.disabled = false;
                return false;
            }
            if (value3el.value === null) {
                ct.disabled = false;
                return false;
            }
            if (value1el.value === "") {
                ct.disabled = false;
                value1el.tippy.show();
                return false;
            }
            if (value2el.value === "") {
                ct.disabled = false;
                return false;
            }
            if (value3el.value === "") {
                ct.disabled = false;
                return false;
            }
            if (value1el.minLength !== null) {
                if (value1el.value.length < value1el.minLength) {
                    ct.disabled = false;
                    value1el.tippy.show();
                    return false;
                }
            }
            if (resultel === null) {
                ct.disabled = false;
                return false;
            }
            if (!Utils.validateEmail(value2el.value)) {
                ct.disabled = false;
                value2el.tippy.show();
                return false;
            }
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) {
                ct.disabled = false;
                return false;
            }
            let formdata = new FormData();
            formdata.append("method", ct.dataset.method);
            formdata.append("token", ct.dataset.token);
            formdata.append("verification", value1el.value);
            formdata.append("sendMailVerifyCodeToken", value3el.value);
            formdata.append("email", value2el.value);
            fetch("/profile", {
                method: 'post', body: formdata, headers: {
                    'X-CSRF-TOKEN': csrf.value
                },
            }).then(async (res) => {
                //console.log(res);
                let json = await res.json();
                console.log(json);
                resultel.innerText = json.message;
                if (res.status === 200) {
                    setTimeout(async () => {
                        await targetel.hidePopover();
                        await location.reload();
                    }, 3000)
                }
                ct.disabled = false;
            });
        };
    }
}

/**
 * 發送郵件驗證碼以進行密碼驗證。
 * @param {HTMLElement} ct - 點擊事件觸發的元素。
 * @param {string} target - 要顯示回應訊息的目標元素選擇器。
 * @return {boolean} 成功發送請求返回 true，否則返回 false。
 */
function sendMailVerifyCode_password(ct, target) {
    ct.onclick = () => {
        let targetel = document.querySelector(target);
        if (targetel === null) return false;
        let csrf = document.querySelector("#csrf_token");
        if (csrf === null) return false;
        let formdata = new FormData();
        formdata.append('token', ct.dataset.token);
        fetch('/profile/password/sendMailVerifyCode', {
            method: "post", headers: {
                'X-CSRF-TOKEN': csrf.value
            }, body: formdata,
        }).then(async (res) => {
            console.log(res);
            let json = await res.json();
            console.log(json);
            console.log(1);
            targetel.innerText = json.message;
            ct.dataset.token = json.token;
        });
    };
}

/**
 * 驗證用戶輸入的密碼驗證碼，通過檢查特定條件並提交請求到服務器，以確保驗證碼的有效性。
 *
 * @param {HTMLElement} ct - 觸發驗證過程的按鈕元素。
 * @param {string} target - 含有驗證碼的目標輸入元素的選擇器。
 *
 * @return {boolean} 成功提交請求並通過服務器驗證返回 true，否则返回 false。
 */
function verifyCode_password(ct, target) {
    if (ct.dataset.action1 !== null && ct.dataset.action2 !== null && ct.dataset.action4 !== null && ct.dataset.action5 !== null && ct.dataset.action3 !== null && ct.dataset.token !== null && ct.dataset.save !== null) {
        ct.onclick = () => {
            let save = document.querySelector(ct.dataset.save);
            let action1el = document.querySelector(ct.dataset.action1);
            let action2el = document.querySelector(ct.dataset.action2);
            let action3el = document.querySelector(ct.dataset.action3);
            let action4el = document.querySelector(ct.dataset.action4);
            let targetel = document.querySelector(target);
            if (action1el === null) return false;
            if (action2el === null) return false;
            if (action3el === null) return false;
            if (action4el === null) return false;
            if (save === null) return false;
            if (targetel.value === null) return false;
            if (targetel.value === "") return false;
            if (targetel.minLength !== null) {
                if (targetel.value.length < targetel.minLength) {
                    return false;
                }
            }
            if (ct.dataset.token === null) return false;
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) return false;
            let formdata = new FormData();
            formdata.append("code", targetel.value);
            formdata.append('token', ct.dataset.token);
            fetch('/profile/password/verifyCode', {
                method: "post", body: formdata, headers: {
                    'X-CSRF-TOKEN': csrf.value
                },
            }).then(async (res) => {
                console.log(res);
                let json = await res.json();
                console.log(json);
                if (res.status === 200) {
                    if (json.access_token !== "") {
                        let htmlInputElement = document.createElement("input");
                        htmlInputElement.value = json.access_token;
                        htmlInputElement.name = "profile_password_sendMailVerifyCodeToken";
                        htmlInputElement.id = "profile_password_sendMailVerifyCodeToken";
                        htmlInputElement.type = "hidden";
                        save.innerHTML = "";
                        save.append(htmlInputElement);
                        action1el.disabled = false;
                        action2el.disabled = false;
                        action3el.disabled = false;
                        action4el.remove();
                        ct.dataset.token = json.token;
                    }
                }
            })
                .catch(console.log);
        };
    }
}

/**
 * 更新使用者的密碼欄位。
 *
 * @param {HTMLElement} ct 包含密碼資料和事件的觸發元素。
 * @param {string} target 指定的目標元素選擇器，用來獲取驗證碼。
 * @return {boolean} 成功與否的布林值，若需表單資料不完整返回 false。
 */
function profileUpdatePassword(ct, target) {
    if (ct.dataset.token !== null && ct.dataset.method !== null) {
        ct.onclick = () => {
            if (ct.dataset.data1 === null) return false;
            if (ct.dataset.data2 === null) return false;
            if (ct.dataset.data3 === null) return false;
            let value1el = document.querySelector(ct.dataset.data1);
            let value2el = document.querySelector(ct.dataset.data2);
            let value3el = document.querySelector(ct.dataset.data3);
            let popoverel = document.querySelector(ct.dataset.popover);
            let result = document.querySelector(ct.dataset.result);
            let targetel = document.querySelector(target);
            if (value1el.value === null) return false;
            if (value2el.value === null) return false;
            if (value3el.value === null) return false;
            if (value1el.value === "") return false;
            if (value2el.value === "") return false;
            if (value3el.value === "") return false;
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) return false;
            let formdata = new FormData();
            formdata.append("method", ct.dataset.method);
            formdata.append("token", ct.dataset.token);
            formdata.append("sendMailVerifyCodeToken", targetel.value);
            formdata.append("current-ps", Utils.encodeContext(value1el.value).compress);
            formdata.append("password", Utils.encodeContext(value2el.value).compress);
            formdata.append("password_confirmation", Utils.encodeContext(value3el.value).compress);
            fetch("/profile", {
                method: 'post', body: formdata, headers: {
                    'X-CSRF-TOKEN': csrf.value
                },
            }).then(async (res) => {
                //console.log(res);
                let json = await res.json();
                console.log(json);
                let str = "";
                if (json.hasOwnProperty("errors")) {
                    console.log(json.errors);
                    if (typeof json.errors === "object") {
                        str = json.errors.join("\n<br>");
                    }
                }
                if(json.message === undefined) json.message = "";
                result.innerText = json.message + str;
                if (res.status === 200) {
                    setTimeout(async () => {
                        await popoverel.hidePopover();
                        await location.reload();
                    }, 3000)
                }
            });
        };
    }
}

/**
 * 用於綁定點擊事件並發送廣播請求的方法
 *
 * @param {HTMLElement} ct - 包含點擊事件和 CSRF token 的元素
 * @param {HTMLElement} target - 目標元素，目前未使用
 * @return {void}
 */
function test_broadcast(ct, target) {
    ct.onclick = () => {
        let formdata = new FormData();
        formdata.append("description", "test btn send");
        formdata.append("title", "test btn send");
        formdata.append("type", "info");
        formdata.append("second", "10000");
        fetch("broadcast", {
            method: 'post', body: formdata, headers: {
                'X-CSRF-TOKEN': ct.dataset.token
            },
        }).then(async (res) => {
            console.log(res);
        });
    };
}

/**
 * 根據參數切換元素的顯示狀態。本方法可設置雙向切換和單次切換兩種模式。
 *
 * @param {HTMLElement} ct - 觸發切換操作的元素
 * @param {string} target - 目標切換的元素選擇器
 * @return {boolean} 返回false表示操作中止
 */
function toggleable(ct, target) {
    ct.addEventListener('click', function () {
        let mode = ct.dataset.mode;
        if (mode === undefined) return false;
        if (mode === "twin") {
            let statusOn = ct.dataset.statuson;
            let statusOff = ct.dataset.statusoff;
            let onEl = document.querySelector(statusOn);
            let offEl = document.querySelector(statusOff);
            let targetEl = null;
            if (target !== "") {
                targetEl = document.querySelector(target);
            }
            if (ct.dataset.status === undefined) {
                ct.dataset.status = "off";
            }
            if (ct.dataset.status === "on") {
                ct.dataset.status = "off";
                onEl.classList.remove("hidden");
                offEl.classList.add("hidden");
                if (targetEl !== null) {
                    targetEl.dataset.status = "off";
                }
            } else {
                ct.dataset.status = "on";
                offEl.classList.remove("hidden");
                onEl.classList.add("hidden");
                if (targetEl !== null) {
                    targetEl.dataset.status = "on";
                }
            }
        } else if (mode === "once") {
            let targetEl = document.querySelector(target);
            let status = targetEl.dataset.status;
            let animation = ct.dataset.animation;
            let whenscreenless = ct.dataset.whenscreenless;
            let whenscrolltopmore = ct.dataset.whenscrolltopmore;
            let lockbody = ct.dataset.lockbody;
            if (lockbody === undefined) lockbody = "false";
            if (whenscreenless === undefined) whenscreenless = Number.MAX_SAFE_INTEGER;
            if (whenscreenless === "-1" || whenscreenless === -1) whenscreenless = Number.MAX_SAFE_INTEGER;
            if (whenscrolltopmore === "-1" || whenscrolltopmore === -1) whenscrolltopmore = Number.MAX_SAFE_INTEGER;
            if (animation !== null && animation === "true" && targetEl.classList.contains("animationing")) {
                console.log("animationing");
                return false;
            }
            console.log(window.scrollY <= whenscrolltopmore);
            console.log(window.scrollY, whenscrolltopmore);
            if (status === undefined) return false;
            if (status === "off") {
                targetEl.dataset.status = "on";
                if (animation === undefined && window.scrollY <= whenscrolltopmore) {
                    if (lockbody === "true") {
                        document.body.style.overflow = "hidden";
                    }
                    targetEl.classList.remove("hidden", "!hidden");
                } else if (animation !== undefined && window.innerWidth < whenscreenless) {
                    let animationStat1 = ct.dataset.animationstat1;
                    let animationStat1_1 = ct.dataset["animationstat1-1"];
                    let animationStat2 = ct.dataset.animationstat2;
                    let animationStat2_1 = ct.dataset["animationstat2-1"];
                    let animationDuration = ct.dataset.animationduration;
                    if (animation === "true") {
                        targetEl.classList.remove("hidden", "!hidden");
                        targetEl.classList.remove(animationStat1_1);
                        targetEl.classList.remove(animationStat2_1);
                        targetEl.classList.add(animationStat2);
                        targetEl.classList.add("animationing");
                        setTimeout(() => {
                            targetEl.classList.remove(animationStat2);
                        }, parseInt(animationDuration) / 2)
                        setTimeout(() => {
                            targetEl.classList.add(animationStat1_1);
                            targetEl.classList.remove("animationing");
                            if (lockbody === "true") document.body.style.overflow = "hidden";
                        }, parseInt(animationDuration) / 2)
                    }
                } else {
                    if (lockbody === "true") {
                        document.body.style.overflow = "hidden";
                    }
                    targetEl.classList.remove("hidden", "!hidden");
                }
            } else {
                targetEl.dataset.status = "off";
                if (animation === undefined && window.scrollY <= whenscrolltopmore) {
                    if (lockbody === "true") document.body.style.overflow = "";
                    targetEl.classList.add("hidden", "!hidden");
                } else if (animation !== undefined && window.innerWidth < whenscreenless) {
                    let animationStat1 = ct.dataset.animationstat1;
                    let animationStat1_1 = ct.dataset["animationstat1-1"];
                    let animationStat2 = ct.dataset.animationstat2;
                    let animationStat2_1 = ct.dataset["animationstat2-1"];
                    let animationDuration = ct.dataset.animationduration;
                    if (animation === "true") {
                        targetEl.classList.remove(animationStat1_1);
                        targetEl.classList.remove(animationStat2_1);
                        targetEl.classList.add(animationStat1);
                        targetEl.classList.add("animationing");
                        setTimeout(() => {
                            targetEl.classList.remove(animationStat1);
                        }, parseInt(animationDuration) / 2)
                        setTimeout(() => {
                            targetEl.classList.add(animationStat2_1);
                            targetEl.classList.remove("animationing");
                            targetEl.classList.add("hidden", "!hidden");
                            if (lockbody === "true") document.body.style.overflow = "";
                        }, parseInt(animationDuration) / 2)
                    }
                } else {
                    if (lockbody === "true") document.body.style.overflow = "";
                    targetEl.classList.add("hidden", "!hidden");
                }
            }
        }
    });
}

/**
 * 彈出視窗方法，用於處理點擊事件並顯示或隱藏彈出視窗。
 *
 * @param {Element} ct - 觸發彈出視窗的元素。
 * @param {string} target - 彈出視窗目標元素的選擇器。
 * @return {boolean|void} 如果 target 為 null，則返回 false；否則沒有返回值。
 */
function popover(ct, target) {
    ct.addEventListener("click", function () {
        if (target === null) return false;
        let targetEl = document.querySelector(target);
        if (targetEl === null) return;
        let source = JSON.parse(ct.dataset.source);
        let children = targetEl.children;
        let item = null;
        let dialog_vt = targetEl.querySelector(".dialog-vt");
        if (dialog_vt !== null) {
            let shop_iframe = dialog_vt.querySelector(".shop-iframe");
            let shop_popover_loader = dialog_vt.querySelector(".shop-popover-placeholder");
            if (shop_iframe !== null && shop_popover_loader !== null) {
                shop_iframe.onload = () => {
                    shop_popover_loader.classList.add('hidden');
                };
                shop_popover_loader.classList.remove("hidden");
                shop_iframe.src = "/shop/item/" + source.id + "/popover?asset=true";
                targetEl.classList.remove("!hidden");
                document.body.style.overflow = "hidden";
            }
            let dialog_closebtn = dialog_vt.querySelector(".dialog-closebtn");
            if (dialog_closebtn !== null && shop_iframe !== null && shop_popover_loader !== null) {
                dialog_closebtn.onclick = () => {
                    document.body.style.overflow = "";
                    targetEl.classList.add("!hidden");
                    shop_popover_loader.classList.remove("hidden");
                    shop_iframe.contentWindow.document.write("");
                    shop_iframe.contentWindow.document.close();
                };
            }
        }
    });
}

/**
 * 綁定一個元素的點擊事件，點擊後展示目標元素並載入自定義頁面。
 *
 * @param {HTMLElement} ct 綁定點擊事件的元素
 * @param {string} target 目標元素的選擇器
 * @return {boolean|void} 點擊時目標元素為 null 返回 false，否則無返回值
 */
function popover2(ct, target) {
    ct.addEventListener("click", function () {
        if (target === null) return false;
        let targetEl = document.querySelector(target);
        if (targetEl === null) return;
        let source = ct.dataset.source;
        let children = targetEl.children;
        let item = null;
        let dialog_vt = targetEl.querySelector(".dialog-vt");
        if (dialog_vt !== null) {
            let shop_iframe = dialog_vt.querySelector(".custom-page-iframe");
            let shop_popover_loader = dialog_vt.querySelector(".shop-popover-placeholder");
            if (shop_iframe !== null && shop_popover_loader !== null) {
                shop_iframe.onload = () => {
                    shop_popover_loader.classList.add('hidden');
                };
                shop_popover_loader.classList.remove("hidden");
                shop_iframe.src = "/custom/page/" + source + '?popup=1';
                targetEl.classList.remove("!hidden");
                document.body.style.overflow = "hidden";
            }
            let dialog_closebtn = dialog_vt.querySelector(".dialog-closebtn");
            if (dialog_closebtn !== null && shop_iframe !== null && shop_popover_loader !== null) {
                dialog_closebtn.onclick = () => {
                    document.body.style.overflow = "";
                    targetEl.classList.add("!hidden");
                    shop_popover_loader.classList.remove("hidden");
                    shop_iframe.contentWindow.document.write("");
                    shop_iframe.contentWindow.document.close();
                };
            }
        }
    });
}

/**
 * 綁定一個元素的點擊事件，點擊後展示目標元素並載入自定義頁面。
 *
 * @param {HTMLElement} ct 綁定點擊事件的元素
 * @param {string} target 目標元素的選擇器
 * @return {boolean|void} 點擊時目標元素為 null 返回 false，否則無返回值
 */
function popover3(ct, target) {
    ct.addEventListener("click", function () {
        if (target === null) return false;
        let targetEl = document.querySelector(target);
        if (targetEl === null) return;
        let source = ct.dataset.source;
        let children = targetEl.children;
        let item = null;
        let dialog_vt = targetEl.querySelector(".dialog-vt");
        if (dialog_vt !== null) {
            let dialog_title = dialog_vt.querySelector(".dialog-title > .dialog-title-field");
            if(dialog_title !== null){
                dialog_title.innerText = "預覽分享資訊";
            }
            let shop_iframe = dialog_vt.querySelector(".custom-page-iframe");
            let shop_popover_loader = dialog_vt.querySelector(".shop-popover-placeholder");
            if (shop_iframe !== null && shop_popover_loader !== null) {
                shop_iframe.onload = () => {
                    shop_popover_loader.classList.add('hidden');
                };
                shop_popover_loader.classList.remove("hidden");
                shop_iframe.src = "/sharetable/item/" + source + '?popup=1';
                targetEl.classList.remove("!hidden");
                document.body.style.overflow = "hidden";
            }
            let dialog_closebtn = dialog_vt.querySelector(".dialog-closebtn");
            if (dialog_closebtn !== null && shop_iframe !== null && shop_popover_loader !== null) {
                let closefn = () => {
                    document.body.style.overflow = "";
                    targetEl.classList.add("!hidden");
                    shop_popover_loader.classList.remove("hidden");
                    shop_iframe.contentWindow.document.write("");
                    shop_iframe.contentWindow.document.close();
                };
                dialog_closebtn.onclick = closefn;
                window.addEventListener('message', function(event) {
                    if (event.data === 'close') {
                        closefn();
                    } else if (event.data === 'open') {
                        document.dispatchEvent(new CustomEvent('CGPOPOVER::init'));
                    }
                });
            }
        }
    });
}

/**
 * 綁定一個元素的點擊事件，點擊後展示目標元素並載入自定義頁面。
 *
 * @param {HTMLElement} ct 綁定點擊事件的元素
 * @param {string} target 目標元素的選擇器
 * @return {boolean|void} 點擊時目標元素為 null 返回 false，否則無返回值
 */
function popover4(ct, target) {
    let afn = function () {
        if (target === null) return false;
        let targetEl = document.querySelector(target);
        if (targetEl === null) return;
        let source = ct.dataset.source;
        let children = targetEl.children;
        let item = null;
        let dialog_vt = targetEl.querySelector(".dialog-vt");
        if (dialog_vt !== null) {
            let dialog_title = dialog_vt.querySelector(".dialog-title > .dialog-title-field");
            if(dialog_title !== null){
                dialog_title.innerText = "編輯分享資訊";
            }
            let shop_iframe = dialog_vt.querySelector(".custom-page-iframe");
            let shop_popover_loader = dialog_vt.querySelector(".shop-popover-placeholder");
            if (shop_iframe !== null && shop_popover_loader !== null) {
                shop_iframe.onload = () => {
                    shop_popover_loader.classList.add('hidden');
                };
                shop_popover_loader.classList.remove("hidden");
                shop_iframe.src = "/sharetable/item/edit/" + source + '?popup=1';
                targetEl.classList.remove("!hidden");
                document.body.style.overflow = "hidden";
            }
            let dialog_closebtn = dialog_vt.querySelector(".dialog-closebtn");
            if (dialog_closebtn !== null && shop_iframe !== null && shop_popover_loader !== null) {
                let closefn = () => {
                    document.body.style.overflow = "";
                    targetEl.classList.add("!hidden");
                    shop_popover_loader.classList.remove("hidden");
                    shop_iframe.contentWindow.document.write("");
                    shop_iframe.contentWindow.document.close();
                };
                dialog_closebtn.onclick = closefn;
                window.addEventListener('message', function(event) {
                    if (event.data === 'close') {
                        document.dispatchEvent(new CustomEvent('CGPOPOVER::close'));
                        document.dispatchEvent(new CustomEvent('CGPFC::update', { detail: {
                            source: source,
                        }}));
                        closefn();
                    } else if (event.data === 'open') {
                        document.dispatchEvent(new CustomEvent('CGPOPOVER::init'));
                    }
                });
            }
        }
    };
    ct.addEventListener("CGPOPOVER::init", afn);
    ct.addEventListener("click", afn);
}

/**
 * 綁定一個元素的點擊事件，點擊後下載
 *
 * @param {HTMLElement} ct 綁定點擊事件的元素
 * @param {string} target 目標元素的選擇器
 * @return {boolean|void} 點擊時目標元素為 null 返回 false，否則無返回值
 */
function download_toast(ct, target) {
    ct.addEventListener("click", () => {
        const filename = ct.dataset.filename;
        document.dispatchEvent(new CustomEvent("CGTOASTIFY::notice", { detail: { message: "已請求下載 "+filename+" 資源", avatar: "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNy4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjUgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTI4OCAzMmMwLTE3LjctMTQuMy0zMi0zMi0zMnMtMzIgMTQuMy0zMiAzMmwwIDI0Mi43LTczLjQtNzMuNGMtMTIuNS0xMi41LTMyLjgtMTIuNS00NS4zIDBzLTEyLjUgMzIuOCAwIDQ1LjNsMTI4IDEyOGMxMi41IDEyLjUgMzIuOCAxMi41IDQ1LjMgMGwxMjgtMTI4YzEyLjUtMTIuNSAxMi41LTMyLjggMC00NS4zcy0zMi44LTEyLjUtNDUuMyAwTDI4OCAyNzQuNyAyODggMzJ6TTY0IDM1MmMtMzUuMyAwLTY0IDI4LjctNjQgNjRsMCAzMmMwIDM1LjMgMjguNyA2NCA2NCA2NGwzODQgMGMzNS4zIDAgNjQtMjguNyA2NC02NGwwLTMyYzAtMzUuMy0yOC43LTY0LTY0LTY0bC0xMDEuNSAwLTQ1LjMgNDUuM2MtMjUgMjUtNjUuNSAyNS05MC41IDBMMTY1LjUgMzUyIDY0IDM1MnptMzY4IDU2YTI0IDI0IDAgMSAxIDAgNDggMjQgMjQgMCAxIDEgMC00OHoiLz48L3N2Zz4=" } }));
    });
}

/**
 * 根據.ct元素的data屬性執行相應的函數。每個.ct元素必須具備data-fn與data-target屬性，
 * 函數會依據data-fn屬性的值來決定呼叫哪一個功能，並將data-target屬性的值傳給該功能。
 * 支援的功能包括：toggleable、password-toggle、datalist_selector、profile.email.sendMailVerifyCode、
 * profile.email.verifyCode、profile.email.newMailVerifyCode、profile.password.sendMailVerifyCode、
 * profile.password.verifyCode、profileUpdateEmail、profileUpdatePassword、test-broadcast、
 * popover、popover2。
 *
 * @return {void} 無返回值
 */
function customTrigger() {
    var cts = document.querySelectorAll('.ct:not(.ct-rendered)');
    for (let ct of cts) {
        //console.log(ct)
        if (ct.dataset.fn !== null && ct.dataset.target !== null) {
            let target = ct.dataset.target;
            //console.log(target)
            ct.classList.add("ct-rendered")
            switch (ct.dataset.fn) {
                case 'toggleable':
                    toggleable(ct, target);
                    break;
                case 'password-toggle':
                    password_toggle(ct, target);
                    break;
                case "datalist_selector":
                    datalist_selector(ct, target);
                    break;
                case "profile.email.sendMailVerifyCode":
                    sendMailVerifyCode_profile_email(ct, target);
                    break;
                case "profile.email.verifyCode":
                    verifyCode_profile_email(ct, target);
                    break;
                case "profile.email.newMailVerifyCode":
                    newMailVerifyCode_profile_email(ct, target);
                    break;
                case "profile.password.sendMailVerifyCode":
                    sendMailVerifyCode_password(ct, target);
                    break;
                case "profile.password.verifyCode":
                    verifyCode_password(ct, target);
                    break;
                case "profileUpdateEmail":
                    profileUpdateEmail(ct, target);
                    break;
                case "profileUpdatePassword":
                    profileUpdatePassword(ct, target);
                    break;
                case "test-broadcast":
                    test_broadcast(ct, target);
                    break;
                case "popover":
                    popover(ct, target);
                    break;
                case "popover2":
                    popover2(ct, target);
                    break;
                case "popover3":
                    popover3(ct, target);
                    break;
                case "popover4":
                    popover4(ct, target);
                    break;
                case "download-toast":
                    download_toast(ct, target);
                    break;
                default:
                    ct.classList.remove("ct-rendered");
                    break;
            }
        }
    }
}

document.addEventListener("CGCT::init", customTrigger);
document.addEventListener('DOMContentLoaded', customTrigger);
