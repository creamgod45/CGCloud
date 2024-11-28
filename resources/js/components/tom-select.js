import TomSelect from "tom-select/base";

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

        if(maxOptions === undefined) {
            maxOptions = null;
        }else{
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
        if(items !== undefined) {
            custome_options['items'] = JSON.parse(items);
        }
        if(options !== undefined) {
            custome_options['options'] = JSON.parse(options);
        }
        if(maxItems !== undefined) {
            custome_options['maxItems'] = maxItems;
        }
        if(allowclear) {
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
