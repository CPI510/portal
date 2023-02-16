
<style>
    .field__wrapper {
        width: 100%;
        position: relative;
        margin: 15px 0;
        text-align: center;
    }

    .field__file {
        opacity: 0;
        visibility: hidden;
        position: absolute;
    }

    .field__file-wrapper {
        width: 100%;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }

    .field__file-fake {
        height: 60px;
        width: calc(100% - 130px);
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        padding: 0 15px;
        border: 1px solid #c7c7c7;
        border-radius: 3px 0 0 3px;
        border-right: none;
    }

    .field__file-button {
        width: 130px;
        height: 60px;
        background: #1bbc9b;
        color: #fff;
        font-size: 1.125rem;
        font-weight: 700;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        border-radius: 0 3px 3px 0;
        cursor: pointer;
    }
</style>
<div class="field__wrapper">

    <input name="file" type="file" name="file" id="field__file-2" class="field field__file" multiple>

    <label class="field__file-wrapper" for="field__file-2">
        <div class="field__file-fake">Файл не выбран</div>
        <div class="field__file-button">Выбрать</div>
    </label>

</div>

<script>
    let fields = document.querySelectorAll('.field__file');
    Array.prototype.forEach.call(fields, function (input) {
        let label = input.nextElementSibling,
            labelVal = label.querySelector('.field__file-fake').innerText;

        input.addEventListener('change', function (e) {
            let countFiles = '';
            if (this.files && this.files.length >= 1)
                countFiles = this.files.length;

            if (countFiles)
                label.querySelector('.field__file-fake').innerText = 'Выбрано файлов: ' + countFiles;
            else
                label.querySelector('.field__file-fake').innerText = labelVal;
        });
    });
</script>
