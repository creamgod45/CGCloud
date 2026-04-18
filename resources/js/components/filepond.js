import { defineFilePond, createStoreExtension } from 'filepond';

// 導入語系與擴充功能
import { locale as zh_cn_base } from 'filepond/locales/zh-cn';
import { EntryListView } from 'filepond/extensions/entry-list-view';
import { FileSizeValidator } from 'filepond/extensions/file-size-validator';
import { FileMimeTypeValidator } from 'filepond/extensions/file-mime-type-validator';
import { ImageBitmapTransform } from 'filepond/extensions/image-bitmap-transform';
import { URLLoader } from 'filepond/extensions/url-loader';
import { 
    createFilePondEntryList, 
    appendEntryImageView 
} from 'filepond/templates';

/**
 * 繁體中文語系 (zh-TW)
 * 透過完全繼承 zh-cn 結構並僅替換文字，確保不會發生 'variables' undefined 錯誤
 */
const zh_TW = {
    ...zh_cn_base,
    // 核心標籤
    abort: '中止',
    remove: '移除',
    reset: '重設',
    undo: '撤銷',
    cancel: '取消',
    store: '上傳',
    revert: '還原',
    busy: '處理中',
    loading: '載入中',
    error: '錯誤',
    warning: '警告',
    success: '成功',
    info: '資訊',
    
    // 檔案類別
    fileMainTypeImage: '圖片',
    fileMainTypeVideo: '影片',
    fileMainTypeAudio: '音訊',
    fileMainTypeApplication: '檔案',
    assistAbort: '點擊中止',
    assistUndo: '點擊撤銷',
    
    // UI 互動
    browse: '瀏覽',
    browseAndDrop: '拖放檔案至此或是 <span class="filepond--label-action"> 點擊瀏覽 </span>',
    loadError: '載入檔案時發生錯誤',
    
    // 驗證訊息 (維持物件結構)
    validationInvalid: '檔案無效',
    validationFileNameMissing: '檔案名稱缺失',
    validationInvalidEntries: '包含無效項目',
    
    // 單位
    unitB: { 1: '位元組', else: '位元組' },
    unitFiles: { 1: '個檔案', else: '個檔案' },

    // 存儲狀態
    storeStorageProgress: '正在上傳 {{progress}}%',
    storeStorageComplete: '上傳完成',
    storeError: '上傳失敗',
};

/**
 * 穩定版 CoreStore
 */
const CoreStore = createStoreExtension('CoreStore', {
    url: null,
    revertUrl: null,
    context: 'Default',
    inputName: 'file'
}, (props) => {
    return {
        storeEntry: (entry, { onprogress, abortController }) => {
            const { url, context, inputName } = props.props;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
                xhr.setRequestHeader('X-Upload-Context', context);
                xhr.upload.onprogress = (e) => onprogress(e.lengthComputable, e.loaded, e.total);
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let res = xhr.responseText.trim();
                        try {
                            const data = JSON.parse(res);
                            if (Array.isArray(data)) {
                                res = data[0];
                            } else if (typeof data === 'object' && data !== null) {
                                res = data.uuid || data.id || res;
                            } else {
                                res = data;
                            }
                        } catch(e) {
                            // res stays as xhr.responseText
                        }
                        
                        if (!res) {
                            console.error('CoreStore: No UUID returned from server');
                            reject('No UUID');
                            return;
                        }
                        
                        // 觸發全域事件
                        document.dispatchEvent(new CustomEvent('FILEPOND_STORE_SUCCESS', {
                            detail: {
                                uuid: res,
                                context: context,
                                entry: entry
                            }
                        }));
                        
                        resolve(res); 
                    } else reject(xhr.status);
                };
                xhr.onerror = () => reject('Error');
                abortController.signal.addEventListener('abort', () => xhr.abort());
                const formData = new FormData();
                formData.append(inputName || 'file', entry.file); 
                xhr.send(formData);
            });
        },
        releaseEntry: async (serverId) => {
            const { revertUrl, context } = props.props;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!serverId || serverId === "[]") return true;
            try {
                const res = await fetch(revertUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'X-Upload-Context': context },
                    body: String(serverId)
                });
                return res.ok;
            } catch (e) { return false; }
        }
    };
});

function filepondLoader() {
    const targets = Array.from(document.querySelectorAll(".filepond")).filter(el => !el.dataset.filepondInit);
    if (targets.length === 0) return;
    targets.forEach(el => el.dataset.filepondInit = "true");

    const listTemplate = createFilePondEntryList();
    appendEntryImageView(listTemplate);

    if (!customElements.get('file-pond')) {
        defineFilePond({
            locale: zh_TW,
            extensions: [
                CoreStore, 
                [EntryListView, { template: listTemplate }],
                FileSizeValidator,
                FileMimeTypeValidator,
                ImageBitmapTransform,
                URLLoader
            ]
        });
    }

    targets.forEach(el => {
        const pond = document.createElement('file-pond');
        const context = el.dataset.context || el.name || 'Unknown';
        
        if (el.dataset.allowtypes) {
            pond.accept = el.dataset.allowtypes.split('::').map(t => t.trim());
        }

        pond.imageBitmapTransform = {
            enabled: true,
            resizeWidth: 320,
            resizeHeight: 240,
            resizeMode: 'contain'
        };

        pond.extensions = [
            [CoreStore, {
                url: el.dataset.upload,
                revertUrl: el.dataset.revert,
                context: context,
                inputName: el.name
            }]
        ];

        pond.setAttribute('should-store', 'true');
        pond.setAttribute('name', el.name);
        pond.locale = zh_TW;

        el.after(pond);
        pond.append(el);

        if (el.dataset.files) {
            try {
                pond.entries = JSON.parse(el.dataset.files);
            } catch (e) {}
        }
    });
}

document.addEventListener("DOMContentLoaded", filepondLoader);
document.addEventListener("CG_FILEPOND::init", filepondLoader);
