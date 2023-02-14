
	const allData = {
        userdata:[],
        userAttached:[]
	};														
	const pass = document.querySelector('#password'),
		  passchange = document.querySelector('#passchange'),
		  formreg = document.querySelector('#formreg'),
		  datauser = document.querySelectorAll('[data-user]'),
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

		e.preventDefault();
		datauser.forEach(item => {
			allData.userdata.push(item.value);
        });

        allData.userAttached.push($('#attached').val());
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
				if(dataform == 'add') formreg.reset();
				allData.userdata = [];
				allData.userAttached = [];
				

            } else {
                spinner.remove();
                statusMessage.textContent = message.failure;
            }
        });
    }

	

