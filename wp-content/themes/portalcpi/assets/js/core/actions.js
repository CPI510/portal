
	const allData = {};
	const pass = document.querySelector('#password'),
		  passchange = document.querySelector('#passchange'),
		  formreg = document.querySelector('#formreg'),
          datauser = document.querySelectorAll('[data-user]'),
          datagroup = document.querySelectorAll('[data-group]'),
		  datauserattached = document.querySelector('#attached'),
		  dataform = formreg.getAttribute('data-form'),
		  boxdiv = document.querySelector('.box'),
          alertDanger = document.querySelector('.alert-danger');

	const message = {
          loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
          success: "Спасибо, все данные внесены",
          failure: "Что-то пошло не так, попробуйте зайти позднее!"
    };

    if(passchange){
        passchange.addEventListener('click', () => {
            if(pass.type === "text") {
                pass.type = "password";
                passchange.textContent = "Показать";
            }else {
                pass.type = "text";
                passchange.textContent = "Скрыть";
            }
        })
    }


	const statusMessage = document.createElement('div');
	
	statusMessage.classList.add('status');
		  
    boxdiv.append(statusMessage);

	formreg.addEventListener('submit', saveData);

	function saveData(e){
	    let userdata;

		e.preventDefault();

        datauser.forEach(item => {

            let keyName = item.getAttribute("name");
            allData[[keyName]] = item.value;

        });

        //allData.push($('#attached').val());
		//console.log(allData.userAttached);

		const request = new XMLHttpRequest();
        request.open('POST', `${document.location.origin}/portal_server/`);

        request.setRequestHeader('Content-type', 'application/json');
        const json = JSON.stringify(allData);

        request.send(json);

        const spinner = document.createElement('img');
        spinner.src = message.loading;
        boxdiv.append(spinner);

        request.addEventListener('load', () => {
            if (request.status === 200){
                //console.log(request.response);

                // statusMessage.textContent = message.success;
                statusMessage.innerHTML = request.response;
				spinner.remove();

				/*if(dataform == 'add') formreg.reset();
                else {
                    //location.href = window.location.href;
                }*/
                for (var member in allData) delete allData[member];
                //console.log(allData);

            } else {
                spinner.remove();
                statusMessage.textContent = message.failure;
            }
        });
    }

	

