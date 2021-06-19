<template>
  <div>
    <div v-if="playing">
      <p>
        <span>{{ pressed }}</span>{{ word }}
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
          this.miss++
          return
        }
        this.pressed += e.key
        this.word = this.word.slice(1)
        if(this.word.length === 0){
          this.pressed = ''
          if(this.trend_array.length === 0){
            this.word = 'Completed!'
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
  margin-top: 60px;
}
</style>
