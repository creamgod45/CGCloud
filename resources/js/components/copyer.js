function copyer(){
    let elementNodeListOf = document.querySelectorAll(".copyer:not(.copied)");
    for (let copyer of elementNodeListOf) {
        copyer.classList.add("copied");
        let url = copyer.dataset.url;
        if(url === undefined) return false;
        copyer.onclick = () => {
            navigator.clipboard.writeText(url).then(() => {
                console.log('Text copied to clipboard:', url);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        }
    }
}

document.addEventListener('CGCP::init', copyer);
document.addEventListener('DOMContentLoaded', copyer);
