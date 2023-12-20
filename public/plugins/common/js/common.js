String.prototype.shuffle = function () {
    let a = this.split(""),
        n = a.length;

    for (let i = n - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        let tmp = a[i];
        a[i] = a[j];
        a[j] = tmp;
    }
    return a.join("").replaceAll(" ", "");
}

function eventChangeList(blockIn, blockOut, secondBlockOut = false, customFunction = false) {
    blockIn.on('keyup paste', function () {
        let numberLineBreaksInFirstList = 0;
        let list = blockIn.val().split('\n');

        for (let i = 0; i < list.length; i++) {
            if (list[i] !== '') {
                numberLineBreaksInFirstList++
            }
        }

        blockOut.html(numberLineBreaksInFirstList)

        if (secondBlockOut !== false) {
            secondBlockOut.html(numberLineBreaksInFirstList)
        }

        if (customFunction !== false) {
            let count = customFunction()
            count *= numberLineBreaksInFirstList
            $('#loss-limits').html(count)
        }
    })
}
