<template>

    <div class="flex">

        <div class="w-3/4">
            <h3 class="bg-yellow p-2 text-3xl">
                It is currently <strong>{{ this.values[this.turn] }}'s</strong> turn.
            </h3>

            <div class="bg-green flex p-2 text-white text-5xl" v-if="whoWon !== 'NOBODY' || plays == 9">
                <p class="p-2">The winner is <strong>{{ whoWon }}</strong>!</p>

                <button class="bg-blue ml-2 p-2 rounded text-white" v-if="allow_refresh" @click.prevent="reset">Start another game.</button>
            </div>

            <div>
                <div class="flex">
                    <button class="select-button" :disabled="disable" @click.prevent="select('a', 'a')" v-text="selections.a.a"></button>
                    <button class="select-button" :disabled="disable" @click.prevent="select('a', 'b')" v-text="selections.a.b"></button>
                    <button class="select-button" :disabled="disable" @click.prevent="select('a', 'c')" v-text="selections.a.c"></button>
                </div>
                <div class="flex">
                    <button class="select-button" :disabled="disable" @click.prevent="select('b', 'a')" v-text="selections.b.a"></button>
                    <button class="select-button" :disabled="disable" @click.prevent="select('b', 'b')" v-text="selections.b.b"></button>
                    <button class="select-button" :disabled="disable" @click.prevent="select('b', 'c')" v-text="selections.b.c"></button>
                </div>
                <div class="flex">
                    <button class="select-button" :disabled="disable" @click="select('c', 'a')" v-text="selections.c.a"></button>
                    <button class="select-button" :disabled="disable" @click="select('c', 'b')" v-text="selections.c.b"></button>
                    <button class="select-button" :disabled="disable" @click="select('c', 'c')" v-text="selections.c.c"></button>
                </div>
            </div>
        </div>

        <div class="ml-4 mr-4 w-1/4">
            <h3 class="bg-blue p-2 text-3xl text-white">
                Leader Board
            </h3>
            <div class="bg-white border p-4">

                <div v-for="(wins, player) in history">

                    <h1>{{ player == values[im] ? 'You' : 'Them' }} : {{ wins }}</h1>

                </div>

            </div>
        </div>

    </div>




</template>

<script>
    export default {

        props: {

            history_url : {
                type : Object,
                required : true,
            },

            store_url : {
                type : Object,
                required : true
            }
        },

        data() {
            return {

                allow_refresh : false,

                im : 'a',

                turn : 'a',

                history : [],

                values : {
                    a : 'X',
                    b : 'O'
                },

                plays : 0,

                selections : {
                    a : {
                        a : null,
                        b : null,
                        c : null
                    },
                    b : {
                        a : null,
                        b : null,
                        c : null
                    },
                    c : {
                        a : null,
                        b : null,
                        c : null
                    }
                },
            }
        },

        created() {

            this.fetchHistory()

        },

        methods: {

            assignWinner(winner)
            {
                this.superAxios(this.store_url, { winner : winner }, {}, (response)=>{

                    this.allow_refresh = true;

                }, (error)=>{
                    alert('oops')
                })
            },

            fetchHistory()
            {
                this.superAxios(this.history_url, {}, {}, (response)=>{
                    this.history = response.data;
                })
            },

            makeRandomSelection()
            {
                var options = [
                    { key : 'a.a', value : this.selections.a.a },
                    { key : 'a.b', value : this.selections.a.b },
                    { key : 'a.c', value : this.selections.a.c },
                    { key : 'b.a', value : this.selections.b.a },
                    { key : 'b.b', value : this.selections.b.b },
                    { key : 'b.c', value : this.selections.b.c },
                    { key : 'c.a', value : this.selections.c.a },
                    { key : 'c.b', value : this.selections.c.b },
                    { key : 'c.c', value : this.selections.c.c },
                ];

                options = _.filter(options, { value : null})
                options = _.map(options, 'key')

                if(options.length)
                {
                    var option = _.sample(options);

                    var split  = option.split('.');
                    var row    = split[0]
                    var column = split[1]

                    this.selections[row][column] = this.values[this.turn]
                }
            },

            reset : function(){

                Object.assign(this.$data, this.$options.data())

                this.fetchHistory()

            },

            select : function(row, column)
            {
                var setXorO = (this.turn == 'a' && this.im == 'a')
                            ? this.values[this.im]
                            : this.values['b']

                Vue.set(this.selections[row], column, setXorO)
            },

            winnerFormula(player)
            {
                var myValue = player;

                var horizontal = (this.selections.a.a == myValue && this.selections.a.b == myValue && this.selections.a.c == myValue)
                               ||(this.selections.b.a == myValue && this.selections.b.b == myValue && this.selections.b.c == myValue)
                               ||(this.selections.c.a == myValue && this.selections.c.b == myValue && this.selections.c.c == myValue)


                var vertical = (this.selections.a.a == myValue && this.selections.b.a == myValue && this.selections.c.a == myValue)
                            || (this.selections.a.b == myValue && this.selections.b.b == myValue && this.selections.c.b == myValue)
                            || (this.selections.a.c == myValue && this.selections.b.c == myValue && this.selections.c.c == myValue)


                var diagonal = (this.selections.a.a == myValue && this.selections.b.b == myValue && this.selections.c.c == myValue)
                            || (this.selections.c.a == myValue && this.selections.b.b == myValue && this.selections.a.c == myValue)

                return (horizontal || vertical || diagonal) ? player : false;
            }

        },

        computed : {

            disable()
            {
                return this.whoWon !== 'NOBODY' || this.plays == 9;
            },

            imTheWInner()
            {
                return this.winnerFormula(this.values[this.im])
            },

            theyAreTheWInner()
            {
                return this.winnerFormula(this.values[this.im == 'a' ? 'b' : 'a'])
            },

            whoWon()
            {
                var theWinnerIs = this.imTheWInner ? this.imTheWInner : this.theyAreTheWInner;

                if(theWinnerIs || this.plays == 9)
                {
                    if(! theWinnerIs)
                    {
                        theWinnerIs = 'NOBODY'
                    }

                    this.assignWinner(theWinnerIs)

                    return theWinnerIs;
                }

                return 'NOBODY'

            }

        },

        watch : {

            'selections' : {
                handler : function(newValue)
                {
                    this.plays++

                    if(this.turn == 'a')
                    {
                        this.turn = 'b';
                    }
                    else
                    {
                        this.turn = 'a';
                    }

                    if(this.turn !== this.im)
                    {
                        this.makeRandomSelection();
                    }
                },
                deep : true
            }
        },

    }
</script>

<style scoped>

    .select-button {
        @apply border h-16 w-1/3 text-5xl
    }


</style>