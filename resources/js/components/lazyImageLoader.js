/**
 * 檢查背景圖片是否加載成功。
 *
 * @param {string} url 圖片的URL地址。
 * @param {function} callback 回調函數，接受一個布爾值參數，表示圖片是否加載成功。
 * @return {void}
 */
function checkBackgroundImageLoad(url, callback) {
    const img = new Image();
    img.src = url;
    img.onload = function () {
        callback(true);
    };
    img.onerror = function () {
        callback(false);
    };
}

/**
 * init 變數使用 CustomEvent 類別實例化，用於初始化所有組件。
 *
 * CustomEvent 實例化時，事件名稱為'Placeholderinit'，並設置初始化訊息。
 *
 * @type {CustomEvent}
 * @property {Object} detail 事件詳細資料
 * @property {string} detail.message 初始化所有組件的訊息
 * @property {boolean} cancelable 事件是否可取消，設置為 false
 */
const init = new CustomEvent('Placeholderinit', {
    detail: {
        message: "init all components"
    },
    cancelable: false
});

/**
 * lazyImageLoader 函數會給網站中的懶加載圖片添加觀察者，
 * 當這些圖片進入瀏覽器的可視範圍時，會將佔位圖片替換為實際圖片，並移除一些佔位樣式類。
 *
 * @return {void} 此函數不返回值。它通過改變圖片元素的背景圖片屬性來實現圖片的懶加載。
 */
function lazyImageLoader() {
    /** First we get all the non-loaded image elements **/
    let lazyImages = document.querySelectorAll(".lazy-loaded-image");
    let lazyImg = document.querySelectorAll(".lazyImg");
    /** Then we set up a intersection observer watching over those images and whenever any of those becomes visible on the view then replace the placeholder image with actual one, remove the non-loaded class and then unobserve for that element **/
    let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                let lazyImage = entry.target;
                let bgImageUrl = window.getComputedStyle(lazyImage).backgroundImage.slice(5, -2); // 獲取背景圖片的URL
                //console.log(entry);
                //console.log(lazyImage);
                if (!lazyImage.classList.contains("placeholdered")) {
                    lazyImage.style.backgroundImage = `url(${lazyImage.dataset.src})`;
                    checkBackgroundImageLoad(bgImageUrl, function (isLoaded) {
                        if (isLoaded) {
                            console.log('Background image has loaded.');
                            setTimeout(() => {
                                lazyImage.classList.remove('placeholder');
                                lazyImage.classList.remove('placeholder-circle');
                                lazyImage.classList.remove('placeholder-16-9');
                                lazyImage.classList.add("placeholdered");
                                document.dispatchEvent(init);
                            }, Number.parseInt(lazyImage.dataset.placeholderdelay));
                            // 在這裡您可以添加圖片加載完成後的處理邏輯
                        }
                    });
                }
            }
        });
    });

    for (let lazyImage of lazyImages) {
        lazyImageObserver.observe(lazyImage);
    }

    for (let lazyImgElement of lazyImg) {
        lazyImageObserver.observe(lazyImgElement);
    }
}

setInterval(() => {
    console.log("queue");
    let placeholders = document.querySelectorAll(".placeholder");
    for (let placeholder of placeholders) {
        if (placeholder.classList.contains("placeholder")) {
            let bgImageUrl = window.getComputedStyle(placeholder).backgroundImage.slice(5, -2); // 獲取背景圖片的URL
            checkBackgroundImageLoad(bgImageUrl, function (isLoaded) {
                if (isLoaded) {
                    console.log('Background image has loaded.');
                    setTimeout(() => {
                        placeholder.classList.remove('placeholder');
                        placeholder.classList.remove('placeholder-circle');
                        placeholder.classList.remove('placeholder-16-9');
                        placeholder.classList.add("placeholdered");
                        document.dispatchEvent(init);
                    }, Number.parseInt(placeholder.dataset.placeholderdelay));
                    // 在這裡您可以添加圖片加載完成後的處理邏輯
                }
            });
        }
    }
}, 1000);

document.addEventListener("LazyImageLoad", lazyImageLoader);

document.addEventListener('DOMContentLoaded', () => {
    lazyImageLoader();
});
