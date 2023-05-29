let extension_list = async() => {
    let response = await fetch('/data/extension_list.json',{
        credentials: 'include',
        headers: {
            "Access-Control-Request-Credentials": true,
            "Access-Control-Request-Private-Network": true
        },

    })
    let res = await response.json();
if (response.status === 200 && res) {
    let extension_list = document.querySelector('ul[name="all_extentions"]')
    if (extension_list) {
        let children = '  '
        res.map((value, index, array) => {
            children += `
                <li value="${value}">
                <label>
                     <input name="ext_${value}" type="checkbox" value="${value}" />
                     ${value}
                 </label>
               </li>
            `
            });

        extension_list.innerHTML = children;
    }
}
    default_ready_extension_list();
}
let default_ready_extension_list = async()=>{
    let response = await fetch('/data/default_extension_list.json',{
        credentials: 'include',
        headers: {
            "Access-Control-Request-Method": "GET",
            "Access-Control-Request-Credentials": true,
            "Access-Control-Request-Private-Network": true
        },

    })
    let res = await response.json();
if (response.status === 200 && res ) {
    let extension_list = document.querySelector('ul[name="ready_extentions"]')
    if (extension_list) {
        let children = '  '
        res.map((value, index, array) => {
            let ele=document.querySelector(`ul[name="all_extentions"] li[value="${value}"]`)
            ele.classList.add('ready_extension')
            let input=ele.querySelector(`input[name="ext_${value}"]`)
            input.setAttribute('checked',true)

            children += `
                <li value="${value}" class="ready_extension">
                 <label>
                     <input name="ready_ext_${value}" type="checkbox" value="${value}" checked="true" />
                     ${value}
                 </label>
                </li>

            `
            });

        extension_list.innerHTML = children;
    }
}
    let submmit_btn=document.querySelector('.exec-button')
if (submmit_btn) {
    submmit_btn.addEventListener('click',exec)
}
}

let exec=(e)=>{

    let os=document.querySelector('select[name="os"]')
    let with_docker =document.querySelector('select[name="without-docker"]')
    let skip_download =document.querySelector('select[name="skip-download"]')
    let with_download_mirror_url =document.querySelector('select[name="with-download-mirror-url"]')
    let with_dependecny_graph=document.querySelector('select[name="with-dependency-graph"]')
    let cmd="  @os="+os.value
        cmd +="  --without-docker="+with_docker.value
        cmd +="  --skip-download="+skip_download.value
        cmd +="  --with-download-mirror-url="+with_download_mirror_url.value
        cmd +="  --with-dependency-graph="+with_dependecny_graph.value
        console.log(cmd)

}
export {extension_list}
