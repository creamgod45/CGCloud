import { defineFilePond, createStoreExtension } from 'filepond';

// 導入語系
import { locale as chinese_base } from 'filepond/locales/zh-cn';

// 單獨導入擴充功能，避開 Vite 解析錯誤
import { EntryListView } from 'filepond/extensions/entry-list-view';
import { FileSizeValidator } from 'filepond/extensions/file-size-validator';
import { FileMimeTypeValidator } from 'filepond/extensions/file-mime-type-validator';
import { ImageBitmapTransform } from 'filepond/extensions/image-bitmap-transform';
import { URLLoader } from 'filepond/extensions/url-loader';

// 導入模板與 UI 元件
import { 
    createFilePondEntryList, 
    appendEntryImageView 
} from 'filepond/templates';

const chinese_tw = {
    ...chinese_base,
    labelIdle: '拖放檔案至此或是 <span class="filepond--label-action"> 點擊瀏覽 </span>',
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
                            res = Array.isArray(data) ? data[0] : (data.uuid || data.id || res);
                        } catch(e) {}
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
            locale: chinese_tw,
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
