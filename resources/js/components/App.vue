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
  //おそらくaxiosは非同期通信でこの通信が終わっているという保証がないとobserver型で帰ってしまう。
  async mounted () {

    await axios.get("./trends.json")
    .then(response => {
        this.trends = response.data[0].trends
    })
    .catch(function(error) {
        console.log('取得に失敗しました。', error);
    })
    //ここだとaxios.getが　確実に終わってから実行できる
    for (let i = 0; i < 2; i++) {
        this.convertKana(this.trends[i].name)
     }

  },
  created () {
    addEventListener('keydown', (e) =>{
      let trend = ""
      if(e.key !== ' ' || this.playing){
        return
      }

    //  console.log("trendsara",this.trend_array)

    this.playing = true

    //observerで帰ってきた時に強制的に配列にするおまじない
    this.trend_array = JSON.parse(JSON.stringify(this.trend_array))
    this.setWord()
    this.keyDown()

    })
  },
  methods: {
       convertKana (text) {
        //漢字からひらがなに変換するための関数
         axios.post("https://labs.goo.ne.jp/api/hiragana", {
            app_id: "de47d0f2e0fcbd301c3e1a51ad66c025f994b1f1e1a6fcbc3eb534388516cc96",
            sentence: text.toString(),
            output_type: "hiragana"
        })
        .then(response => {
            this.converted_text = response.data.converted
            this.trend_array.push(r.romanize(this.converted_text))

            console.log("api叩いた結果:" + this.converted_text)
        }
        )

    },

    setWord() {
        this.word = this.trend_array.splice(Math.floor(Math.random() * this.trend_array.length), 1)[0]
    },
    keyDown(){
      addEventListener('keydown', (e) => {


        //打ち間違えてしまったときの処理
        if(e.key !== this.word[0]){
            if(e.shiftKey)return
          //comleteしている状態だったらカウント増やさない。
          if(this.trends.length === 0)
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
          if(this.trend_array.length === 0){

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

