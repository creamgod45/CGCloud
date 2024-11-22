import * as Utils from './components/utils.js'
import axios from 'axios'

function login() {
    let loginEl = document.querySelector(".login");
    if (loginEl !== null && loginEl.dataset.target !== null) {
        let children = loginEl.children;
        console.log(children);
        let object = {
            account: children[2].children[1],
            accountTooltip: children[2],
            password: children[3].children[1].children[0].children[0],
            passwordTooltip: children[3],
            submit: children[6].children[0],
            alert: document.querySelector(loginEl.dataset.target),
        };
        console.log(object);
        object.submit.disabled = false;
        object.submit.onclick = () => {
            object.submit.disabled = true;
            let error = 0;
            if (object.accountTooltip.isTooltopShow) {
                object.accountTooltip.tooltipHide();
            }
            if ((object.account.value === null || object.account.value === "") && !object.accountTooltip.isTooltopShow) {
                object.accountTooltip.tooltipShow();
                error++;
            } else if (object.account.value.length > object.account.maxLength + 1 && !object.accountTooltip.isTooltopShow) {
                object.accountTooltip.tooltipShow();
                error++;
            }
            if (object.accountTooltip.isTooltopShow && error === 0) {
                object.accountTooltip.tooltipHide();
            }
            let error2 = 0;
            if (object.passwordTooltip.isTooltopShow) {
                object.passwordTooltip.tooltipHide();
            }
            if ((object.password.value === null || object.password.value === "") && !object.passwordTooltip.isTooltopShow) {
                object.passwordTooltip.tooltipShow();
                error2++;
            } else if (object.password.value.length < object.password.minLength && !object.passwordTooltip.isTooltopShow) {
                object.passwordTooltip.tooltipShow();
                error2++;
            }
            if (object.passwordTooltip.isTooltopShow && error2 === 0) {
                object.passwordTooltip.tooltipHide();
            }
            let error5 = 0;
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) error5++;
            if (loginEl.dataset.token === undefined) error5++;
            if (error > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error2 > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error5 > 0) {
                object.submit.disabled = false;
                return false;
            }
            let formdata = new FormData();
            formdata.append("token", loginEl.dataset.token);
            formdata.append("username", object.account.value);
            formdata.append("password", Utils.encodeContext(object.password.value).compress);

            axios.post("/login", formdata, {
                adapter: "fetch",
            }).then(async (res) => {
                /**
                 * @typedef {Object} login
                 * @property {boolean} type - Register result status
                 * @property {string} message - Register result message
                 * @property {string|null} redirect - Register need redirect page
                 * @property {array|null} error_keys - Register result validate failed keys
                 * @property {string} token - Register Post token
                 */
                /**
                 * @var {login} data test
                 */
                const data = res.data;
                object.alert.innerHTML = data.message;
                if (data.type) {
                    setTimeout(() => {
                        location.assign(data.redirect);
                    }, 5000);
                } else {
                    object.submit.disabled = false;
                    for (const errorKey of data.error_keys) {
                        switch (errorKey) {
                            case 'password':
                                object.passwordTooltip.tooltipShow();
                                break;
                            case 'username':
                                object.accountTooltip.tooltipShow();
                                break;
                        }
                    }
                    loginEl.dataset.token = data.token;
                }
            });
        };
    }
}

