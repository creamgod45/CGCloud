/**
 * 當頁面滾動時處理滾動指示器的方法。
 *
 * @param {HTMLElement} self - 包含進度條的元素。
 * @param {HTMLElement} target - 被滾動的目標元素。
 * @return {void} 無返回值
 */
function handlerScrollIndicator(self, target) {
    console.log(self, target);
    window.addEventListener('scroll', function(event) {
        let maxScroll = target.scrollHeight - target.clientHeight; // 總的可滾動距離
        if(self.querySelector('progress') === null) {
            throw new Error("scroll-indicator element must have a progress element");
            return;
        }
        self.querySelector('progress').value = ((target.scrollTop) / maxScroll) * 100; // 將進度條值設置為百分比
    })
}

/**
 * 根據指定目標元素的捲動位置，控制指定的滾動指示器的變化。
 * 該方法會遍歷所有具有.scroll-indicator類的元素，根據它們的data-target屬性，
 * 找到目標元素，並為其綁定滾動處理器。
 *
 * @return {void}
 */
function scroll_indicator() {
    let scroll_inicator = document.querySelectorAll(".scroll-indicator");
    for (let scrollInicatorElement of scroll_inicator) {
        let target = scrollInicatorElement.dataset.target;
        console.log(scrollInicatorElement.dataset);
        if(target === undefined) continue;
        let Element = document.querySelector(target);
        console.log(Element);
        if(Element !== null){
            handlerScrollIndicator(scrollInicatorElement, Element);
        }
    }
}

document.addEventListener('DOMContentLoaded', scroll_indicator);
