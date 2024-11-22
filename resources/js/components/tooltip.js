import {generateRandomString, htmldecode} from "./utils.js";

/**
 * 為指定的元素顯示提示工具。
 *
 * @param {Event} event - 觸發提示工具的事件。
 * @return {void} 無返回值。
 */
function tooltip(event) {
    let tooltips = document.querySelectorAll(".tooltip-gen");

    function getOnmouseenter(direction, tooltip, tooltipEl) {
        return () => {
            if (tooltipEl.dataset.destory !== undefined) {
                let destoryEl = tooltipEl.dataset.destory;
                if (destoryEl !== "") {
                    document.querySelector(destoryEl).remove();
                    tooltipEl.dataset.destory = "";
                    tooltipEl.isTooltopShow = false;
                }
            }
            let t = document.createElement("div");
            tooltipEl.dataset.render = "true";
            t.classList.add("tooltip");
            if (tooltipEl.dataset.render === "true" && tooltipEl.dataset.mobileTriggerPx !== null && tooltipEl.dataset.mobile !== null) {
                if (window.screen.width <= Number.parseInt(tooltipEl.dataset.mobiletriggerpx)) {
                    //console.log(true)
                    direction = tooltipEl.dataset.mobile;
                    tooltipEl.dataset.mobilestatus = "true";
                } else {
                    direction = tooltipEl.dataset.direction;
                }
            }
            t.classList.add(direction);
            if (tooltipEl.dataset.customclass !== undefined) {
                //console.log(tooltipEl.dataset.customclass)
                let strings = tooltipEl.dataset.customclass.split(" ");
                if (strings.length > 1) {
                    for (let string of strings) {
                        t.classList.add(string);
                    }
                } else {
                    t.classList.add(tooltipEl.dataset.customclass);
                }
            }
            let s = generateRandomString(5);
            t.id = s;
            if (tooltipEl.dataset.htmlable !== undefined) {
                //console.log(tooltipEl.dataset.htmlable);
                if (tooltipEl.dataset.htmlable === "true") {
                    t.innerHTML = tooltip;
                } else {
                    t.innerHTML = htmldecode(tooltip);
                }
            } else {
                t.innerHTML = htmldecode(tooltip);
            }
            tooltipEl.dataset.destory = "#" + s;
            tooltipEl.appendChild(t);
            let tooltipBound = tooltipEl.getBoundingClientRect();
            //let tBound = t.getBoundingClientRect();
            if (direction === "tooltip-top") {
                t.style.bottom = tooltipBound.height + 20 + "px";
                t.style.right = "0";
                //tooltipBound = tooltipEl.getBoundingClientRect(); tBound = t.getBoundingClientRect();
                //t.style.right = (tBound.x-tooltipBound.x)+(tooltipBound.width)+'px';
                //console.log(`bottom: ${t.style.bottom} left: ${t.style.left}`);
            }
            if (direction === "tooltip-left") {
                t.style.right = tooltipBound.width + 20 + "px";
            }
            if (direction === "tooltip-right") {
                t.style.left = tooltipBound.width + 20 + "px";
            }

            if (direction === "tooltip-bottom") {
                t.style.top = tooltipBound.height + 18 + "px";
                t.style.left = "0";
            }
            tooltipEl.isTooltopShow = true;
        }
    }

    function getOnmouseleave(tooltipEl) {
        return () => {
            if (tooltipEl.dataset.destory !== undefined) {
                let destoryEl = tooltipEl.dataset.destory;
                if (destoryEl !== "") {
                    document.querySelector(destoryEl).remove();
                    tooltipEl.dataset.destory = "";
                    tooltipEl.isTooltopShow = false;
                }
            }
        };
    }

    for (let tooltipEl of tooltips) {
        let dataset = tooltipEl.dataset;
        if (dataset.tooltip === null) continue;
        if (dataset.direction === null) continue;
        let tooltip = dataset.tooltip;
        let direction = dataset.direction;
        if (dataset.render === "true" && dataset.mobileTriggerPx !== null && dataset.mobile !== null) {
            if (window.screen.width <= Number.parseInt(dataset.mobiletriggerpx)) {
                //console.log(true)
                direction = dataset.mobile;
                dataset.mobilestatus = "true";
            } else {
                direction = dataset.direction;
            }
            if (tooltipEl.dataset.destory !== undefined) {
                let destoryEl = tooltipEl.dataset.destory;
                if (destoryEl !== "") {
                    document.querySelector(destoryEl).remove();
                    tooltipEl.dataset.destory = "";
                    tooltipEl.isTooltopShow = false;
                    if (tooltipEl.isTooltopShow === false) {
                        tooltipEl.tooltipShow();
                    }
                }
            }
        }
        if (tooltipEl.dataset.hoverable !== undefined) {
            if (tooltipEl.dataset.hoverable === "false") {

            } else {
                tooltipEl.onmouseenter = getOnmouseenter(direction, tooltip, tooltipEl)
                tooltipEl.onmouseleave = getOnmouseleave(tooltipEl)
            }
        } else {
            tooltipEl.onmouseenter = getOnmouseenter(direction, tooltip, tooltipEl)
            tooltipEl.onmouseleave = getOnmouseleave(tooltipEl)
        }
        tooltipEl.isTooltopShow = false;
        tooltipEl.tooltipShow = getOnmouseenter(direction, tooltip, tooltipEl)
        tooltipEl.tooltipHide = getOnmouseleave(tooltipEl)
    }
}

window.addEventListener('resize', tooltip);
document.addEventListener('TooltipLoad', tooltip);
document.addEventListener('DOMContentLoaded', tooltip);