function register() {
    let registerEl = document.querySelector(".register");
    if (registerEl !== null && registerEl.dataset.target !== null) {
        let children = registerEl.children;
        //console.log(children);
        let object = {
            account: children[2].children[1],
            accountTooltip: children[2],
            email: children[3].children[1],
            emailTooltip: children[3],
            password: children[4].children[1].children[0].children[0],
            passwordTooltip: children[4],
            repassword: children[5].children[1].children[0].children[0],
            repasswordTooltip: children[5],
            phone: children[6].children[1].children[0].children[1],
            phoneTooltip: children[6],
            submit: children[8].children[0],
            alert: document.querySelector(registerEl.dataset.target),
        };
        object.submit.disabled = false;
        console.log(object);
        object.submit.onclick = () => {
            object.submit.disabled = true;
            object.account.onchange = () => {
                if (object.account.value === null || object.account.value === "") return false;
                if (object.account.value.length > object.account.maxLength + 1) return false;
                object.accountTooltip.tooltipHide();
            }
            object.email.onchange = () => {
                if (object.email.value === null || object.email.value === "") return false;
                if (!Utils.validateEmail(object.email.value)) return false;
                object.emailTooltip.tooltipHide();
            }
            object.password.onchange = () => {
                if (object.password.value === null || object.password.value === "") return false;
                if (object.password.value.length < object.password.minLength) return false;
                object.passwordTooltip.tooltipHide();
            }
            object.repassword.onchange = () => {
                if (object.repassword.value === null || object.repassword.value === "") return false;
                if (object.repassword.value.length < object.repassword.minLength) return false;
                if (object.password.value !== object.repassword.value) return false;
                object.repasswordTooltip.tooltipHide();
            }
            object.phone.onchange = () => {
                if (object.phone.value === null || object.phone.value === "") return false;
                if (object.phone.value.length < object.phone.minLength) return false;
                object.phoneTooltip.tooltipHide();
            }
            let error = 0;
            if (object.accountTooltip.isTooltopShow) {
                object.accountTooltip.tooltipHide();
            }
            if ((object.account.value === null || object.account.value === "") && !object.accountTooltip.isTooltopShow) {
                object.accountTooltip.tooltipShow();
                error++;
            } else if (object.account.value.length > object.account.maxLength + 1 && !object.accountTooltip.isTooltopShow) {
                object.accountTooltip.tooltipShow();
                error++;
            }
            if (object.accountTooltip.isTooltopShow && error === 0) {
                object.accountTooltip.tooltipHide();
            }
            let error1 = 0;
            if (object.emailTooltip.isTooltopShow) {
                object.emailTooltip.tooltipHide();
            }
            if ((object.email.value === null || object.email.value === "") && !object.emailTooltip.isTooltopShow) {
                object.emailTooltip.tooltipShow();
                error1++;
            } else if (object.email.value.length > object.email.maxLength + 1 && !object.emailTooltip.isTooltopShow) {
                object.emailTooltip.tooltipShow();
                error1++;
            } else if (!Utils.validateEmail(object.email.value) && !object.emailTooltip.isTooltopShow) {
                object.emailTooltip.tooltipShow();
                error1++;
            }
            if (object.emailTooltip.isTooltopShow && error1 === 0) {
                object.emailTooltip.tooltipHide();
            }
            let error2 = 0;
            if (object.passwordTooltip.isTooltopShow) {
                object.passwordTooltip.tooltipHide();
            }
            if ((object.password.value === null || object.password.value === "") && !object.passwordTooltip.isTooltopShow) {
                object.passwordTooltip.tooltipShow();
                error2++;
            } else if (object.password.value.length < object.password.minLength && !object.passwordTooltip.isTooltopShow) {
                object.passwordTooltip.tooltipShow();
                error2++;
            }
            if (object.passwordTooltip.isTooltopShow && error2 === 0) {
                object.passwordTooltip.tooltipHide();
            }
            let error3 = 0;
            if (object.repasswordTooltip.isTooltopShow) {
                object.repasswordTooltip.tooltipHide();
            }
            if ((object.repassword.value === null || object.repassword.value === "") && !object.repasswordTooltip.isTooltopShow) {
                object.repasswordTooltip.tooltipShow();
                error3++;
            } else if (object.repassword.value.length < object.repassword.minLength && !object.repasswordTooltip.isTooltopShow) {
                object.repasswordTooltip.tooltipShow();
                error3++;
            } else if (object.password.value !== object.repassword.value && !object.repasswordTooltip.isTooltopShow) {
                object.repasswordTooltip.tooltipShow();
                error3++;
            }
            if (object.repasswordTooltip.isTooltopShow && error3 === 0) {
                object.repasswordTooltip.tooltipHide();
            }
            let error4 = 0;
            if (object.phoneTooltip.isTooltopShow) {
                object.phoneTooltip.tooltipHide();
            }
            if ((object.phone.value === null || object.phone.value === "") && !object.phoneTooltip.isTooltopShow) {
                object.phoneTooltip.tooltipShow();
                error4++;
            } else if (object.phone.value.length < object.phone.minLength && !object.phoneTooltip.isTooltopShow) {
                object.phoneTooltip.tooltipShow();
                error4++;
            }
            if (object.phoneTooltip.isTooltopShow && error4 === 0) {
                object.phoneTooltip.tooltipHide();
            }
            let error5 = 0;
            let csrf = document.querySelector("#csrf_token");
            if (csrf === null) error5++;
            if (registerEl.dataset.token === undefined) error5++;
            if (error > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error1 > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error2 > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error3 > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error4 > 0) {
                object.submit.disabled = false;
                return false;
            }
            if (error5 > 0) {
                object.submit.disabled = false;
                return false;
            }
            let formdata = new FormData();
            formdata.append("token", registerEl.dataset.token);
            formdata.append("username", object.account.value);
            formdata.append("email", object.email.value);
            formdata.append("phone", object.phone.value);
            formdata.append("password", Utils.encodeContext(object.password.value).compress);
            formdata.append("password_confirmation", Utils.encodeContext(object.repassword.value).compress);

            axios.post("/register", formdata, {
                adapter: "fetch",
            }).then(async (res) => {
                /**
                 * @typedef {Object} Register
                 * @property {boolean} type - Register result status
                 * @property {string} message - Register result message
                 * @property {string|null} redirect - Register need redirect page
                 * @property {array|null} error_keys - Register result validate failed keys
                 * @property {string} token - Register Post token
                 */
                /**
                 * @var {Register} data test
                 */
                const data = res.data;
                object.alert.innerHTML = data.message;
                if (data.type) {
                    setTimeout(() => {
                        location.assign(data.redirect);
                    }, 5000);
                } else {
                    object.submit.disabled = false;
                    for (const errorKey of data.error_keys) {
                        switch (errorKey) {
                            case 'password':
                                object.passwordTooltip.tooltipShow();
                                break;
                            case 'password_confirmation':
                                object.repasswordTooltip.tooltipShow();
                                break;
                            case 'email':
                                object.emailTooltip.tooltipShow();
                                break;
                            case 'username':
                                object.accountTooltip.tooltipShow();
                                break;
                            case 'phone':
                                object.phoneTooltip.tooltipShow();
                                break;
                        }
                    }
                    registerEl.dataset.token = data.token;
                }
            });
            //fetch("/register", {
            //    method: 'post',
            //    body: formdata,
            //    headers: {
            //        'X-CSRF-TOKEN': csrf.value
            //    },
            //}).then(async (res) => {
            //    //console.log(res);
            //    let json = await res.json();
            //    console.log(json);
            //});
        };
    }
    return false;
}

function loader() {
    setTimeout(() => {
        register();
        login();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', loader);
