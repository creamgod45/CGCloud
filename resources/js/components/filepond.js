import * as FilePond from 'filepond';
import zh_tw from "filepond/locale/zh-tw.js";
// 引入 FilePond 插件
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFilePoster from 'filepond-plugin-file-poster';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';
import * as Utils from "./utils.js";
import axios from "axios";

// 初始化 FilePond 插件並應用到所有包含標籤 'filepond' 的元素。
function filepondLoader() {
    let filepond = document.querySelectorAll(".filepond"); // 選擇所有類名為 ‘filepond’ 的元素
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // 獲取 CSRF token
    for (let filepondElement of filepond) { // 遍歷每個元素
        // 註冊所有需要使用的 FilePond 插件
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.registerPlugin(FilePondPluginImageTransform);
        FilePond.registerPlugin(FilePondPluginImageExifOrientation);
        FilePond.registerPlugin(FilePondPluginFileValidateSize);
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        FilePond.registerPlugin(FilePondPluginFilePoster);
        FilePond.registerPlugin(FilePondPluginImageValidateSize);

        // 獲取上傳、取消和修補的 URL
        let process = filepondElement.dataset.upload;
        let revert = filepondElement.dataset.revert;
        let patch = filepondElement.dataset.patch;
        let thumbable = filepondElement.dataset.thumbable === "true";
        if (process === undefined || revert === undefined || patch === undefined) return; // 如果 URL 有任一未定義則返回

        patch = patch.replaceAll("%20", ""); // 修正 patch 路徑
        let allowMultiple = filepondElement.dataset.multiple; // 是否允許上傳多個文件
        let maxFileSize = filepondElement.dataset.maxfilesize; // 允許的最大文件大小
        let maxTotalFileSize = filepondElement.dataset.maxtotalfilesize; // 允許的最大總文件大小

        // 允許的文件類型
        let raw_allowtypes = filepondElement.dataset.allowtypes;
        let allowtypes = [];
        for (let type of raw_allowtypes.split("::")) {
            type = type.trim();
            allowtypes.push(type);
        }

        // 設置已上傳的文件
        let raw_files = filepondElement.dataset.files;
        let files = raw_files !== undefined ? JSON.parse(raw_files) : null;

        // 設定默認值
        maxTotalFileSize = maxTotalFileSize === undefined ? null : maxTotalFileSize;
        maxFileSize = maxFileSize === undefined ? null : maxFileSize;
        allowMultiple = allowMultiple === undefined ? false : allowMultiple === "true";

        FilePond.setOptions(zh_tw); // 設定語言
        let id = Utils.generateRandomString(8); // 生成隨機字符串 ID
        filepondElement.fileid = id;
        filepondElement.uploaded = false;

        let form = filepondElement.form !== null ? filepondElement.form : null;
        let fileAttachmentName = filepondElement.name.replaceAll("[]", "") + "Attachment";
        let fileAttachmentInput = filepondElement.parentElement.querySelector("input[name='"+fileAttachmentName+"']");

        let filepond = FilePond.create(filepondElement); // 創建 FilePond 實例
        if (fileAttachmentInput !== null) {
            fileAttachmentInput.filepond = filepond;
            fileAttachmentInput.upload_files = [];
        }

        // 設定 FilePond 選項
        let options = {
            server: {
                process: process,
                revert: revert,
                patch: {
                    url: patch.trim(),
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        "Upload-Id": Utils.generateRandomString(8)
                    }
                },
                restore: null,
                fetch: '/fetch',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            },
            allowMultiple: allowMultiple,
            chunkUploads: true,
            chunkSize: 10000000,
            chunkRetryDelays: [500, 1000, 3000],
            files: files,
            instantUpload: false,
            maxFileSize: maxFileSize,
            maxTotalFileSize: maxTotalFileSize,
            labelMaxFileSizeExceeded: '文件太大',
            labelMaxFileSize: '最大檔案大小為 {filesize}',
            labelMaxTotalFileSizeExceeded: '超出最大總大小',
            labelMaxTotalFileSize: '最大總檔案大小為 {filesize}',
            acceptedFileTypes: allowtypes,
            labelFileTypeNotAllowed: '文件類型無效',
            fileValidateTypeLabelExpectedTypes: '需要 {allButLastType} 或 {lastType}',
        };
        if(thumbable){
            options.imageTransformVariants = {
                thumb_medium_: (transforms) => {
                    transforms.resize = {
                        size: { width: 280, height: 157 },
                    };
                    return transforms;
                },
                thumb_small_: (transforms) => {
                    transforms.resize = {
                        size: { width: 132, height: 132 },
                    };
                    return transforms;
                },
            };
        }
        FilePond.setOptions(options);

        // 文件添加事件處理
        filepond.on('addfile', function () {
            fileAttachmentInput.upload_files = filepond.getFiles();
        })

        // 文件還原處理
        filepond.on('processfilerevert', function (file) {
            filepondElement.uploaded = false;
        });

        // 文件處理事件
        filepond.on('processfile', function (file) {
            filepondElement.uploaded = true;
        });

        filepond.on('processfileabort', function (file) {
            filepondElement.uploaded = false;
        });

        if (form !== null) {
            form.onsubmit = function () {
                return filepondElement.uploaded; // 提交表單前檢查是否有文件成功上傳
            };
        }
    }
}

// DOM 加載完成後執行 filepondLoader 函數
document.addEventListener("DOMContentLoaded", function () {
    filepondLoader();
});
