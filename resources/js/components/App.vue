<template>
  <div>
    <div id="app">
        <Example-component></Example-component>
    </div>
    <div v-if="playing">

      <p id="query">
            {{ pressed }}
            {{ word }}
      </p>

      <p>
        miss:{{ miss }}
      </p>


    </div>
    <div v-else>
      スペースボタンを押してください
    </div>
  </div>
</template>

<script>
import ExampleComponent from './ExampleComponent.vue'
//TwitterAPIから持ってきた単語をまとめたjsonファイルをインポートする
//import trends from './trends.json'
import axios from 'axios'
import Romanizer from 'js-hira-kata-romanize'
const r = new Romanizer({
    chouon: Romanizer.CHOUON_CIRCUMFLEX
});

export default {
  name: 'App',
  data () {
    return {
      //wordsはjsonファイルをObject型に変換して利用するためあくまでテスト用
      words: ['apple', 'banana', 'grape'],
      trend_array: [],
      trends: [],
      word: '',
      pressed: '',
      miss: 0,
      playing: false,
    }
  },
  mounted () {
      axios.get("./trends.json")
      .then(response => (this.trends = response.data[0].trends))
      .catch(function(error) {
            console.log('取得に失敗しました。', error);
        })
  },
  created () {
    addEventListener('keydown', (e) =>{
      if(e.key !== ' ' || this.playing){
        return
      }
      for (let i = 0; i < this.trends.length; i++) {
        this.trend_array.push(r.romanize(this.trends[i].name))
     }
      console.log(this.trend_array)
      this.playing = true
      this.setWord()
      this.keyDown()
    })
  },
  methods: {
    setWord() {
      this.word = this.trend_array.splice(Math.floor(Math.random() * this.trend_array.length), 1)[0]
    },
    keyDown(){
      addEventListener('keydown', (e) => {
        //打ち間違えてしまったときの処理
        if(e.key !== this.word[0]){
          //comleteしている状態だったらカウント増やさない。
          if(this.words.length === 0 ||this.word.length === 0)
          {
              return
          }
          this.miss++
          return
        }
        this.pressed += e.key
        this.word = this.word.slice(1)
        if(this.word.length === 0){
          this.pressed = ''
          if(this.words.length === 0){
            if(this.word)
            this.word = 'Completed!　ミスの回数は'+this.miss +'回だったよ！'
            this.miss="CLEAR"
            return
          }
          this.setWord()
        }
      })
    }
  },
  computed: {
  },
  components: {
    ExampleComponent
  }

}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin: 60px;

}
#query
{
    font-size: 30px;
    font-weight:bold
}
</style>

