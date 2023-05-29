let get_content = async (url) => {
    let response = await fetch(url, {
        credentials: 'include',
        method: 'GET',
        mode: 'cors',
        headers: {
            "Access-Control-Request-Method": "GET",
            "Access-Control-Request-Credentials": true,
            "Access-Control-Request-Private-Network": true
        },
    })
    return await response.json();
}

let show_all_extension_list_template = (data) => {
    let children = '  '
    data.map((value, index, array) => {
        children += `
                <li data-value="${value}">
                <label>
                 <input name="ext_list[]" type="checkbox" data-value="${value}" value="${value}" />
                 ${value}
                </label>
                </li>
                `
    });
    return children;
}
let show_default_extension_list_template = (data) => {
    let children = '  '
    data.map((value, index, array) => {
        document.querySelector(`ul[name="all_extentions"] li input[data-value="${value}"]`).checked = true
    });
}

let show_extension_list = async (all_extension_box) => {
    let [all_extension_list, ready_extension_list] = await Promise.all([
        get_content('/data/extension_list.json'),
        get_content('/data/default_extension_list.json')
    ])
    window['default_ready_exteion_list'] = ready_extension_list;
    all_extension_box.innerHTML = show_all_extension_list_template(all_extension_list)
    show_default_extension_list_template(ready_extension_list)
}

let inputCheckBoxBindEvent = (all_extension_box, ready_extension_list_box) => {

    all_extension_box.addEventListener('click', (event) => {
        if (event.target.nodeName === 'INPUT' || event.target.nodeName === 'LABEL') {
            console.log(event.target.nodeName)
            let element = event.target
            if (element.nodeName === 'INPUT') {
                if (element.checked) {
                    delete element.checked
                }
            }
            document.querySelector('.generate-cmd-button').click()
        } else {
            //  event.stopPropagation();
            //  event.preventDefault();
        }
    }, false)

    let reset_button = document.querySelector('.reset-cmd-button')
    reset_button.addEventListener('click', (event) => {

        let default_ready_extension_list = window['default_ready_exteion_list']
        default_ready_extension_list.map((value) => {
            let ele = document.querySelector(`ul[name="all_extentions"] li input[data-value="${value}"]`)
            if (ele.checked === true) {
                ele.checked = false;
            }
        })
        event.stopPropagation();
        event.preventDefault();
        document.querySelector('.generate-cmd-button').click()
    })
}


let extension_list = () => {
    let all_extension_box = document.querySelector('ul[name="all_extentions"]')
    show_extension_list(all_extension_box);
    inputCheckBoxBindEvent(all_extension_box);
}

export {extension_list}
