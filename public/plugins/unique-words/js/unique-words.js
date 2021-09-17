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

function saveInBuffer() {
    var text = ''
    document.querySelectorAll('.table-row').forEach((el) => {
        if (document.getElementById('unique-word').checked) {
            text += el.children[1].innerText + ';'
        }
        if (document.getElementById('unique-word-forms').checked) {
            text += el.children[2].innerText + ';'
        }
        if (document.getElementById('number-occurrences').checked) {
            text += el.children[3].innerText + ';'
        }
        if (document.getElementById('key-phrases').checked) {
            console.log('----------')
            console.log(el.children[4])
            console.log('----------')
            // let textarea = el.children[4].querySelector('.unique-element-key-phrases').innerHTML.trim()
            // textarea = textarea.split('\n')
            // for (let i = 0; i < textarea.length; i++) {
            //     text += textarea[i] + '\n;;;;'
            // }
        }
        text += '\n'
    })
    createElementForCopyInformationInBuffer(text)
}

function createElementForCopyInformationInBuffer(text) {
    let copyText = document.createElement('textarea');
    document.body.appendChild(copyText);
    copyText.value = text;
    copyText.select();
    document.execCommand('copy');
    document.body.removeChild(copyText);
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
    $('.success-message').show(300)
    setTimeout(() => {
        $('.success-message').hide(300)
    }, 5000)
}

function createKeyPhrases(key, value) {
    let div = document.createElement('div')
    div.className = 'd-flex flex-column'
    let divForIcons = document.createElement('div')
    divForIcons.className = 'mb-2'
    divForIcons.appendChild(createClipboardIcon(key))
    divForIcons.appendChild(createDownloadIcon(key))
    div.appendChild(divForIcons)
    div.appendChild(createTextArea(key, value))
    return div
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
    let icon = document.createElement('i')
    if (value.numberOccurrences >= 2) {
        let div = createKeyPhrases(key, value)
        td5.appendChild(div)
    } else {
        td5.appendChild(document.createTextNode(value.keyPhrases))
    }

    icon.onclick = function () {
        $('#unique-words-id-' + key).remove()
    }
    icon.className = 'fa fa-trash'
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
