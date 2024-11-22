import * as Utils from './components/utils.js'
import axios from "axios";

Utils.getClientFingerprint().then(fingerprint => {
    axios.post("/hello", {
        ID: fingerprint
    }, {
        adapter: "fetch"
    }).then((response) => {
        if (response.data.message === "ok") {
            let refElement = document.querySelector("[name=ref]");
            if (refElement !== null) {
                window.location.href = refElement.value;
            }
        }
    });
});
