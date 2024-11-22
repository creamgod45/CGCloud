import TinyMCE from "tinymce";

async function tinymceLoader() {
    let tinymces = document.querySelectorAll(".tinymceEditor");
    for (let tinymceEl of tinymces) {
        let baseurl = tinymceEl.dataset.baseurl;
        let menubar = tinymceEl.dataset.menubar;
        let resize = tinymceEl.dataset.resize;
        let minheight = tinymceEl.dataset.minheight;
        let maxheight = tinymceEl.dataset.maxheight;
        let language = tinymceEl.dataset.language;
        let valid_elements = tinymceEl.dataset.validelements;
        let invalid_elements = tinymceEl.dataset.invalidelements;
        if (baseurl === undefined) return false;
        if (menubar === undefined) menubar = true;
        if (menubar === "false") menubar = false;
        if (resize === undefined) resize = true;
        if (resize === "false") resize = false;
        if (minheight === undefined) minheight = null;
        if (minheight !== null) minheight = parseInt(minheight);
        if (maxheight === undefined) maxheight = null;
        if (maxheight !== null) maxheight = parseInt(maxheight);
        if (language === undefined) language = "zh_TW";
        let options = {
            selector: ".tinymceEditorInit",
            license_key: 'gpl',
            base_url: baseurl,
            language: language,
            language_load: true, // Disables the automatic loading of additional plugin and theme language files.
            menubar: menubar,
            resize: resize,
            min_height: minheight,
            max_height: maxheight,
            schema: "html5-strict",
            letterspacing: "0px 2px 4px 6px 24px",
            layout_options: {
                style: {
                    'text-align': 'justify', 'text-indent': '2em', 'line-height': 1.5
                },
                filterTags: ['table>*', 'tbody'], //'table，'tbody','td','tr' 将会忽略掉 同时 table>*，忽略table 标签 以及所有子标签
                clearStyle: ['text-indent'],//text-indent 将会被清除掉
                tagsStyle: {
                    'table': {
                        'line-height': 3, 'text-align': 'center'
                    }, 'table,tbody,tr,td': { //支持并集选择
                        'line-height': 2
                    }, 'tr>td,table>tbody': { //支持, 精准定位 通过 ' > '
                        'line-height': 3, 'text-align': 'center'
                    }
                }
            },
            toolbar: [
                'undo redo fontfamily fontsize bold strikethrough underline italic align indent indent2em backcolor forecolor code table codeformat letterspacing layout hr lineheight subscript superscript visualaid restoredraft anchor casechange charmap code codesample emoticons fullscreen help image insertdatetime link unlink media preview searchreplace cut copy paste pastetext | removeformat deleteallconversations',
            ],
            plugins: [
                'accordion',
                'advlist',
                'anchor',
                'attachment',
                'autolink',
                'autoresize',
                'autosave',
                'charmap',
                'code',
                'codesample',
                'directionality',
                'emoticons',
                'fullscreen',
                'help',
                'image',
                'importcss',
                'indent2em',
                'insertdatetime',
                'layout',
                'letterspacing',
                'lineheight',
                'link',
                'lists',
                'media',
                'nonbreaking',
                'pagebreak',
                'preview',
                'quickbars',
                'save',
                'searchreplace',
                'table',
                'upfile',
                'visualblocks',
                'visualchars',
                'wordcount',
            ],
        };
        if (valid_elements !== undefined) {
            options["valid_elements"] = valid_elements;
        }
        if (invalid_elements !== undefined) {
            options["invalid_elements"] = invalid_elements;
        }
        tinymceEl.classList.add('tinymceEditorInit');
        await TinyMCE.init(options);
    }
}

document.addEventListener('DOMContentLoaded', tinymceLoader);
