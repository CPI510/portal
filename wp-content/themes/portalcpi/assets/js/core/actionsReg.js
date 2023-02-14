
	const allData = {
		userdata:[]
	};														
	const pass = document.querySelector('#password'),
          passchange = document.querySelector('#passchange'),
          pass2 = document.querySelector('#password2'),
		  passchange2 = document.querySelector('#passchange2'),
		  formreg = document.querySelector('#formreg'),
		  datauser = document.querySelectorAll('[data-user]'),
		  boxdiv = document.querySelector('.box'),
          alertDanger = document.querySelector('.alert-danger'),
          regbtn = document.querySelector('#reg'),
          carddiv = document.querySelector('.card'),
          regdiv = document.querySelector('.registration'),
          alertdiv = document.querySelector('.alert'),
          groupreg = document.querySelector('#group_reg');

	const message = {
          loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
          success: "Спасибо, все данные внесены",
          failure: "Что-то пошло не так, попробуйте зайти позднее!"
    };

    if (passchange && pass) typePassChange(passchange,pass);
    if (passchange2 && pass2) typePassChange(passchange2,pass2);

    function typePassChange(btn, typeinput){
        btn.addEventListener('click', () => {
            if(typeinput.type === "text") {
                typeinput.type = "password";
                if( document.querySelector('#kz').value == 1 ){
                    btn.textContent = "Парольді көрсету";
                }else{
                    btn.textContent = "Показать пароль";
                }

            }else {
                typeinput.type = "text";
            if( document.querySelector('#kz').value == 1 ){
                btn.textContent = "Парольді жасыру";
            }else{
                btn.textContent = "Скрыть пароль";
            }

            }
        })
    }
        
    

    if(regbtn) regbtn.addEventListener('click', ()=> {
        regdiv.classList.remove('hide');
        regdiv.classList.add('show');
        carddiv.classList.remove('show');
        carddiv.classList.add('hide');
        if (alertdiv) alertdiv.classList.add('hide');
    })

	

	const statusMessage = document.createElement('div');
	
	statusMessage.classList.add('status');
		  
    boxdiv.append(statusMessage);

	formreg.addEventListener('submit', saveData);

	function saveData(e){

        let w = 0;
		e.preventDefault();
		datauser.forEach(item => {
            if(w == 5 && document.querySelector('[data-mailafter]')) {
                item.value = item.value + document.querySelector('[data-mailafter]').value;
            }
			allData.userdata.push(item.value);
            w++;
        });
		// console.log(allData);

		const request = new XMLHttpRequest();
        request.open('POST', `${document.location.origin}/server_user/`);

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
                allData.userdata = [];
                if(groupreg) groupreg.setAttribute('disabled','disabled');
                if(regdiv){
                    regdiv.classList.remove('show');
                    regdiv.classList.add('hide');
                    carddiv.classList.remove('hide');
                    carddiv.classList.add('show');
                    if(alertdiv) alertdiv.classList.add('hide');
                }
                //console.log(boxdiv);
                if(boxdiv.querySelector('.alert-danger')){
                    regdiv.classList.remove('hide');
                    regdiv.classList.add('show');
                    carddiv.classList.remove('show');
                    carddiv.classList.add('hide');
                }


            } else {
                spinner.remove();
                statusMessage.textContent = message.failure;
            }
        });
    }
