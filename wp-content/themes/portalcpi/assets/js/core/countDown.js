// в примере используется текущая дата + 24 часа
// дату можно указывать просто '2018-10-20 20:00'
//const end = '<?= getCourse($_GET['group_id'], 'end_date') ?>';


function countDown(end,display){

    const endDate = calcTime(new Date(end)),
        newDate = calcTime(new Date());
        //display = document.querySelector('#display');

    setInterval(() => {
        const result = getTimeRemaining(endDate)
        let formated = ''

        for (let part in result)
    formated += `${result[part]} ${part}, `
// const formated = Object.values(result).join(':')

    //let newDate = new Date().toISOString().
    // replace(/T/, ' ').
    // replace(/\..+/, '');

    if(newDate > endDate){
    display.innerHTML = '--'
}else{
    display.innerHTML = formated.slice(0, -2)
    // console.log(end);
    // console.log(newDate);
}


}, 500)
}



function getTimeRemaining(endtime){
    const t = endtime - calcTime()
    let obj = {}
    if( document.querySelector('#kz').value == 1 ){
        obj = {
            'күндер': t / (1000 * 60 * 60 * 24) | 0,
            'cағ.': t / (1000 * 60 * 60) % 24 | 0,
            'мин.': t / 1000 / 60 % 60 | 0,
            'сек.': t / 1000 % 60 | 0
        }
    }else{
        obj = {
            'дней': t / (1000 * 60 * 60 * 24) | 0,
            'ч.': t / (1000 * 60 * 60) % 24 | 0,
            'мин.': t / 1000 / 60 % 60 | 0,
            'сек.': t / 1000 % 60 | 0
        }
    }


    for (let key in obj)
        obj[key] = ('0' + obj[key]).slice(-2)

    return obj
}

// дата с нужным смещением
function calcTime(d = new Date(), offset = 3) {
    utc = d.getTime() + (d.getTimezoneOffset() * 60000)

    nd = new Date(utc + (3600000 * offset))

    return nd
}