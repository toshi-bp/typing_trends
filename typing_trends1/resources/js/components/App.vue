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

export default {
  name: 'App',
  data () {
    return {
      //wordsはjsonファイルをObject型に変換して利用するためあくまでテスト用
      words: ['apple', 'banana', 'grape'],
      word: '',
      pressed: '',
      miss: 0,
      playing: false,
    }
  },
  created () {
    addEventListener('keydown', (e) =>{
      if(e.key !== ' ' || this.playing){
        return
      }
      this.playing = true
      this.setWord()
      this.keyDown()
    })
  },
  methods: {
    setWord() {
      this.word = this.words.splice(Math.floor(Math.random() * this.words.length), 1)[0]
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
          if(this.words.length === 0){
            this.word = 'Completed!'
            return
          }
          this.setWord()
        }
      })
    }
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
