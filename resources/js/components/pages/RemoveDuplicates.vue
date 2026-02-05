<template>
    <form @submit.prevent="CalculateDuplicates">
        <div class="form-group">
            <textarea type="text" class="form-control" rows="10" v-model="text"></textarea>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <Base-checkbox v-for="item in checkbox.left" :key="item.id" :data="item"></Base-checkbox>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <Base-checkbox v-for="item in checkbox.right" :key="item.id" :data="item"></Base-checkbox>
                </div>
            </div>
            <div class="col-md-5">

                <div class="form-group mb-2">
                    <Base-checkbox :key="checkbox.rsc.id" :data="checkbox.rsc"></Base-checkbox>
                </div>
                <div class="form-group mb-4">
                    <input type="text" class="form-control" :placeholder="start" v-model="removeStart">
                </div>

                <div class="form-group mb-2">
                    <Base-checkbox :key="checkbox.rec.id" :data="checkbox.rec"></Base-checkbox>
                </div>
                <div class="form-group mb-4">
                    <input type="text" class="form-control" :placeholder="end" v-model="removeEnd">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <input type="submit" id="start" class="btn btn-secondary mr-2" name="delete" :value="submit">
                <span class="text-muted">Было строк {{ before }} - Стало строк {{ now }} - Удалено дублей {{ deleted }}</span>
            </div>
        </div>
    </form>

</template>

<script>
    export default {
        name: "RemoveDuplicates",
        props:{
            names: {
                required : true,
                type: Object
            },
            start: {
                type: String
            },
            end: {
                type: String
            },
            submit: {
                type: String
            },
        },
        data(){
            return {
                before: 0,
                now: 0,
                deleted: 0,
                text: '',
                removeStart: '',
                removeEnd: '',
                checkbox: {
                    'left': [
                        {id: 1, name: 'removeExtraSpace', title: '', selected: false},
                        {id: 2, name: 'trim', title: '', selected: false},
                        {id: 3, name: 'replaceTabWithSpace', title: '', selected: false},
                        {id: 4, name: 'removeEmptyRows', title: '', selected: false},
                        {id: 5, name: 'lowerCase', title: '', selected: false},
                    ],
                    'right': [
                        {id: 8, name: 'removeDuplicates', title: '', selected: true},
                        {id: 9, name: 'replaceUmlaut', title: '', selected: false},
                    ],
                    'rsc' : {id: 6, name: 'removeStartingChars', title: '', selected: false},
                    'rec' : {id: 7, name: 'removeEndingChars', title: '', selected: false},
                },
            }
        },
        created(){
            let app = this;
            let array = _.concat(this.checkbox.left, this.checkbox.right, this.checkbox.rsc, this.checkbox.rec);

            _.forEach(array, function(data) {
                data.title = app.names[data.id];
            });
        },
        methods: {
            CalculateDuplicates(){
                var app = this;

                axios.get(`/duplicates/${app.text.length}`).then(function(response){

                    if(response.data.require){
                        alert(`Количество символов: ${response.data.quantity} Больше допустимого: ${response.data.require}`);
                        window.location.reload();
                    }
                });

                let array = _.concat(this.checkbox.left, this.checkbox.right, this.checkbox.rsc, this.checkbox.rec);

                let options = _.filter(array, function(n) {
                    return n.selected;
                });

                this.before = this.text.split(/[\r\n]+/).filter(line => line.trim() !== '').length;

                _.forEach(options, function(data) {
                    app[data.name]();
                });

                this.now = this.text.split(/[\r\n]+/).filter(line => line.trim() !== '').length;
                this.deleted = this.before - this.now;
            },
            removeExtraSpace(){
                this.text = _.replace(this.text, / +/gm, ' ');
            },
            trim(){
                this.text = _.join(_.map(this.text.split(/[\r\n]+/), _.trim), '\n');
            },
            replaceTabWithSpace(){
                this.text = _.replace(this.text, /[ \t]/gm, ' ');
            },
            removeEmptyRows(){
                this.text = _.replace(this.text, /^\n+/gm, '');
            },
            lowerCase(){
                let str = this.text;
                let lower = str.toLowerCase();
                this.text = lower;
            },
            removeStartingChars(){
                let regex = `^[${this.removeStart}]| [${this.removeStart}]+`;
                var re = new RegExp(regex, "gm");
                this.text = _.replace(this.text, re, ' ');
            },
            removeEndingChars(){
                let regex = `[${this.removeEnd}]+[ \t]|[${this.removeEnd}]$`;
                var re = new RegExp(regex, "gm");
                this.text = _.replace(this.text, re, ' ');
            },
            removeDuplicates(){
                let strings = this.text.split(/[\r\n]+/);
                let unique = [];
                top:for (var i in strings) {
                    for (var j in unique) {
                        if (strings[i] == unique[j]) {
                            continue top;
                        }
                    }
                    unique.push(strings[i]);
                }
                this.text = unique.join("\r\n");
            },
            replaceUmlaut(){
                this.text = _.replace(this.text, /ё/gm, 'е');
            }
        },
    }
</script>


