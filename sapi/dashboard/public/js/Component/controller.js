import {init, send} from "./message.js"

let gen = (e) => {

    let os = document.querySelector('select[name="os"]')
    let with_docker = document.querySelector('select[name="without-docker"]')
    let skip_download = document.querySelector('select[name="skip-download"]')
    let with_download_mirror_url = document.querySelector('select[name="with-download-mirror-url"]')
    let with_dependecny_graph = document.querySelector('select[name="with-dependency-graph"]')
    let with_web_ui = document.querySelector('select[name="with-web-ui"]')
    let cmd = "php prepare.php"

    if (os.value === 'macos') {
        cmd += " @os=" + os.value
    }

    if (with_docker.value === "1") {
        cmd += "  --without-docker=" + with_docker.value
    } else {
        if (os.value === 'macos') {
            cmd += "  --without-docker=1"
        }
    }
    if (skip_download.value === "1") {
        cmd += "  --skip-download=" + skip_download.value
    }
    if (with_download_mirror_url.value !== "0") {
        cmd += "  --with-download-mirror-url=" + with_download_mirror_url.value
    }
    if (with_dependecny_graph.value === "1") {
        cmd += "  --with-dependency-graph=" + with_dependecny_graph.value
    }
    if (with_web_ui.value === "1") {
        cmd += "  --with-web-ui=" + with_web_ui.value
    }

    let extenion_list_obj = document.querySelectorAll('ul[name="all_extentions"] input[type=checkbox]')
    if (extenion_list_obj.length > 0) {
        let extension_list = []
        extenion_list_obj.forEach((value, key, parent) => {
            if (value.checked === true) {
                extension_list.push(value.value)
            }

        })
        let default_ready_extension_list = window['default_ready_exteion_list']
        console.log(extension_list, default_ready_extension_list)
        //交集
        let intersect = extension_list.filter(x => default_ready_extension_list.includes(x))
        console.log(intersect)

        //差集
        let minus = extension_list.filter(x => !default_ready_extension_list.includes(x))
        console.log(minus)
        if (minus.length > 0) {
            minus.map((value, index, array) => {
                cmd += " +" + value;
            })

        }
        //补集
        let complement = default_ready_extension_list.filter(x => !extension_list.includes(x))
        console.log(complement)
        if (complement.length > 0) {
            complement.map((value, index, array) => {
                cmd += " -" + value;
            })
        }
    }

    console.log(cmd)
    let codeBox = document.querySelector('.preview-code .pre-code.preprocessor')
    codeBox.innerText = cmd;
    document.querySelector('.exec-button').setAttribute('data-cmd', cmd)

}
let ButtonController = (e) => {
    let submmit_btn = document.querySelector('.generate-cmd-button')
    if (submmit_btn) {
        submmit_btn.addEventListener('click', gen)
    }
}

let selectedBindEvent = () => {
    let option_list = document.querySelector('.options-list')
    if (option_list) {
        option_list.addEventListener('click', (event) => {
            if (event.target.nodeName === 'SELECT') {
                console.log(event.target)
                gen()
            } else {
                event.stopPropagation()
                event.preventDefault()
            }

        }, false)
    }

}
let runButtonBindEvent = () => {
    let run = document.querySelector('.exec-button')
    run.addEventListener('click', (event) => {
        let cmd = event.target.getAttribute('data-cmd');
        console.log(cmd)
        let message={
            "action":"preprocessor",
            "data":cmd
        }
        send(JSON.stringify(message));
    })
}
let show_controller = () => {
    ButtonController()
    selectedBindEvent()
    gen()
    runButtonBindEvent()
    init()
}
export {show_controller}
