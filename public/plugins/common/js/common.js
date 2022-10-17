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
