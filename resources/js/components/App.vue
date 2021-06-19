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
    chouon: Romanizer.CHOUON_SKIP
});

export default {
  name: 'App',
  data () {
    return {
      //wordsはjsonファイルをObject型に変換して利用するためあくまでテスト用
      words: ['apple', 'banana', 'grape'],
      trend_array: [],
      trends: [],
      converted_text: '',
      word: '',
      pressed: '',
      miss: 0,
      playing: false,
    }
  },
  mounted () {
      axios.get("./trends.json")
      .then(response => {
        this.trends = response.data[0].trends
     })
      .catch(function(error) {
            console.log('取得に失敗しました。', error);
        })
  },
  created () {
    addEventListener('keydown', (e) =>{
      let trend = ""
      if(e.key !== ' ' || this.playing){
        return
      }
      console.log(this.trends)
      for (let i = 0; i < this.trends.length; i++) {
        //console.log(this.convertKana(this.trends[i].name))
        console.log("入力値:" + this.trends[i].name)
        trend = this.convertKana(this.trends[i].name)
        console.log("配列に格納される値:" + trend)
        this.trend_array.push(r.romanize(trend))
     }
      console.log(this.trend_array)
      this.playing = true
      this.setWord()
      this.keyDown()
    })
  },
  methods: {
    convertKana (text) {
        //漢字からひらがなに変換するための関数
        this.converted_text = axios.post("https://labs.goo.ne.jp/api/hiragana", {
            app_id: "e11526019e0fe1a677884c525948a7aac1fc66516e192a5311c50275bdaaad66",
            sentence: text.toString(),
            output_type: "hiragana"
        })
        .then(response => {
            response.data.converted
            console.log("api叩いた結果:" + response.data.converted)
        }
        )
        return this.converted_text
    },
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
            this.word = 'Completed! ミスの回数は'+this.miss +'回だったよ！'
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

