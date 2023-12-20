window.onload = function () {
    countPhrasesInLists()
}

function countPhrasesInLists() {
    document.getElementById('phrases').addEventListener('keyup', function () {
        let numberLineBreaksInFirstList = 0;
        let firstList = document.getElementById('phrases').value.split('\n');
        for (let i = 0; i < firstList.length; i++) {
            if (firstList[i] !== '') {
                numberLineBreaksInFirstList++
            }
        }
        document.getElementById('countPhrases').innerText = numberLineBreaksInFirstList
    });
}

function deleteItems() {
    let greaterOrEqual = Number($('#greaterOrEqual').val())
    let lessOrEqual = Number($('#lessOrEqual').val())
    document.querySelectorAll('.table-row').forEach((el) => {
        if (Number(el.children[3].innerText) >= greaterOrEqual && greaterOrEqual !== 0) {
            el.remove();
        }

        if (Number(el.children[3].innerText) <= lessOrEqual && lessOrEqual !== 0) {
            el.remove();
        }
    })
}

function createElementForCopyInformationInBuffer(text) {
    let area = document.createElement('textarea');
    area.style.opasity = 0
    document.body.appendChild(area);
    area.value = text;
    area.select();
    document.execCommand("copy");
    document.body.removeChild(area);
    showSuccessCopyMessage()
}

function calculatePercentTableGeneration(length) {
    return 100 / length
}

function createTextArea(key, value) {
    let textarea = document.createElement('textarea')
    textarea.name = 'keyPhrases'
    textarea.className = 'form form-control'
    let str = value.keyPhrases.toString()
    textarea.innerHTML = str.replace(/,/g, '\n\r')
    textarea.rows = 6
    textarea.id = 'unique-words-textarea-' + key

    return textarea
}

function savePhrasesInBuffer(key) {
    document.getElementById('unique-words-textarea-' + key).select();
    document.execCommand('copy');
    showSuccessCopyMessage()
}

function createKeyPhrases(key, value) {
    let divForSquareIcons = document.createElement('div')
    let div = document.createElement('div')
    let divForIcons = document.createElement('div')
    let parentDiv = document.createElement('div')

    parentDiv.id = 'parent-' + key
    parentDiv.className = 'parent-div'
    divForSquareIcons.appendChild(createPlusSquare(key, value))
    divForSquareIcons.appendChild(createMinusSquare(key))
    div.className = 'd-flex flex-column'
    divForIcons.className = 'mb-2'
    divForIcons.appendChild(createClipboardIcon(key))
    divForIcons.appendChild(createDownloadIcon(key))
    div.appendChild(divForSquareIcons)
    parentDiv.appendChild(divForIcons)
    parentDiv.appendChild(createTextArea(key, value))
    div.appendChild(parentDiv)

    return div
}

function createPlusSquare(key, value) {
    let iPlus = document.createElement('i')
    iPlus.innerText = '  ' + value.keyPhrases[0].slice(0, 30)
    iPlus.className = 'fa fa-plus-square-o'
    iPlus.id = 'plus-' + key
    iPlus.onclick = function () {
        $('#minus-' + key).show()
        $('#parent-' + key).show()
        $('#plus-' + key).hide()
    }

    return iPlus
}

function createMinusSquare(key) {
    let iMinus = document.createElement('i')
    iMinus.className = 'fa fa-minus-square-o'
    iMinus.id = 'minus-' + key
    iMinus.onclick = function () {
        $('#minus-' + key).hide()
        $('#parent-' + key).hide()
        $('#plus-' + key).show()
    }

    return iMinus
}

function createRow(key, value) {
    let tbody = document.getElementById('result-table').getElementsByTagName('tbody')[0];
    let row = document.createElement("tr")
    row.id = 'unique-words-id-' + key
    row.className = 'table-row'
    let td1 = document.createElement('td')
    let td2 = document.createElement('td')
    let td3 = document.createElement('td')
    let td4 = document.createElement('td')
    let td5 = document.createElement('td')
    td5.id = 'unique-words-td-id-' + key
    let icon = document.createElement('i')
    if (value.numberOccurrences >= 2) {
        let div = createKeyPhrases(key, value)
        td5.appendChild(div)
    } else {
        td5.appendChild(document.createTextNode(value.keyPhrases[0]))
    }

    icon.onclick = function () {
        $('#unique-words-id-' + key).remove()
    }
    icon.className = 'fa fa-trash click_tracking'
    icon.setAttribute('data-click', 'Remove word')
    td1.appendChild(icon)
    td2.appendChild(document.createTextNode(value.word))
    td3.appendChild(document.createTextNode(value.wordForms))
    td4.appendChild(document.createTextNode(value.numberOccurrences))
    row.appendChild(td1);
    row.appendChild(td2);
    row.appendChild(td3);
    row.appendChild(td4);
    row.appendChild(td5);
    tbody.appendChild(row);
}

function setProgressBarStyles(percent) {
    $('.progress-bar').css({
        width: percent + '%'
    })
    document.querySelector('.progress-bar').innerText = percent + '%'
}

function showSuccessCopyMessage() {
    $('.success-message').show(300)
    setTimeout(() => {
        $('.success-message').hide(300)
    }, 5000)
}
