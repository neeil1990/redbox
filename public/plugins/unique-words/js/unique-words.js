window.onload = function () {
    countPhrasesInLists()
    calculatePhrasesInFistList()
    removeExtraSpaces()
    goUp()
}

function countPhrasesInLists() {
    document.getElementById('phrases').addEventListener('keyup', function () {
        calculatePhrasesInFistList();
    });
}

function calculatePhrasesInFistList() {
    let numberLineBreaksInFirstList = 0;
    let firstList = document.getElementById('phrases').value.split('\n');
    for (let i = 0; i < firstList.length; i++) {
        if (firstList[i] !== '') {
            numberLineBreaksInFirstList++
        }
    }
    document.getElementById('countPhrases').innerText = numberLineBreaksInFirstList
}

function deleteItem(id) {
    document.getElementById('unique-words-id-' + id).remove();
    document.getElementById('extraId').value += id + ' ';
}

function deleteItems() {
    let greaterOrEqual = document.getElementById('greaterOrEqual').value
    let lessOrEqual = document.getElementById('lessOrEqual').value

    document.querySelectorAll('.unique-result').forEach((el) => {
        if (el.children[3].innerText >= greaterOrEqual && greaterOrEqual != '') {
            el.remove();
        }

        if (el.children[3].innerText <= lessOrEqual && lessOrEqual != '') {
            el.remove();
        }
    })
}

function saveInBuffer() {
    var text = ''
    document.querySelectorAll('.unique-result').forEach((el) => {
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
            let textarea = el.children[4].querySelector('.unique-element-key-phrases').innerHTML.trim()
            textarea = textarea.split('\n')
            for (let i = 0; i < textarea.length; i++) {
                text += textarea[i] + '\n;;;;'
            }
        }
        text += '\n'
    })
    createElementForCopyInformationInBuffer(text)
}

function savePhrasesInBuffer(id) {
    let text = document.getElementById('key-phrases-' + id).value
    createElementForCopyInformationInBuffer(text.trim())
}

function createElementForCopyInformationInBuffer(text) {
    let copyText = document.createElement('textarea');
    document.body.appendChild(copyText);
    copyText.value = text;
    copyText.select();
    document.execCommand('copy');
    document.body.removeChild(copyText);
}

function removeExtraSpaces() {
    document.querySelectorAll('.key-phrases-result').forEach((el) => {
        el.innerHTML = el.innerHTML.trim()
    });
}

function goUp() {
    let top = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
    if (top > 0) {
        window.scrollBy(0, -100);
    }
}

function showForm(id) {
    document.getElementById('unique-form' + id).style.display = 'block'
    document.getElementById('unique-minus' + id).style.display = 'block'
    document.getElementById('unique-plus' + id).style.display = 'none'
    document.getElementById('unique-span' + id).style.display = 'none'
}

function hiddenForm(id){
    document.getElementById('unique-form' + id).style.display = 'none'
    document.getElementById('unique-minus' + id).style.display = 'none'
    document.getElementById('unique-plus' + id).style.display = 'block'
    document.getElementById('unique-span' + id).style.display = 'block'
}
