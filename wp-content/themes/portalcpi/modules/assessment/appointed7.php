<?php
global $wpdb;

//printAll($_POST); exit();

if ( isset($_POST['group_id']) && isset($_POST['addmoder']) && isset($_POST['appointed_user_id']) && isset($_POST['link']) && getAccess( get_current_user_id() )->access == 1 ){

    foreach ($_POST['addmoder'] as $user => $on){

        if( $wpdb->insert('p_appointed7', [
            'group_id' => $_POST['group_id'],
            'user_id' => $user,
            'appointed_user_id' => $_POST['appointed_user_id'],
            'link' => $_POST['link']
        ],[
            '%d', '%d', '%d', '%s'
        ]) ){
            alertStatus('success', 'Сохранено');
        } else {
            echo $wpdb->last_error;
        }

        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['group_id'].'" />';
    }

}

if ( isset($_POST['group_id']) && isset($_POST['appointed_for_del']) && getAccess( get_current_user_id() )->access == 1 ){

    foreach ($_POST['appointed_for_del'] as $user => $appointed_user_id){

        if( $wpdb->delete('p_appointed7', [
            'group_id' => $_POST['group_id'],
            'user_id' => $user,
            'appointed_user_id' => $appointed_user_id
        ],[
            '%d', '%d', '%d'
        ]) ){
            alertStatus('success', 'Сохранено');
        } else {
            echo $wpdb->last_error;
        }

        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['group_id'].'" />';
    }

}
?>

<?php if ( isset($_GET['list']) && $_GET['list'] == 'all' ){
    
    get_header();
    ?><p></p>
    <section>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-head">
                        <header>
                            Назначенные (закрепленные) слушатели
                        </header>
                    </div>
                    <div class="card-body">
                        <?php
                        echo "<table class='table table-striped no-margin'>
                            <tr>
                                <th>#</th>
                                <th>Код слушателя</th>
                                <th>Ссылка</th>
                                <th>Рубрика</th>
                            </tr>";
                        $q = 0;
                        foreach ($wpdb->get_results( $wpdb->prepare( "SELECT a.id, a.user_id, b.code, a.group_id, c.number_group, a.link
FROM p_appointed7 a
LEFT JOIN p_assessment_coding_user b ON a.user_id = b.listener_id 
LEFT JOIN p_groups c ON a.group_id = c.id WHERE a.appointed_user_id = %d", get_current_user_id() ) ) as $result){
                            $q++;
                            $rubriclink7 = '<a href="#" id="fileu" data-id="'.$result->user_id.'" data-link="assessment/?z=rubric&id='.$result->group_id.'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                ' . ASSESSMENT_SECOND[2] . '
                                            </a>';
                            echo "
                            <tr>
                                <td>$q</td>
                                <td>";  if( empty(!$result->code) ) echo  $result->code; else echo "Код не записан"; echo "</td>
                                <td><a href='{$result->link}' target='_blank' class='btn btn-success'>Открыть ссылку</a></td>
                                <td>$rubriclink7</td>
                            </tr>";
                        }
                        echo "</table>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <form id="formreg">
        <div class="modal fade" id="Modal" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="box"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><?= ASSESSMENT_SECOND[1] ?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </form>

    <script>

        const allData = {};
        const fileu = document.querySelectorAll("#fileu"),
            boxdiv = document.querySelector('.box'),
            formreg = document.querySelector('#formreg')
        close = document.querySelector("#close");
        const message = {
            loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
            success: "Спасибо, все данные внесены",
            failure: "Что-то пошло не так, попробуйте зайти позднее!"
        };
        const statusMessage = document.createElement('div');

        statusMessage.classList.add('status');

        boxdiv.append(statusMessage);

        for (var i = 0; i < fileu.length ; i++) {
            //let dataid = fileu[i].getAttribute('data-id');
            fileu[i].addEventListener('click', saveData);
            //console.log(fileu[i].getAttribute('data-id'));
        }

        formreg.addEventListener('submit', saveOn);
        function saveOn(event) {
            event.preventDefault();
            //console.log(event);
            var target = event.currentTarget;
            const boxcoding = document.querySelector('.boxcoding'),
                savecodebtn = document.querySelector("#savecodebtn");

            let datacoding = "";
            if(allData.fileuserdata === 'adduser'){
                datacoding = document.querySelectorAll("[data-user]");
            }else{
                datacoding = document.querySelectorAll("[data-assessment]");
            }

            datacoding.forEach(item => {

                let keyName = item.getAttribute("name");
            allData[[keyName]] = item.value;

        });
            //console.log(allData);
            const request = new XMLHttpRequest();
            request.open('POST', `${document.location.origin}/${allData.link}`);
            request.setRequestHeader('Content-type', 'application/json');
            const json = JSON.stringify(allData);
            request.send(json);

            const spinner = document.createElement('img');
            spinner.src = message.loading;
            boxcoding.append(spinner);
            request.addEventListener('load', () => {
                if (request.status === 200){
                boxcoding.innerHTML = request.response;
                spinner.remove();
                allData.code = "";
                allData.section_a_grade = "";
                allData.section_a_description = "";
                allData.section_b_grade = "";
                allData.section_b_description = "";
                allData.section_c_grade = "";
                allData.section_c_description = "";
                allData.action = "";
                //for (var member in allData) delete allData[member];
            } else {
                spinner.remove();
                boxcoding.textContent = message.failure;
                allData.code = "";
                allData.section_a_grade = "";
                allData.section_a_description = "";
                allData.section_b_grade = "";
                allData.section_b_description = "";
                allData.section_c_grade = "";
                allData.section_c_description = "";
                allData.action = "";

            }
        });
        }


        function saveData(event) {

            statusMessage.innerHTML = "";
            var target = event.currentTarget;
            //var parent = target.parentElement.nodeName;
            //console.log(target.getAttribute('data-id'));
            allData.fileuserdata = target.getAttribute('data-id');

            //console.log(target.getAttribute('data-link'));
            let dataLink = target.getAttribute('data-link');

            const request = new XMLHttpRequest();
            request.open('POST', `${document.location.origin}/${dataLink}`);

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


            } else {
                spinner.remove();
                statusMessage.textContent = message.failure;
            }
        });
        }

        close.addEventListener("click", () => {
            statusMessage.innerHTML = "";
        });

    </script>
    <?php
    get_footer();
} 

