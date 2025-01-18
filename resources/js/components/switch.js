import * as Utils from './utils.js';

/**
 * 初始化並設置所有具有 `switch` 類別的元素。
 *
 * 這個方法會遍歷文件中所有具有 `switch` 類別的元素，並為它們分配唯一的 ID，
 * 註冊相應的事件（如 `click`、`mouseover`、`mousedown` 和 `mouseup` 等），
 * 並依據元素的數據屬性設置其值和狀態。
 *
 * @return 無返回值
 */
function switcher() {
    let switches = document.querySelectorAll(".switch");
    for (let switch1 of switches) {
        if (switch1.id === null || switch1.id === undefined || switch1.id === "")
            switch1.id = "switch_" + Utils.generateRandomString(5);
        switch1.events = [];
        switch1.registerEvent = (method) => {
            switch1.events[switch1.events.length] = method;
        };
        switch1.isRegistered = (method) => {
            let filter = switch1.events.find(m => m === method);
            return filter !== undefined;
        };
        if (switch1.dataset.onclick === "true") {
            switch1.registerEvent('click');
        }
        if (switch1.dataset.onmouseover === "true") {
            switch1.registerEvent('mouseover');
        }
        if (switch1.dataset.onmousedown === "true") {
            switch1.registerEvent('mousedown');
        }
        if (switch1.dataset.onmouseup === "true") {
            switch1.registerEvent('mouseup');
        }
        let name = switch1.dataset.name;
        let input = document.createElement("input");
        if (name !== undefined) {
            input.type = 'hidden';
            input.name = name;
            switch1.append(input);
        }

        if (switch1.dataset.value !== undefined) {
            switch1.value = (switch1.dataset.value === "true" || switch1.dataset.value === "1");
            if (!switch1.value)
                switch1.classList.remove("active");
            else
                switch1.classList.add("active");
            switch1.dataset.value = switch1.value;
            if (name !== undefined) {
                input.value = switch1.value;
            }
        }
        switch1.on = (method, callback) => {
            if (switch1.isRegistered(method)) {
                document.addEventListener('CGSW::' + method, function (e) {
                    if (e.detail.html.isRegistered(method) && switch1 === e.detail.html) {
                        callback(e)
                    }
                });
            }
        };

        switch1.onclick = function () {
            let id = switch1.id;
            if (switch1.value === null) {
                switch1.classList.add("active");
                switch1.value = true
                if (name !== undefined) {
                    input.value = "true";
                }
            } else {
                if (switch1.value)
                    switch1.classList.remove("active");
                else
                    switch1.classList.add("active");
                switch1.value = !switch1.value;
                switch1.dataset.value = switch1.value;
                if (name !== undefined) {
                    input.value = switch1.value;
                }
            }
            document.dispatchEvent(new CustomEvent('CGSW::click', {
                cancelable: false,
                detail: {
                    id: id,
                    html: switch1,
                    value: switch1.value,
                }
            }));
        };
        switch1.onmouseover = function () {
            let id = switch1.id;
            document.dispatchEvent(new CustomEvent('CGSW::mouseover', {
                cancelable: false,
                detail: {
                    id: id,
                    html: switch1,
                }
            }));
        };
        switch1.onmousedown = function () {
            let id = switch1.id;
            document.dispatchEvent(new CustomEvent('CGSW::mousedown', {
                cancelable: false,
                detail: {
                    id: id,
                    html: switch1,
                }
            }));
        };
        switch1.onmouseup = function () {
            let id = switch1.id;
            document.dispatchEvent(new CustomEvent('CGSW::mouseup', {
                cancelable: false,
                detail: {
                    id: id,
                    html: switch1,
                }
            }));
        };
    }
}
document.addEventListener('CGSW::init', function () {
   switcher();
});
document.addEventListener('DOMContentLoaded', function () {
    switcher();
});
