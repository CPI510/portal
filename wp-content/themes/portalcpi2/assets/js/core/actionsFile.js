window.addEventListener('DOMContentLoaded', () => {

    const uploadInput = document.querySelector('#uploadInput'),
          load = document.querySelector('#load'),
          textbox = document.getElementById("ajax-respond");

    var files;
    $('input[type=file]').change(function(){
        files = this.files;
        if(uploadInput.value == "") load.setAttribute("disabled", "disabled");
        else load.removeAttribute("disabled");
    });

    $('.submit.button').click(function( event ){
        event.stopPropagation();
        event.preventDefault();
    
        
        var data = new FormData();
        $.each( files, function( key, value ){
            data.append( key, value );
        });
        data.append( 'folder', getLinkData()['folder_id'] );

        const message = {
            loading: `${document.location.origin}//wp-content/themes/portalcpi/assets/img/spinner.svg`,
            success: "Спасибо, все данные внесены",
            failure: "Что-то пошло не так, попробуйте зайти позднее!"
        };

        const boxdiv = document.querySelector('.ajax-respond'),
            statusMessage = document.createElement('div');
        
        statusMessage.classList.add('status');
            
        boxdiv.append(statusMessage);

        const spinner = document.createElement('img');
            spinner.src = message.loading;
            boxdiv.append(spinner);

        load.setAttribute("disabled", "disabled");
    
        $.ajax({
            url: `${document.location.origin}/server_file/`,
            type: 'POST',
            data: data, 
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function( respond, textStatus, jqXHR ){
                if ( typeof respond.error === 'undefined' ){
                    getData(respond.idf);
                } else {
                    console.log('Ошибка отправки файла на сервер: ' + respond.error );
                }
                spinner.remove();
                load.removeAttribute("disabled");
                uploadInput.value = "";
                load.setAttribute("disabled", "disabled");
            },
            error: function( jqXHR, textStatus, errorThrown ){
                statusMessage.innerHTML = message.failure;
                console.log('При отправке Ajax-запроса возникла ошибка: ' + textStatus );
                spinner.remove();
                load.removeAttribute("disabled");
                uploadInput.value = "";
                load.setAttribute("disabled", "disabled");
            }
        });
    });


    function updateSize() {
        var file = uploadInput.files[0],
        ext = "не определилось",
        parts = file.name.split('.');
        if (parts.length > 1) ext = parts.pop();
        // document.getElementById("ajax-respond").innerHTML = [
        // "Размер файла: " + file.size + " B",
        // "Расширение: " + ext,
        // "MIME тип: " + file.type
        // ].join("<br>");

        if(!fileExt(ext)){
            textbox.innerHTML = `<div class="alert alert-callout alert-warning" role="alert">
            <strong>Внимание!</strong> Не допустимый файл! Разрешенные файлы: doc, docx, pdf, ppt, pptx
        </div>`;
            load.setAttribute("disabled", "disabled");
        }else{
            textbox.innerHTML ='';
        }
    }

    function fileExt(data){
        switch(data){
            case 'doc' : return true; 
            break;
            case 'docx' : return true; 
            break;
            case 'pdf' : return true; 
            break;
            case 'ppt' : return true; 
            break;
            case 'pptx' : return true; 
            break;
            default: return false;
            break;
        }
    }
  
  uploadInput.addEventListener('change', updateSize);
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function getData(id){
    const request = new XMLHttpRequest();

    request.open('GET', `${document.location.origin}/server_file/?id=${id}`);
    request.setRequestHeader('Content-type', 'application/json; charset=utf-8');
    request.send();

    request.addEventListener('load', () => {
        if (request.status === 200){
            const data = JSON.parse(request.response);
            addRow('dataFile', data.files, id);
        }else{
            console.log('error');
        }
    });
}

function deleteFile(fileid){
    const request = new XMLHttpRequest();

    request.open('GET', `${document.location.origin}/server_file/?del=${fileid}`);
    request.setRequestHeader('Content-type', 'application/json; charset=utf-8');
    request.send();

    request.addEventListener('load', () => {
        if (request.status === 200){
            const data = JSON.parse(request.response);
            delRow('dataFile', data);
        }else{
            console.log('error');
        }
    });
}

function addRow(table, val, id){
    const tbody = document.getElementById(table).getElementsByTagName("TBODY")[0],
          row = document.createElement("TR"),
          td1 = document.createElement("TD"),
          td2 = document.createElement("TD"),
          td3 = document.createElement("TD"),            
          td4 = document.createElement("TD"),
          td5 = document.createElement("TD"),
          a = document.createElement("a"),
          link = document.createTextNode(val.filename);

    a.appendChild(link);
    a.title = val.filename;
    a.href = `/server_file/?download=${id}`;
    a.classList.add('text-primary');

    td1.appendChild (document.createTextNode(val.folder_name));
    td2.appendChild(a);
    td3.appendChild (document.createTextNode(formatBytes(val.filesize)));
    td4.appendChild (document.createTextNode(val.datecreate));
    td5.innerHTML = `<a href="#" class="btn btn-icon-toggle" onclick="deleteFile('${id}');" data-del="${id}" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>`;

    row.appendChild(td1);
    row.appendChild(td2);
    row.appendChild(td3);
    row.appendChild(td4);
    row.appendChild(td5);

    tbody.appendChild(row);
}

function delRow(table, id){
    const link = document.querySelectorAll('[data-del]');
    
    link.forEach(element => {        
        if( element.getAttribute('data-del') == id) { 
            element.parentNode.parentNode.remove();
        }
    });
}

function getLinkData(){
    return window
    .location
    .search
    .replace('?','')
    .split('&')
    .reduce(
        function(p,e){
            var a = e.split('=');
            p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
            return p;
        },
        {}
    );
}