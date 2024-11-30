//import TomSelect from "tom-select/base";
import TomSelect from 'tom-select/dist/esm/tom-select.complete.js';
import axios from "axios";

//https://tom-select.js.org/docs/
function TomSelect_() {
    let tomSelectElements = document.querySelectorAll('.tom-select');
    for (let tomSelectElement of tomSelectElements) {
        let dataset = tomSelectElement.dataset;
        let multiple = dataset.multiple;
        let allowclear = dataset.allowclear == 'true';
        let placeholder = dataset.placeholder; //The placeholder of the control. Defaults to input element's placeholder, unless this one is specified. To update the placeholder setting after initialization, call inputState()
        let items = dataset.items;
        let options = dataset.options;
        let create = dataset.create === "true"; // Determines if the user is allowed to create new items that aren't in the initial list of options. This setting can be any of the following: true, false, or a function.
        let persist = dataset.persist === "true"; // If false, items created by the user will not show up as available options once they are unselected.
        let maxItems = dataset.maxitems;
        let delimiter = dataset.delimiter;
        let allowEmptyOption = dataset.allowemptyoption === "true";
        let duplicates = dataset.duplicates === "true";
        let maxOptions = dataset.maxoptions;
        let preload = dataset.preload === "true";
        let src = dataset.src;
        let perpage = dataset.perpage;

        if (maxOptions === undefined) {
            maxOptions = null;
        } else {
            maxOptions = parseInt(maxOptions);
        }

        let custome_options = {
            placeholder: placeholder,
            create: create,
            persist: persist,
            delimiter: delimiter ?? ',',
            allowEmptyOption: allowEmptyOption,
            selectOnTab: true,
            duplicates: duplicates,
            maxOptions: maxOptions,
            preload: preload,

        };
        if (src !== undefined) {
            custome_options.valueField = 'value';
            custome_options.labelField = 'name';
            custome_options.searchField = 'name';
            if (perpage !== undefined) custome_options.maxOptions = parseInt(perpage); else custome_options.maxOptions = 15;
            if (custome_options.hasOwnProperty('plugins')) {
                custome_options['plugins'].push('virtual_scroll');
            } else {
                custome_options['plugins'] = ['virtual_scroll'];
            }
            custome_options.render = {};
            // custome_options.render.loading_more = function(data, escape) {
            //     return `<div class="loading-more-results py-2 d-flex align-items-center"><div class="spinner"></div> Loading more results from reddit </div>`;
            // };
            // custome_options.render.no_more_results = function(data,escape){
            //     return `<div class="no-more-results">No more results</div>`;
            // }
            custome_options.firstUrl = function (query) {
                return src;
            };

            custome_options.shouldLoad = function (query) {
                console.log(query);
                console.log(this.currentResults.items.length);
                console.log(this);
                console.log(this.maxOptions);

                const getUnixTime = () => {
                    return Math.floor(Date.now() / 1000);
                };

                if (this.timeout === null || this.timeout === undefined) {
                    this.timeout = getUnixTime();
                }

                if (this.currentResults.items.length < this.settings.maxOptions - 5 && this.currentResults.items.length !== 0 && getUnixTime() > this.timeout) {
                    this.timeout = getUnixTime() + 3;
                    console.log('load');
                    let item = this.items;
                    this.load(query, function (e) {
                        item = e;
                    });
                }
                return true;
            };

            custome_options.load = function (query, callback) {
                console.log(query);
                if(query === "") return callback([]);
                //const url = this.getUrl(query);
                axios.post(src, {
                    query: query, selectedItems: this.items,
                }).then(res => {
                    let items = res.data.data;
                    console.log(res.data);
                    console.log(items);
                    callback(items);
                    //this.setNextUrl(query, res.data.next_page_url)
                }).catch(err => {
                    console.log(err);
                    callback([]);
                })
            };
        }

        if (items !== undefined) {
            custome_options['items'] = JSON.parse(items);
        }
        if (options !== undefined) {
            custome_options['options'] = JSON.parse(options);
        }
        if (maxItems !== undefined) {
            custome_options['maxItems'] = maxItems;
        }
        if (allowclear) {
            if (custome_options.hasOwnProperty('plugins')) {
                custome_options['plugins'].push('clear_button');
            } else {
                custome_options['plugins'] = ['clear_button'];
            }
        }
        let tomSelect = new TomSelect(tomSelectElement, custome_options);
    }
}

document.addEventListener('DOMContentLoaded', TomSelect_);
