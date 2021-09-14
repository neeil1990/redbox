window.onload = function () {
    countPhrasesInLists()
    calculatePhrasesInFistList()
    calculatePhrasesInSecondList()
    if (localStorage.getItem('radioOptionState') !== null) {
        let index = localStorage.getItem('radioOptionState');
        document.getElementById(index + '-radio-option').checked = true;
    }
}

function saveOptionState(index) {
    localStorage.setItem('radioOptionState', index)
}

function saveOfBuffer() {
    document.getElementById('comparison-result').select();
    document.execCommand('copy');
    $('.success-message').show(300)
    setTimeout(() => {
        $('.success-message').hide(300)
    }, 3000)
}

function countPhrasesInLists() {
    document.getElementById('firstList').addEventListener('keyup', function () {
        calculatePhrasesInFistList();
    });
    document.getElementById('secondList').addEventListener('keyup', function () {
        calculatePhrasesInSecondList()
    });
    document.getElementById('comparison-result').addEventListener('keyup', function () {
        comparisonResult()
    });
}

function calculatePhrasesInFistList() {
    let numberLineBreaksInFirstList = 0;
    let firstList = document.getElementById('firstList').value.split('\n');
    for (let i = 0; i < firstList.length; i++) {
        if (firstList[i] !== '') {
            numberLineBreaksInFirstList++
        }
    }

    document.getElementById('firstPhrases').innerText = numberLineBreaksInFirstList
}

function calculatePhrasesInSecondList() {
    let numberLineBreaksInSecondList = 0;
    let secondList = document.getElementById('secondList').value.split('\n');
    for (let i = 0; i < secondList.length; i++) {
        if (secondList[i] !== '') {
            numberLineBreaksInSecondList++
        }
    }

    document.getElementById('secondPhrases').innerText = numberLineBreaksInSecondList
}

function comparisonResult() {
    let numberLineBreaksInResultList = 0;
    let resultList = document.getElementById('comparison-result').value.split('\n');
    for (let i = 0; i < resultList.length; i++) {
        if (resultList[i] !== '') {
            numberLineBreaksInResultList++
        }
    }

    document.getElementById('numberPhrasesInResult').innerText = numberLineBreaksInResultList
}

$('.result-form').hide()
$('.error-message').hide()
$('.success-message').hide()
