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
                <div class="form-group">
                    <input type="text" class="form-control" :placeholder="start" v-model="removeStart">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" :placeholder="end" v-model="removeEnd">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input type="submit" id="start" class="btn btn-secondary" name="delete" :value="submit">
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
                text: '',
                removeStart: '',
                removeEnd: '',
                checkbox: {
                    'left': [
                        {id: 1, name: 'removeExtraSpace', title: '', selected: true},
                        {id: 2, name: 'trim', title: '', selected: true},
                        {id: 3, name: 'replaceTabWithSpace', title: '', selected: true},
                        {id: 4, name: 'removeEmptyRows', title: '', selected: true},
                        {id: 5, name: 'lowerCase', title: '', selected: true},
                    ],
                    'right': [
                        {id: 6, name: 'removeStartingChars', title: '', selected: false},
                        {id: 7, name: 'removeEndingChars', title: '', selected: false},
                        {id: 8, name: 'removeDuplicates', title: '', selected: true},
                        {id: 9, name: 'replaceUmlaut', title: '', selected: true},
                    ],
                },
            }
        },
        created(){
            let app = this;
            let array = _.concat(this.checkbox.left, this.checkbox.right);
            _.forEach(array, function(data) {
                data.title = app.names[data.id];
            });
        },
        methods: {
            CalculateDuplicates(){
                var app = this;

                let array = _.concat(this.checkbox.left, this.checkbox.right);
                let options = _.filter(array, function(n) {
                    return n.selected;
                });

                _.forEach(options, function(data) {
                    app[data.name]();
                });
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


